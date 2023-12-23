<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteRestaurant'])) {
        $deleteRestaurantID = $_POST['deleteRestaurant'];
        // Perform the deletion, you might want to add additional checks here
        $deleteSql = "DELETE FROM restaurants WHERE RestaurantID = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        mysqli_stmt_bind_param($deleteStmt, "i", $deleteRestaurantID);
        mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
    }
}

// Fetch the restaurants added by the current restaurant owner
$ownerID = $_SESSION["id"];
$sql = "SELECT * FROM restaurants WHERE ownerID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $ownerID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
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

        <!-- View Restaurants Table -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table class="table">
                <thead>
                    <tr>
                        <th>Restaurant Name</th>
                        <th>Location</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['RestaurantName']}</td>";
                        echo "<td>{$row['Location']}</td>";
                        echo "<td><img src='{$row['RestaurantImage']}' alt='Restaurant Image' style='max-width: 100px; max-height: 100px;'></td>";
                        echo "<td>
                                <a class='btn btn-info' href='edit_restaurant.php?restaurantID={$row['RestaurantID']}'>Edit</a>
                                <button type='submit' name='deleteRestaurant' value='{$row['RestaurantID']}' class='btn btn-danger'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
