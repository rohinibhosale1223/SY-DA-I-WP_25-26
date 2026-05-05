<?php
session_start();
require 'db.php';
$session_id = session_id();

// Handle Remove Item
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM user_cart WHERE cart_id = $cart_id AND session_id = '$session_id'");
    header("Location: cart.php");
    exit();
}

// Fetch Cart Items
$cart_items = $conn->query("SELECT * FROM user_cart WHERE session_id = '$session_id'");
$grand_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Your Shopping Cart</h1>
        <nav><a href="index.php">HOME</a> | <a href="products.php">SHOP</a></nav>
    </header>

    <main class="auth-main">
        <div class="auth-card" style="max-width: 850px;">
            <h2>Order Summary</h2>
            <div class="table-responsive">
                <table class="luxury-table">
                    <thead>
                        <tr>
                            <th>Product Details</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($cart_items->num_rows > 0): ?>
                            <?php while($item = $cart_items->fetch_assoc()): 
                                $total = $item['price'] * $item['quantity'];
                                $grand_total += $total;
                            ?>
                            <tr>
                                <td><strong><?= $item['product_name'] ?></strong></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($total, 2) ?></td>
                                <td>
                                    <a href="cart.php?remove=<?= $item['cart_id'] ?>" class="btn-remove" style="text-decoration:none;">REMOVE</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center; padding: 30px;">Your cart is empty.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Grand Total:</strong></td>
                            <td colspan="2"><strong style="color: #d4af37; font-size: 1.3rem;">$<?= number_format($grand_total, 2) ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php if ($grand_total > 0): ?>
                <div style="text-align: right; margin-top: 30px;">
                    <a href="checkout.php" class="auth-btn" style="width: auto; padding: 12px 30px; display: inline-block; text-decoration: none;">GO TO CHECKOUT</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="script.js"></script>
</body>
</html>