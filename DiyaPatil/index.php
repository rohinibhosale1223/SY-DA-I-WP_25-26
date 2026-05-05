<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>AURA Luxury Fragrances</h1>
        <nav>
            <a href="index.php">HOME</a>
            <a href="products.php">SHOP</a>
            <a href="about.html">ABOUT US</a>
            <a href="contact.html">CONTACT</a>
            <a href="cart.php">CART</a>
            <?php if(isset($_SESSION['logged_in'])): ?>
                <a href="logout.php">LOGOUT</a>
            <?php else: ?>
                <a href="login.php">LOGIN</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="home-container">
        <section class="hero-banner">
            <h2>Discover Your Signature Scent</h2>
            <p>Explore our exclusive collection of luxury perfumes, masterfully crafted with the rarest botanical ingredients.</p>
            <a href="products.php"><button class="auth-btn" style="width: auto; padding: 15px 30px; margin-top: 20px;">Explore the Collection</button></a>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Aura Fragrances.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>