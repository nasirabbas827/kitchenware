<?php
// Include config file
require_once 'config.php';

// Process verification token from URL parameter
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare SQL statement to update user status based on verification token
    $sql = "UPDATE users SET status = 'approved' WHERE verification_token = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);

    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
        // Verification successful, show success message
        echo '<div class="alert alert-success" role="alert">Your email has been verified successfully. You can now <a href="login.php">login</a>.</div>';
    } else {
        // Verification failed, show error message
        echo '<div class="alert alert-danger" role="alert">Oops! Verification failed. Please try again later.</div>';
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    // Token parameter is missing, show error message
    echo '<div class="alert alert-danger" role="alert">Invalid verification link.</div>';
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Email Verification</h2>
        <p class="text-center">Verifying your email...</p>
    </div>
</body>
</html>
