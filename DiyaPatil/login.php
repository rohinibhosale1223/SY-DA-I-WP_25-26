<?php
session_start();
require 'db.php'; // This connects to your MySQL database

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Capture and clean the email input to prevent SQL injection
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];

    // 1. Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 2. User exists! Fetch their data as an associative array
        $user = $result->fetch_assoc();
        
        // 3. Verify the typed password against the hashed password in the database
        if (password_verify($password, $user['password'])) {
            
            // Success! Create the session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_fname'] = $user['fname'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect to the home page with a welcome message
            echo "<script>
                    alert('Login successful! Welcome back, " . $user['fname'] . ".');
                    window.location.href='index.php';
                  </script>";
            exit();
            
        } else {
            // Password did not match
            echo "<script>alert('Error: Incorrect password. Please try again.'); window.history.back();</script>";
            exit();
        }
    } else {
        // Email was not found in the database
        echo "<script>alert('Error: No account found with that email. Please register first.'); window.history.back();</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Member Login</h1>
        <nav>
            <a href="index.php">HOME</a>
            <a href="register.php">REGISTER</a>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="login_email">Email Address:</label>
                    <input type="email" id="login_email" name="email" placeholder="Enter your registered email" required>
                </div>
                
                <div class="input-group">
                    <label for="login_password">Password:</label>
                    <input type="password" id="login_password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="auth-btn">SIGN IN</button>
            </form>
            
            <p class="auth-link">New to Aura? <a href="register.php">Create an account</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Aura Fragrances.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>