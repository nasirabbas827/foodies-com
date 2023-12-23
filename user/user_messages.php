<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Fetch all restaurants
$fetchAllRestaurantsSql = "SELECT * FROM restaurants";
$fetchAllRestaurantsStmt = mysqli_prepare($conn, $fetchAllRestaurantsSql);
mysqli_stmt_execute($fetchAllRestaurantsStmt);
$restaurantsResult = mysqli_stmt_get_result($fetchAllRestaurantsStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messages</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <h2>Your Messages</h2>
        <div class="row">

        <div class="container">
    <h2>Your Messages</h2>
    <div class="row">

        <?php while ($restaurant = mysqli_fetch_assoc($restaurantsResult)) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $restaurant['RestaurantName']; ?></h5>
                        <p class="card-text"><?php echo $restaurant['Location']; ?></p>
                        <a href="user_chat.php?restaurantID=<?php echo $restaurant['RestaurantID']; ?>" class="btn btn-primary btn-sm">Chat Now</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>


            <div class="col-md-8">
                <p>Select a restaurant to start or continue the conversation.</p>
            </div>

        </div>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
