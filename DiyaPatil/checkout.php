<?php
session_start();
require 'db.php'; 

// 1. Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    echo "<script>alert('Please log in to finalize your purchase.'); window.location.href='login.php';</script>";
    exit();
}

$session_id = session_id();
$user_email = $_SESSION['user_email'];

// 2. Fetch cart items and calculate the total amount
$cart_query = $conn->query("SELECT * FROM user_cart WHERE session_id = '$session_id'");
$grand_total = 0;

if ($cart_query->num_rows > 0) {
    while ($item = $cart_query->fetch_assoc()) {
        $grand_total += ($item['price'] * $item['quantity']);
    }
} else {
    echo "<script>alert('Your cart is empty!'); window.location.href='products.php';</script>";
    exit();
}

// 3. Process the checkout when the button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Using placeholders since address and phone are not manual inputs
    $placeholder_address = "Address on File";
    $placeholder_phone = "Contact on File";

    // Insert the order into the database
    $sql = "INSERT INTO orders (user_email, total_amount) 
            VALUES ('$user_email', $grand_total)";

    if ($conn->query($sql) === TRUE) {
        // Clear the cart for this session upon successful order registration
        $conn->query("DELETE FROM user_cart WHERE session_id = '$session_id'");
        
        echo "<script>
                alert('Order successfully registered for $user_email!');
                window.location.href='index.php';
              </script>";
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Checkout</h1>
        <nav><a href="cart.php">RETURN TO CART</a></nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h2>Order Review</h2>
            <div style="text-align: left; margin: 20px 0; border-top: 1px solid #eee; padding-top: 20px;">
                <p><strong>Account:</strong> <?= $user_email ?></p>
                <p><strong>Total Amount:</strong> <span style="color: #d4af37; font-weight: bold;">$<?= number_format($grand_total, 2) ?></span></p>
            </div>
            
            <form action="checkout.php" method="POST">
                <button type="submit" class="auth-btn">CONFIRM ORDER</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Aura Fragrances.</p>
    </footer>
</body>
</html>