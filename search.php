<?php
session_start();
include "connect1.php";

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo "Please enter a search term.";
    exit;
}

$query = mysqli_real_escape_string($conn, trim($_GET['query']));

$sql = "SELECT * FROM products WHERE product_name LIKE ? OR product_description LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
$searchTerm = "%$query%";

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    echo "<h1>Search Results for '$query'</h1>";
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='product'>
                <h3>{$row['product_name']}</h3>
                <p>{$row['product_description']}</p>
                <p>Price: \${$row['product_price']}</p>
            </div>";
        }
    } else {
        echo "<p>No products found.</p>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error: Could not prepare the search query.";
}

mysqli_close($conn);
?>
