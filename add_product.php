<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
</head>
<body>
    <header>
        <h1>Admin Panel - Add Product</h1>
    </header>

    <div class="container">
        <h2>Add New Product</h2>
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <td>Product Name:</td>
                    <td><input type="text" name="product_name" required></td>
                </tr>
                <tr>
                    <td>Product Price:</td>
                    <td><input type="text" name="product_price" required></td>
                </tr>
                <tr>
                    <td>Quantity:</td>
                    <td><input type="text" name="quantity" required></td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>
                    <select name="category" class="form-select" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="best-seller">Best Seller</option>
                        <option value="face">Face</option>
                        <option value="cheek">Cheek</option>
                        <option value="lip">Lip</option>
                        <option value="eye">Eye</option>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td>Product Image:</td>
                    <td>
                        <input type="file" name="product_img" required>
                    </td>
                </tr>
                <tr>
                    <td>Product Description:</td>
                    <td>
                        <input type="text" name="product_description" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="submit" name="add_product" value="Add" class="btn btn-primary">
                        <input type="reset" value="Clear" class="btn btn-primary" style="margin-left: 10px;">
                    </td>
                </tr>
            </table>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="homepage.php" class="btn btn-secondary">Back to Homepage</a> <!-- You can replace 'product_list.php' with your own page URL -->
        </div>
    </div>
</body>
</html>

<?php
include "connect1.php";

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    $product_description = $_POST['product_description'];

    // Check if file was uploaded
    if (isset($_FILES['product_img']) && $_FILES['product_img']['error'] === 0) {
        // Handle the image upload
        $product_img = $_FILES['product_img']['name'];
        $product_img_tmp = $_FILES['product_img']['tmp_name'];
        $upload_dir = "Image/"; // Folder where images will be stored
        $upload_file = $upload_dir . basename($product_img);

        // Move the uploaded file to the server directory
        if (move_uploaded_file($product_img_tmp, $upload_file)) {
            // Prepare SQL query
            $sql = "INSERT INTO products (product_name, product_price, quantity, category, product_img, product_description) 
                    VALUES ('$product_name', '$product_price', '$quantity','$category' , '$product_img', '$product_description')";

            // Execute the query
            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo "<script>alert('Product added successfully');</script>";
                echo "<script>window.location.href = 'add_product.php';</script>";
            } else {
                echo "<script>alert('Error adding product: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Failed to upload image');</script>";
        }
    } else {
        echo "<script>alert('Please select an image to upload or check for upload errors.');</script>";
    }
}
?>

