<?php
session_start();
include 'connect1.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login1.php"); // Redirect if not logged in
    exit;
}

$user_id = $_SESSION['id']; // Get user ID from session

// Handle adding items to the cart (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if the product is already in the cart for the logged-in user
    $sql = "SELECT * FROM cart WHERE userID = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Product already in the cart, update the quantity by replacing the current value
        $sql = "UPDATE cart SET quantity = ? WHERE userID = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    } else {
        // Add new product to cart
        $sql = "INSERT INTO cart (product_id, userID, quantity) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $product_id, $user_id, $quantity);
        mysqli_stmt_execute($stmt);
    }
}

// Handle removing items from the cart (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_product_id'])) {
    $remove_product_id = $_POST['remove_product_id'];

    $sql = "DELETE FROM cart WHERE userID = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $remove_product_id);
    mysqli_stmt_execute($stmt);
}

// Handle updating the quantity of items in the cart (POST request)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    // Ensure the quantity is valid (greater than 0)
    if ($new_quantity > 0) {
        $sql = "UPDATE cart SET quantity = ? WHERE userID = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $new_quantity, $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    }
}

// Retrieve the cart items for the logged-in user
$sql = "SELECT c.product_id, p.product_name, p.product_price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.userID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="t2.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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

</header>

<div class="container mt-5">
    <h1>Your Shopping Cart</h1>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. Go back to the <a href="homepage.php">homepage</a> to add products.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total = 0;
                foreach ($cart_items as $item):
                    $total = $item['product_price'] * $item['quantity'];
                    $grand_total += $total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                    <td>
                        <form action="cart.php" method="POST">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" >
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                    <td>
                        <!-- Remove from cart -->
                        <form action="cart.php" method="POST" style="display:inline;">
                            <input type="hidden" name="remove_product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Grand Total</strong></td>
                    <td colspan="2"><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>


        <div class="d-flex justify-content-between">
        <a href="homepage.php" class="btn btn-primary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-success">Checkout</a>


        </div>
    <?php endif; ?>
</div>

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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
