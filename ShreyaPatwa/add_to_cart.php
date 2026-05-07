<?php

$conn = new mysqli("localhost", "root", "cheru", "gaming");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = trim($_POST['name']);
$price = intval($_POST['price']);

if (empty($name) || $price <= 0) {
    die("Invalid product data");
}

/*
Check if item already exists
*/
$check = "SELECT * FROM cart WHERE name='$name'";
$result = $conn->query($check);

if ($result->num_rows > 0) {

    // increase quantity
    $update = "UPDATE cart 
               SET quantity = quantity + 1 
               WHERE name='$name'";

    $conn->query($update);

} else {

    // insert new item
    $sql = "INSERT INTO cart(name, price, quantity)
            VALUES('$name', '$price', 1)";

    $conn->query($sql);
}

header("Location: cart.php");

?>