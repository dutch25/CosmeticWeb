<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="t2.css">
    <script>
        function validateForm() {
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;

            if (username == "" ) {
                alert('Please fill out username fields!');
                return false;
            }

            if (password == "") {
                alert('Please fill out password fields!');
                return false;
            }
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

        <!-- Bottom Header -->

    </header>

    <main>
        <div class="login-container">
            <h2>Login</h2>
            <form action="login1.php" method="POST" onsubmit = "return validateForm()">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" >

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" >

                <button type="submit">Login</button>
                <p>Don't have an account? <a href="register1.php">Sign Up</a></p>
            </form>
        </div>
    </main>
    
</body>
</html>

<?php
include "connect1.php";

$user_err = $pass_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);

    if (empty($user)) {
        $user_err = "Please enter username.";
    }
    if (empty($pass)) {
        $pass_err = "Please enter password.";
    }

    if (empty($user_err) && empty($pass_err)) {
        $sql = "SELECT userid, username, password FROM users WHERE username = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "s", $user);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if username exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $db_username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($pass, $hashed_password)) {
                            // Start a session and redirect to the main page
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $db_username;

                            header("location: homepage.php");
                            exit;
                        } else {
                            $pass_err = "Invalid password.";
                        }
                    }
                } else {
                    $user_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing the statement.";
        }
    }

    if (!empty($user_err)) {
        echo "<script>alert('$user_err');</script>";
    }
    if (!empty($pass_err)) {
        echo "<script>alert('$pass_err');</script>";
    }
}

mysqli_close($conn);
?>
