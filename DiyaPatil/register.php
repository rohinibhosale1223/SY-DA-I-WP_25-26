<?php
session_start();
require 'db.php'; // This connects to your database!

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and clean the inputs
    $fname = $conn->real_escape_string(trim($_POST['fname']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_pass = $_POST['confirm_pass'];

    // Validation: Check if passwords match
    if ($password !== $confirm_pass) {
        echo "<script>alert('Validation Error: Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Validation: Check if the email is already in the database
    $check_email = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        echo "<script>alert('Error: This email is already registered. Please log in.'); window.history.back();</script>";
        exit();
    } else {
        // Securely hash the password before saving it to the database
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // The SQL command to save the new user
        $sql = "INSERT INTO users (fname, email, password) VALUES ('$fname', '$email', '$hashed_password')";

        // Execute the command and redirect
        if ($conn->query($sql) === TRUE) {
            echo "<script>
                    alert('Registration successful! You have been saved to the database. You can now log in.');
                    window.location.href='login.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Database Error: " . $conn->error . "'); window.history.back();</script>";
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aura Fragrances | Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Join the Aura Society</h1>
        <nav>
            <a href="index.php">HOME</a>
            <a href="login.php">LOGIN</a>
        </nav>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h2>Create Your Account</h2>
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="fname">Full Name:</label>
                    <input type="text" id="fname" name="fname" placeholder="Enter your full name" required>
                </div>
                
                <div class="input-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" placeholder="name@example.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Create Password:</label>
                    <input type="password" id="password" name="password" placeholder="Minimum 8 characters" required>
                </div>
                
                <div class="input-group">
                    <label for="confirm_pass">Confirm Password:</label>
                    <input type="password" id="confirm_pass" name="confirm_pass" placeholder="Re-enter your password" required>
                </div>
                
                <button type="submit" class="auth-btn">REGISTER ACCOUNT</button>
            </form>
            
            <p class="auth-link">Already a member? <a href="login.php">Log in here</a>.</p>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Aura Fragrances.</p>
    </footer>
    <script src="script.js"></script>
</body>
</html>