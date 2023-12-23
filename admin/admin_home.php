<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = 0;
$totalFoodLovers = 0;
$totalRestaurantOwners = 0;
$totalRestaurants = 0;
$totalOrders = 0;
$totalFeedbacks = 0;
$totalPendingUsers = 0;

// Fetch total users
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];

// Fetch total Food Lovers
$totalFoodLovers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'food_lover'"))['total'];

// Fetch total Restaurant Owners
$totalRestaurantOwners = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'restaurant_owner'"))['total'];

// Fetch total restaurants
$totalRestaurants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM restaurants"))['total'];

// Fetch total orders (assuming you have an orders table)
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];

// Fetch total feedbacks (assuming you have a feedbacks table)
$totalFeedbacks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedbacks"))['total'];

// Fetch total pending users (assuming you have a status column in the users table)
$totalPendingUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE status = 'pending'"))['total'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center">Admin Dashboard</h2>
        <div class="row mt-4">
            <!-- Total Users Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Food LoversCard -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Food Lovers</h5>
                        <p class="card-text"><?php echo $totalFoodLovers; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Restaurant Owners  Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Restaurant Owners</h5>
                        <p class="card-text"><?php echo $totalRestaurantOwners; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Restaurants Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Restaurants</h5>
                        <p class="card-text"><?php echo $totalRestaurants; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text"><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Feedbacks Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Feedbacks</h5>
                        <p class="card-text"><?php echo $totalFeedbacks; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Pending Users Card -->
            <div class="col-md-4">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Pending Users</h5>
                        <p class="card-text"><?php echo $totalPendingUsers; ?></p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
