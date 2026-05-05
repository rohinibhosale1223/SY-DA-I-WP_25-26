<?php
// backend/api.php
session_start();

// Set Headers for JSON API & CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // In production, restrict to your domain
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require 'db_connect.php';

// Retrieve JSON input if sent via fetch POST
$json_input = json_decode(file_get_contents('php://input'), true);

// Determine action
$action = $_GET['action'] ?? ($json_input['action'] ?? ($_POST['action'] ?? ''));

switch ($action) {
    case 'get_products':
        try {
            // Fetch products
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
            $products = $stmt->fetchAll();
            
            // Format for frontend compatibility (e.g., price with dollar sign)
            $formatted_products = array_map(function($p) {
                return [
                    'id' => $p['id'],
                    'name' => $p['name'],
                    'price' => '$' . number_format($p['price'], 2),
                    'image' => $p['image_url'],
                    'tag' => $p['tag']
                ];
            }, $products);

            echo json_encode(['success' => true, 'products' => $formatted_products]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Database error.']);
        }
        break;

    case 'register':
        $name = $json_input['full_name'] ?? '';
        $email = $json_input['email'] ?? '';
        $password = $json_input['password'] ?? '';

        if (!$name || !$email || !$password) {
            echo json_encode(['success' => false, 'error' => 'All fields are required.']);
            exit;
        }

        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'Email is already registered.']);
                exit;
            }

            // Hash password and insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            
            echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Registration failed.']);
        }
        break;

    case 'login':
        $email = $json_input['email'] ?? '';
        $password = $json_input['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                
                echo json_encode(['success' => true, 'message' => 'Login successful', 'user' => ['name' => $user['full_name']]]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Login failed.']);
        }
        break;

    case 'create_order':
        $shipping = $json_input['shipping'] ?? null;
        $cart = $json_input['cart'] ?? [];
        
        if (!$shipping || empty($cart)) {
            echo json_encode(['success' => false, 'error' => 'Invalid order data.']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Calculate total
            $total_amount = array_reduce($cart, function($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);

            // Insert order
            $user_id = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, full_name, address, city, state, zip, country, total_amount) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $shipping['full_name'],
                $shipping['address'],
                $shipping['city'],
                $shipping['state'],
                $shipping['zip'],
                $shipping['country'],
                $total_amount
            ]);
            
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $item_stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, size, quantity, price_at_purchase) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($cart as $item) {
                $item_stmt->execute([
                    $order_id,
                    $item['id'],
                    $item['size'],
                    $item['quantity'],
                    $item['price']
                ]);
                
                // Optional: Update stock here
                $update_stock = $pdo->prepare("UPDATE product_sizes SET stock = stock - ? WHERE product_id = ? AND size = ? AND stock >= ?");
                $update_stock->execute([$item['quantity'], $item['id'], $item['size'], $item['quantity']]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'order_id' => $order_id]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to process order.']);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid or missing action.']);
        break;
}
?>
