<?php
$host = 'localhost';
$db = 'webs';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}
else{
    echo'';
}
?>
