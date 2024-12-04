<?php
session_start(); // Start the session

// Destroy the session and clear session data
$_SESSION = array();
session_destroy();

// Redirect back to the homepage
header("location: homepage.php");
exit;
?>
