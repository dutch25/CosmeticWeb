<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="t2.css">
    <script>
        function validateForm() {
            var fullname = document.getElementById('fullname').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;
            var confirm_password = document.getElementById('confirm_password').value;

            if (fullname == "" ) {
                alert('Please fill out username fields!');
                return false;
            }

            if (email == "") {
                alert('Please fill out email fields!');
                return false;
            }

            if (password == "") {
                alert('Please fill out password fields!');
                return false;
            }


            if (password !== confirm_password) {
                alert('Passwords do not match!');
                return false;
            }

            return true; 
        }
    </script>
</head>
<body background="https://btec.fpt.edu.vn/wp-content/uploads/2024/08/banner-lon-2.jpg">
<header>
        <!-- Top Header -->
        <div class="header-top">
            <div class="left-section">
                <a href="#" class="account-link">
                    <span class="icon">👤</span> MY ACCOUNT
                </a>
            </div>

            <div class="center-section">
                <a href="homepage.php"><h1>D'Store</h1></a>
            </div>

            <div class="user-actions">
                <a href="login1.php">Sign In</a>
                <a href="#" class="heart-icon">♥</a>
                <a href="#" class="cart-icon">🛒</a>
            </div>
        </div>


    </header>

    <div class="login-container">
        <h2>Create an Account</h2>
        <form action="register1.php" method="POST" onsubmit = "return validateForm()">
            <label for="fullname">Username</label>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your username" >
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" >

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" >

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" >

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login1.php">Sign in</a></p>
    </div>

</body>
</html>


<?php
include "connect1.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["fullname"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $email = trim($_POST["email"]);

    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        echo "<script>alert('All fields are required!');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Check if the username already exists
    $check_username_sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_username_sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Username already exists!');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error preparing username check statement: " . mysqli_error($conn) . "');</script>";
        exit;
    }

    // Check if the email already exists
    $check_email_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email_sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Email is already in use!');</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error preparing email check statement: " . mysqli_error($conn) . "');</script>";
        exit;
    }

    // Insert new user into the database
    $insert_sql = "INSERT INTO users (username, password, email, Role_ID) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    if ($stmt) {
        $role_id = 1; // Regular user
        mysqli_stmt_bind_param($stmt, "sssi", $username, password_hash($password, PASSWORD_DEFAULT), $email, $role_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Registration successful!');</script>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login1.php'; 
                    }, 1);
                  </script>";
        } else {
            echo "<script>alert('Error during registration: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Error preparing insert statement: " . mysqli_error($conn) . "');</script>";
    }

    mysqli_close($conn);
}
?>
