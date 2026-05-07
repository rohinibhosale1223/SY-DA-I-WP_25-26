<?php
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

if(empty($name) || empty($email) || empty($password)){
    echo "All fields required";
} else {
    echo "Registration Successful";
}
?>
