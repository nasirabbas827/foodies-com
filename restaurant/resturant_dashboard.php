<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

$ownerID = $_SESSION["id"];

// Fetch total counts
$totalRestaurants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM restaurants WHERE ownerID = $ownerID"))['total'];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders WHERE restaurantID IN (SELECT RestaurantID FROM restaurants WHERE ownerID = $ownerID)"))['total'];
$totalFeedbacks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedbacks WHERE restaurantID IN (SELECT RestaurantID FROM restaurants WHERE ownerID = $ownerID)"))['total'];
$totalMessages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM chat_messages WHERE restaurantID IN (SELECT RestaurantID FROM restaurants WHERE ownerID = $ownerID)"))['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Restaurant Owner Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Welcome, Restaurant Owner!</h2>

        <div class="row mt-4">
            <!-- Total Restaurants Card -->
            <div class="col-md-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Restaurants</h5>
                        <p class="card-text"><?php echo $totalRestaurants; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="col-md-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text"><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Feedbacks Card -->
            <div class="col-md-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Feedbacks</h5>
                        <p class="card-text"><?php echo $totalFeedbacks; ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Messages Card -->
            <div class="col-md-3">
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">Total Messages</h5>
                        <p class="card-text"><?php echo $totalMessages; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
