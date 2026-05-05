<?php
$servername = "localhost";
$username = "root"; // Default username for XAMPP/WAMP
$password = "";     // Default password for XAMPP/WAMP is usually empty
$dbname = "aura_fragrance"; // The name of the database we created

// Create the connection using the MySQLi extension
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If it fails, stop the script and print the error
    die("Database Connection Failed: " . $conn->connect_error);
}

// Note: Do not echo "Connected successfully" here, 
// otherwise it will print at the top of every single web page!
?>