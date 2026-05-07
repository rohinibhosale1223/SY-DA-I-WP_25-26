
<?php
$email = $_POST['email'];
$password = $_POST['password'];

if($email == "admin@gmail.com" && $password == "1234"){
    echo "Login Successful";
} else {
    echo "Invalid Login";
}
?>
