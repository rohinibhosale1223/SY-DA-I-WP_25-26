<?php
// backend/db_connect.php

$host = '127.0.0.1';
$db   = 'jersey_adda';
$user = 'root';
$pass = ''; // Blank password as requested
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Stop execution and show a generic error message if connection fails
    die("Database connection failed. Please ensure MySQL is running and the credentials are correct.");
}
?>
