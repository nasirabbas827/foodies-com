<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to get owner's name based on ownerID
function getOwnerName($conn, $ownerID)
{
    $fetchOwnerNameSql = "SELECT username FROM users WHERE id = ?";
    $fetchOwnerNameStmt = mysqli_prepare($conn, $fetchOwnerNameSql);
    mysqli_stmt_bind_param($fetchOwnerNameStmt, "i", $ownerID);
    mysqli_stmt_execute($fetchOwnerNameStmt);
    $ownerNameResult = mysqli_stmt_get_result($fetchOwnerNameStmt);

    if ($ownerName = mysqli_fetch_assoc($ownerNameResult)) {
        return $ownerName['username'];
    } else {
        return "N/A";
    }
}

// Check if the delete action is requested
if (isset($_GET['delete']) && isset($_GET['restaurantID'])) {
    $restaurantIDToDelete = $_GET['restaurantID'];

    // Delete the restaurant from the database
    $deleteRestaurantSql = "DELETE FROM restaurants WHERE RestaurantID = ?";
    $deleteRestaurantStmt = mysqli_prepare($conn, $deleteRestaurantSql);
    mysqli_stmt_bind_param($deleteRestaurantStmt, "i", $restaurantIDToDelete);

    if (mysqli_stmt_execute($deleteRestaurantStmt)) {
        header("Location: admin_restaurants.php");
        exit;
    } else {
        echo "Error deleting restaurant: " . mysqli_error($conn);
    }
}

// Fetch all restaurants from the database
$fetchRestaurantsSql = "SELECT * FROM restaurants";
$fetchRestaurantsResult = mysqli_query($conn, $fetchRestaurantsSql);

// Check if any restaurants are found
if ($fetchRestaurantsResult) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Admin Restaurants</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <?php include('admin_navbar.php'); ?>

        <div class="container mt-5">
            <h2 class="text-center">Admin Restaurants</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Owner Name</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($fetchRestaurantsResult)) : ?>
                        <tr>
                            <td><?php echo $row['RestaurantID']; ?></td>
                            <td><?php echo getOwnerName($conn, $row['OwnerID']); ?></td>
                            <td><?php echo $row['RestaurantName']; ?></td>
                            <td><?php echo $row['Location']; ?></td>
                            <td>
                                <img src="../restaurant/<?php echo $row['RestaurantImage']; ?>" alt="Restaurant Image" style="max-width: 100px; max-height: 100px;">
                            </td>
                            <td>
                                <a href="admin_restaurants.php?delete=true&restaurantID=<?php echo $row['RestaurantID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this restaurant?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

    </html>
    <?php

    // Free result set
    mysqli_free_result($fetchRestaurantsResult);
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
