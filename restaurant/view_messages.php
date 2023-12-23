<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Fetch the list of restaurants owned by the logged-in owner
$fetchRestaurantsSql = "SELECT * FROM restaurants WHERE OwnerID = ?";
$fetchRestaurantsStmt = mysqli_prepare($conn, $fetchRestaurantsSql);
mysqli_stmt_bind_param($fetchRestaurantsStmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($fetchRestaurantsStmt);
$restaurantsResult = mysqli_stmt_get_result($fetchRestaurantsStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Restaurant</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Select a Restaurant</h2>
        <form action="restaurant_chat.php" method="get">
            <div class="form-group">
                <label for="restaurantID">Choose a Restaurant:</label>
                <select class="form-control" id="restaurantID" name="restaurantID">
                    <?php while ($restaurant = mysqli_fetch_assoc($restaurantsResult)) : ?>
                        <option value="<?php echo $restaurant['RestaurantID']; ?>"><?php echo $restaurant['RestaurantName']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">View Chat</button>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
