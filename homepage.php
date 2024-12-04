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
    <title>Cosmetic Store</title>
    <link rel="stylesheet" href="t2.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to show product details in the modal
        function showDetails(button) {
            const product = button.closest('.product');
            const name = product.dataset.name;
            const price = product.dataset.price;
            const description = product.dataset.description;
            const img = product.dataset.img;

            // Update modal content
            document.getElementById('productModalLabel').innerText = name;
            document.getElementById('modalProductImage').src = img;
            document.getElementById('modalProductPrice').innerText = price;
            document.getElementById('modalProductDescription').innerText = description;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }


        
        
    </script>
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
                <?php if ($roleid == 2): ?>
                    <div class="">
                        <a href="add_product.php">Admin Panel</a>
                    </div>
                <?php endif; ?>

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
                    <li><a href="#best-seller">Best Seller</a></li>
                    <li><a href="#face">Face</a></li>
                    <li><a href="#cheek">Cheek</a></li>
                    <li><a href="#eye">Eye</a></li>
                    <li><a href="#lip">Lip</a></li>
                    <a href="services.php">Stores & Services</a>
                </ul>
            </nav>

            <div class="search-bar">
                <input 
                    type="text" 
                    id="homepageSearch" 
                    placeholder="Search products..." 
                    oninput="filterProducts()"
                >
            </div>

            <script>

            function filterProducts() {
                const searchInput = document.getElementById("homepageSearch").value.toLowerCase();
                const products = document.querySelectorAll(".product"); // Select all product cards
                const carousel = document.getElementById("demo"); // Carousel element

                let hasResults = false; // Track if there are any matching results

                products.forEach((product) => {
                    const productName = product.dataset.name.toLowerCase();
                    const productDescription = product.dataset.description.toLowerCase();

                    // Check if the search input matches the product name or description
                    if (productName.includes(searchInput) || productDescription.includes(searchInput)) {
                        product.style.display = ""; // Show the product
                        hasResults = true;
                    } else {
                        product.style.display = "none"; // Hide the product
                    }
                });

                // Hide carousel if search input is not empty or no results
                if (searchInput.trim() !== "" || !hasResults) {
                    carousel.style.display = "none";
                } else {
                    carousel.style.display = ""; // Show the carousel when search is cleared
                }
            }


            </script>
        </div>
    </header>

    <!-- Carousel -->
    <div id="demo" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="Image/carou1.jpg" alt="Lipstick" class="d-block">
            </div>
            <div class="carousel-item">
                <img src="Image/carou2.jpg" alt="Chicago" class="d-block">
            </div>
            <div class="carousel-item">
                <img src="Image/carou3.jpg" alt="New York" class="d-block">
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Main Content -->
    <main>            
                <section id="best-seller">
                    <div class="section-header">
                        <h2>Best Seller</h2>
                    </div>

                    <div class="product-list">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE category = 'best-seller' LIMIT 6");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='product card' 
                                data-name='{$row['product_name']}' 
                                data-price='{$row['product_price']}' 
                                data-description='{$row['product_description']}' 
                                data-img='Image/{$row['product_img']}'>
                                <img src='Image/{$row['product_img']}' class='card-img-top' alt='{$row['product_name']}'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['product_name']}</h5>
                                    <p class='card-text'>\${$row['product_price']}</p>
                                    <button class='btn btn-primary' onclick='showDetails(this)'>Details</button>
                                    <form action='cart.php' method='POST' style='display: inline-block; margin-top: 10px;'>
                                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                        <input type='hidden' name='product_name' value='{$row['product_name']}'>
                                        <input type='hidden' name='product_price' value='{$row['product_price']}'>
                                        <input type='hidden' name='quantity' value='1'>
                                        <button class='btn btn-success' onclick='addToCart(\"{$row['product_name']}\", \"{$row['product_price']}\", {$row['product_id']})'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </section>

                <section id="face">
                    <div class="section-header">
                        <h2>Face Products</h2>
                    </div>
                    <div class="product-list">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE category = 'face' LIMIT 6");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='product card' 
                                data-name='{$row['product_name']}' 
                                data-price='{$row['product_price']}' 
                                data-description='{$row['product_description']}' 
                                data-img='Image/{$row['product_img']}'>
                                <img src='Image/{$row['product_img']}' class='card-img-top' alt='{$row['product_name']}'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['product_name']}</h5>
                                    <p class='card-text'>\${$row['product_price']}</p>
                                    <button class='btn btn-primary' onclick='showDetails(this)'>Details</button>
                                    <form action='cart.php' method='POST' style='display: inline-block; margin-top: 10px;'>
                                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                        <input type='hidden' name='product_name' value='{$row['product_name']}'>
                                        <input type='hidden' name='product_price' value='{$row['product_price']}'>
                                        <input type='hidden' name='quantity' value='1'>
                                        <button class='btn btn-success' onclick='addToCart(\"{$row['product_name']}\", \"{$row['product_price']}\", {$row['product_id']})'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </section>

                <section id="cheek">
                    <div class="section-header">
                        <h2>Cheek Products</h2>
                    </div>
                    <div class="product-list">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE category = 'cheek' LIMIT 6");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='product card' 
                                data-name='{$row['product_name']}' 
                                data-price='{$row['product_price']}' 
                                data-description='{$row['product_description']}' 
                                data-img='Image/{$row['product_img']}'>
                                <img src='Image/{$row['product_img']}' class='card-img-top' alt='{$row['product_name']}'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['product_name']}</h5>
                                    <p class='card-text'>\${$row['product_price']}</p>
                                    <button class='btn btn-primary' onclick='showDetails(this)'>Details</button>
                                    <form action='cart.php' method='POST' style='display: inline-block; margin-top: 10px;'>
                                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                        <input type='hidden' name='product_name' value='{$row['product_name']}'>
                                        <input type='hidden' name='product_price' value='{$row['product_price']}'>
                                        <input type='hidden' name='quantity' value='1'>
                                        <button class='btn btn-success' onclick='addToCart(\"{$row['product_name']}\", \"{$row['product_price']}\", {$row['product_id']})'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </section>

                <section id="eye">
                    <div class="section-header">
                        <h2>Eye Products</h2>
                    </div>
                    <div class="product-list">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE category = 'eye' LIMIT 6");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='product card' 
                                data-name='{$row['product_name']}' 
                                data-price='{$row['product_price']}' 
                                data-description='{$row['product_description']}' 
                                data-img='Image/{$row['product_img']}'>
                                <img src='Image/{$row['product_img']}' class='card-img-top' alt='{$row['product_name']}'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['product_name']}</h5>
                                    <p class='card-text'>\${$row['product_price']}</p>
                                    <button class='btn btn-primary' onclick='showDetails(this)'>Details</button>
                                    <form action='cart.php' method='POST' style='display: inline-block; margin-top: 10px;'>
                                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                        <input type='hidden' name='product_name' value='{$row['product_name']}'>
                                        <input type='hidden' name='product_price' value='{$row['product_price']}'>
                                        <input type='hidden' name='quantity' value='1'>
                                        <button class='btn btn-success' onclick='addToCart(\"{$row['product_name']}\", \"{$row['product_price']}\", {$row['product_id']})'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </section>

                <section id="lip">
                    <div class="section-header">
                        <h2>Lip Products</h2>
                    </div>
                    <div class="product-list">
                        <?php
                        // Fetch face products
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE category = 'lip' LIMIT 6");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <div class='product card' 
                                data-name='{$row['product_name']}' 
                                data-price='{$row['product_price']}' 
                                data-description='{$row['product_description']}' 
                                data-img='Image/{$row['product_img']}'>
                                <img src='Image/{$row['product_img']}' class='card-img-top' alt='{$row['product_name']}'>
                                <div class='card-body'>
                                    <h5 class='card-title'>{$row['product_name']}</h5>
                                    <p class='card-text'>\${$row['product_price']}</p>
                                    <button class='btn btn-primary' onclick='showDetails(this)'>Details</button>
                                    <form action='cart.php' method='POST' style='display: inline-block; margin-top: 10px;'>
                                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                                        <input type='hidden' name='product_name' value='{$row['product_name']}'>
                                        <input type='hidden' name='product_price' value='{$row['product_price']}'>
                                        <input type='hidden' name='quantity' value='1'>
                                        <button class='btn btn-success' onclick='addToCart(\"{$row['product_name']}\", \"{$row['product_price']}\", {$row['product_id']})'>Add to Cart</button>
                                    </form>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>
                </section>

                <button id="stickyScrollToTop" class="btn btn-primary">↑</button>
                    
                <script>
                const stickyScrollToTop = document.getElementById('stickyScrollToTop');
                    
                window.addEventListener('scroll', function () {
                    if (window.scrollY > 100) { // Show after scrolling 100px
                        stickyScrollToTop.style.display = 'block';
                    } else {
                        stickyScrollToTop.style.display = 'none';
                    }
                });

                stickyScrollToTop.addEventListener('click', function () {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
                </script>



                <!-- Modal -->
                <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img id="modalProductImage" src="" alt="Product Image">
                                <p><strong>Price:</strong> $<span id="modalProductPrice"></span></p>
                                <p id="modalProductDescription"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>

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
</html>

<?php


mysqli_close($conn); // Close the database connection

?>