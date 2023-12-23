<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the user ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update the status in the database
        $new_status = $_POST['new_status'];
        $update_sql = "UPDATE users SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect back to the admin users page
        header("Location: view_users.php");
        exit;
    }

    // Fetch user details for displaying in the form
    $fetch_sql = "SELECT id, username, status FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $fetch_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $username, $status);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Close the database connection
    mysqli_close($conn);
} else {
    // If user ID is not provided in the URL, redirect to admin users page
    header("Location: view_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Status</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('admin_navbar.php'); ?>

    <div class="container">
        <h2 class="text-center">Update User Status</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $user_id); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" value="<?php echo $username; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="status">Current Status:</label>
                <input type="text" class="form-control" id="status" value="<?php echo $status; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="new_status">New Status:</label>
                <select class="form-control" id="new_status" name="new_status">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
