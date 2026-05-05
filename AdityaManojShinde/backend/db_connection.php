<?php
$host = 'localhost'; // XAMPP default
$db_name = 'shoefy_ecommerce_site';
$username = 'aditya';
$password = '8767';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    
    // Set PDO error mode to exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch data as an associative array by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Uncomment the line below if you want to test the connection directly in the browser
    // echo "Connected successfully to $db_name";
} catch(PDOException $e) {
    // If the connection fails, stop execution and display the error
    die("Connection failed: " . $e->getMessage());
}
?>
