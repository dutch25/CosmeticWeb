<form action="update_profile.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>

    <button type="submit">Update Profile</button>
</form>

<?php
// Start the session
session_start();

// Include database connection
include "connect1.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $userId = $_SESSION['id']; // Assumes user ID is stored in session

    // Update query
    $sql = "UPDATE users SET username = ?, email = ?, phone = ? WHERE userid = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $userId);
        if (mysqli_stmt_execute($stmt)) {
            echo "Profile updated successfully.";
            // Optionally update session variables
            $_SESSION['username'] = $username;
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>

