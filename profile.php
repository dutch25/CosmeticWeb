<?php
session_start();
include "connect1.php"; // Include database connection

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login1.php");
    exit;
}

$user_id = $_SESSION["id"];

// Fetch user information from the database
$sql = "SELECT username, email, phone, fullname FROM users WHERE userid = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
    mysqli_stmt_execute($stmt); // Execute the query
    mysqli_stmt_bind_result($stmt, $username, $email, $phone, $fullname); // Bind the result variables
    mysqli_stmt_fetch($stmt); // Fetch the results
    mysqli_stmt_close($stmt); // Close the statement
} else {
    echo "Error preparing the SQL statement.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_fullname = $_POST['fullname'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            // If passwords don't match, display an alert and keep the user on the page
            echo "<script>alert('Passwords do not match.');</script>";
        } else {
            // Hash the new password
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update query with the new password
            $update_sql = "UPDATE users SET email = ?, phone = ?, fullname = ?, password = ? WHERE userid = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "ssssi", $new_email, $new_phone, $new_fullname, $new_password, $user_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    $_SESSION['username'] = $new_fullname; // Update session variable
                    echo "<script>alert('Profile updated successfully!');</script>";
                } else {
                    echo "<script>alert('Error updating profile.');</script>";
                }
                mysqli_stmt_close($update_stmt);
            }
        }
    } else {
        // If no new password is provided, just update email, phone, and fullname
        $update_sql = "UPDATE users SET email = ?, phone = ?, fullname = ? WHERE userid = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sssi", $new_email, $new_phone, $new_fullname, $user_id);
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['username'] = $new_fullname; // Update session variable
                echo "<script>alert('Profile updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating profile.');</script>";
            }
            mysqli_stmt_close($update_stmt);
        }
    }
}

mysqli_close($conn); // Close the database connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="t2.css">
</head>
<body>
    <header>
        <!-- Top Header -->
        <div class="header-top">
            <div class="left-section">
                <a href="profile.php" class="account-link">
                    <span class="icon">👤</span> 
                    Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
            </div>

            <div class="center-section">
                <a href="homepage.php"><h1>D'Store</h1></a>
            </div>

            <div class="user-actions">
                <a href="logout.php">Logout</a>
                <a href="#" class="heart-icon">♥</a>
                <a href="cart.php" class="cart-icon">🛒</a>
            </div>
        </div>
    </header>

    <main>
        <div class="profile-panel">
            <h2>User Profile</h2>
            
            <!-- Display Basic Information -->
            <div class="profile-section">
                <h3>Basic Information</h3>
                <div class="info-box">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($fullname); ?></p>
                </div>
            </div>

            <!-- Update Information Form -->
            <div class="profile-section">
                <h3>Update Information</h3>
                <div class="info-box">
                    <form method="POST">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

                        <label for="fullname">Full Name:</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>

                        <label for="new-password">New Password:</label>
                        <input type="password" id="new-password" name="new_password">

                        <label for="confirm-password">Confirm New Password:</label>
                        <input type="password" id="confirm-password" name="confirm_password">


                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer style="background-color: #005bb5; color: #fff; padding: 20px 0; font-size: 14px;">
    <div class="container text-center">
        <div style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 10px;">D'Store</h4>
            <p>Your trusted source for the best products. Quality and customer satisfaction guaranteed.</p>
        </div>

        <div style="margin-bottom: 20px;">
            <p>Contact me: <a href="mailto:support@yourcompany.com" style="color: #fff;">haminhduccp@yourcompany.com</a></p>
            <p>Phone: +1 (123) 456-7890</p>
        </div>

        <div>
            <p>&copy; 2024 D'Store. All Rights Reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>

<style>
/* Profile Panel Styles */
.profile-panel {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    font-family: Arial, sans-serif;
}

.profile-panel h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.profile-section {
    margin-bottom: 30px;
}

.profile-section h3 {
    color: #444;
    margin-bottom: 10px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 5px;
}

.info-box {
    background-color: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.info-box p {
    margin: 10px 0;
    color: #555;
}

.info-box form label {
    display: block;
    font-weight: bold;
    margin: 10px 0 5px;
}

.info-box input[type="text"],
.info-box input[type="email"],
.info-box input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.info-box button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.info-box button:hover {
    background-color: #0056b3;
}

footer {
    text-align: center;
    font-size: 14px;
    padding: 20px;
}
footer a {
    color: #fff;
    text-decoration: none;
    margin: 0 5px;
}
footer a:hover {
    text-decoration: underline;
}
footer img {
    width: 24px;
    margin: 0 5px;
}
</style>

