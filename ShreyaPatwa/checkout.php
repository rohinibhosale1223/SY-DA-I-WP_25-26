<?php
$conn = new mysqli("localhost", "root", "cheru", "gaming");

$sql = "DELETE FROM cart";
$conn->query($sql);

echo "Checkout Successful!";
?>