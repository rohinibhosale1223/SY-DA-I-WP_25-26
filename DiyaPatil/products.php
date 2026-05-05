<?php
session_start();
require 'db.php'; // Connects to your database

// Handle the "Add to Cart" button click
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $session_id = session_id();
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $price = (float)$_POST['price'];

    // Check if item already exists in the cart for this session
    $check_sql = "SELECT * FROM user_cart WHERE session_id = '$session_id' AND product_name = '$product_name'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // If it's already there, just add 1 to the quantity
        $conn->query("UPDATE user_cart SET quantity = quantity + 1 WHERE session_id = '$session_id' AND product_name = '$product_name'");
    } else {
        // If it's not there, insert it as a new row
        $conn->query("INSERT INTO user_cart (session_id, product_name, price, quantity) VALUES ('$session_id', '$product_name', $price, 1)");
    }
    
    // Refresh the page so the user can keep shopping
    echo "<script>alert('$product_name added to your cart!'); window.location.href='products.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Our Collection</h1>
        <nav>
            <a href="index.php">HOME</a>
            <a href="cart.php">VIEW CART</a>
        </nav>
    </header>

    <main>
        <section class="category-section">
            <h2 class="category-heading">Signature Perfumes</h2>
            
            <div class="product-grid">
                
                <div class="product-card">
                    <img src="images/midnight_rose.jpg" alt="Midnight Rose" class="product-image">
                    <h3 class="product-title">Midnight Rose</h3>
                    <p class="product-price">$125.00</p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_name" value="Midnight Rose">
                        <input type="hidden" name="price" value="125.00">
                        <button type="submit" name="add_to_cart" class="auth-btn">ADD TO CART</button>
                    </form>
                </div>

                <div class="product-card">
                    <img src="images/oceanic_wood.jpg" alt="Oceanic Wood" class="product-image">
                    <h3 class="product-title">Oceanic Wood</h3>
                    <p class="product-price">$95.00</p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_name" value="Oceanic Wood">
                        <input type="hidden" name="price" value="95.00">
                        <button type="submit" name="add_to_cart" class="auth-btn">ADD TO CART</button>
                    </form>
                </div>

                <div class="product-card">
                    <img src="images/vanilla_silk.jpg" alt="Vanilla Silk" class="product-image">
                    <h3 class="product-title">Vanilla Silk</h3>
                    <p class="product-price">$110.00</p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_name" value="Vanilla Silk">
                        <input type="hidden" name="price" value="110.00">
                        <button type="submit" name="add_to_cart" class="auth-btn">ADD TO CART</button>
                    </form>
                </div>

                <div class="product-card">
                    <img src="images/smoked_leather.jpg" alt="Smoked Leather" class="product-image">
                    <h3 class="product-title">Smoked Leather</h3>
                    <p class="product-price">$85.00</p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_name" value="Smoked Leather">
                        <input type="hidden" name="price" value="85.00">
                        <button type="submit" name="add_to_cart" class="auth-btn">ADD TO CART</button>
                    </form>
                </div>

            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2026 Aura Fragrances.</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>