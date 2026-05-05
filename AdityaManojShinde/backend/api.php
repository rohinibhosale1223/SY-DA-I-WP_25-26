<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'db_connection.php';

// Get the HTTP method and action parameter
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

$response = ['success' => false, 'message' => 'Invalid Request'];

try {
    switch ($action) {
        // --- AUTHENTICATION ENDPOINTS ---
        case 'signup':
            if ($method === 'POST') {
                $name = $input['full_name'] ?? '';
                $email = $input['email'] ?? '';
                $password = $input['password'] ?? '';
                
                if (empty($name) || empty($email) || empty($password)) {
                    throw new Exception("All fields are required");
                }
                
                // Check if email exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Email already exists");
                }
                
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $email, $hash])) {
                    $response = ['success' => true, 'message' => 'User created successfully', 'user_id' => $pdo->lastInsertId()];
                }
            }
            break;

        case 'login':
            if ($method === 'POST') {
                $email = $input['email'] ?? '';
                $password = $input['password'] ?? '';
                
                if (empty($email) || empty($password)) {
                    throw new Exception("Email and password are required");
                }
                
                $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    $response = [
                        'success' => true, 
                        'message' => 'Login successful', 
                        'user' => ['id' => $user['id'], 'full_name' => $user['full_name'], 'email' => $email]
                    ];
                } else {
                    throw new Exception("Invalid email or password");
                }
            }
            break;

        // --- PRODUCT ENDPOINTS ---
        case 'products':
            if ($method === 'GET') {
                $id = isset($_GET['id']) ? intval($_GET['id']) : null;
                if ($id) {
                    // Get single product
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $product = $stmt->fetch();
                    if ($product) {
                        $response = ['success' => true, 'product' => $product];
                    } else {
                        throw new Exception("Product not found");
                    }
                } else {
                    // Get all products
                    $stmt = $pdo->query("SELECT * FROM products");
                    $products = $stmt->fetchAll();
                    $response = ['success' => true, 'products' => $products];
                }
            }
            break;

        // --- CART ENDPOINTS ---
        case 'cart':
            $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : (isset($input['user_id']) ? intval($input['user_id']) : null);
            
            if (!$user_id) {
                throw new Exception("User ID is required for cart operations");
            }

            if ($method === 'GET') {
                // Get user cart
                $stmt = $pdo->prepare("
                    SELECT c.id as cart_id, c.quantity, p.* 
                    FROM cart_items c 
                    JOIN products p ON c.product_id = p.id 
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$user_id]);
                $cart = $stmt->fetchAll();
                $response = ['success' => true, 'cart' => $cart];
                
            } elseif ($method === 'POST') {
                // Add item to cart
                $product_id = $input['product_id'] ?? null;
                $quantity = $input['quantity'] ?? 1;
                
                if (!$product_id) throw new Exception("Product ID is required");
                
                // Check if item already exists in cart
                $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$user_id, $product_id]);
                $existing = $stmt->fetch();
                
                if ($existing) {
                    // Update quantity
                    $new_qty = $existing['quantity'] + $quantity;
                    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
                    $stmt->execute([$new_qty, $existing['id']]);
                } else {
                    // Insert new item
                    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $product_id, $quantity]);
                }
                $response = ['success' => true, 'message' => 'Item added to cart'];
                
            } elseif ($method === 'PUT') {
                // Update quantity directly
                $cart_id = $input['cart_id'] ?? null;
                $quantity = $input['quantity'] ?? null;
                
                if (!$cart_id || $quantity === null) throw new Exception("Cart ID and quantity required");
                
                if ($quantity <= 0) {
                    // Remove if quantity is set to 0
                    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
                    $stmt->execute([$cart_id, $user_id]);
                    $response = ['success' => true, 'message' => 'Item removed from cart'];
                } else {
                    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$quantity, $cart_id, $user_id]);
                    $response = ['success' => true, 'message' => 'Cart updated'];
                }
                
            } elseif ($method === 'DELETE') {
                // Remove item from cart completely
                $cart_id = isset($_GET['cart_id']) ? intval($_GET['cart_id']) : null;
                if (!$cart_id) throw new Exception("Cart ID required");
                
                $stmt = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
                $stmt->execute([$cart_id, $user_id]);
                $response = ['success' => true, 'message' => 'Item removed from cart'];
            }
            break;
            
        default:
            throw new Exception("Unknown action: $action");
    }
} catch (Exception $e) {
    // Return 400 Bad Request on errors
    http_response_code(400);
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Output final JSON
echo json_encode($response);
?>
