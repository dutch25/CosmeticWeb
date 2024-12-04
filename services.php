<?php
session_start(); // Start session to access login status
include "connect1.php"; // Include database connection

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login1.php");
    exit;
}

$user_id = $_SESSION["id"];

// Fetch user information from the database, including the roleid
$sql = "SELECT username, email, fullname, role_id FROM users WHERE userid = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $email, $fullname, $roleid);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing the SQL statement.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores & Services</title>
    <link rel="stylesheet" href="t2.css">
</head>
<body>
    <header>
        <!-- Top Header -->
        <div class="header-top">
            <div class="left-section">
                <a href="profile.php" class="account-link">
                    <span class="icon">👤</span> 
                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    <?php else: ?>
                        MY ACCOUNT
                    <?php endif; ?>
                </a>
            </div>

            <div class="center-section">
                <a href="homepage.php"><h1>D'Store</h1></a>
            </div>

            <div class="user-actions">


                <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                    <!-- Show Sign In button if user is not logged in -->
                    <a href="login1.php">Sign In</a>
                <?php else: ?>
                    <!-- Show Logout button if user is logged in -->
                    <a href="logout.php">Logout</a>
                <?php endif; ?>

                <a href="#" class="heart-icon">♥</a>
                <a href="cart.php" class="cart-icon">🛒</a>
            </div>
        </div>

        <!-- Bottom Header -->
        <div class="header-bottom">
            <nav>
            <ul class="menu">
                    <li><a href="homepage.php#best-seller">Best Seller</a></li>
                    <li><a href="homepage.php#face">Face</a></li>
                    <li><a href="homepage.php#cheek">Cheek</a></li>
                    <li><a href="homepage.php#eye">Eye</a></li>
                    <li><a href="homepage.php#lip">Lip</a></li>
                    <a href="services.php">Stores & Services</a>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="service-terms">
            <div class="container">
                <!-- Store Locations -->
                <div class="section-header">
                    <h2>Our Store Locations</h2>
                </div>
                <div class="store-locations">
                    <div class="store">
                        <h3>Hoan Kiem Store</h3>
                        <p>25 Trang Tien St, Hoan Kiem, Hanoi</p>
                    </div>
                    <div class="store">
                        <h3>Royal City Mall Branch</h3>
                        <p>72A Nguyen Trai St, Thanh Xuan, Hanoi</p>
                    </div>
                    <div class="store">
                        <h3>Vincom Long Bien</h3>
                        <p>Vincom Center, Long Bien, Hanoi</p>
                    </div>
                </div>

                <!-- Services We Offer -->
                <div class="section-header">
                    <h2>Services We Offer</h2>
                </div>
                <div class="services">
                    <ul>
                        <li>Free Makeup Consultation</li>
                        <li>Gift Wrapping Services</li>
                        <li>Skincare Routine Recommendations</li>
                        <li>Personalized Product Recommendations</li>
                        <li>In-store Tutorials and Workshops</li>
                    </ul>
                </div>
            </div>
        </section>

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

<?php


mysqli_close($conn); // Close the database connection

?>