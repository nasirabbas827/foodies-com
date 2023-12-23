<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if menuItemID and restaurantID are provided in the query string
if (!isset($_GET["menuItemID"]) || !isset($_GET["restaurantID"])) {
    echo "Invalid request. Please provide valid menuItemID and restaurantID.";
    exit;
}

$menuItemID = $_GET["menuItemID"];
$restaurantID = $_GET["restaurantID"];

// Fetch details of the selected menu item
$fetchMenuItemSql = "SELECT * FROM MenuItems WHERE MenuItemID = ?";
$fetchMenuItemStmt = mysqli_prepare($conn, $fetchMenuItemSql);
mysqli_stmt_bind_param($fetchMenuItemStmt, "i", $menuItemID);
mysqli_stmt_execute($fetchMenuItemStmt);
$menuItemResult = mysqli_stmt_get_result($fetchMenuItemStmt);

if ($menuItem = mysqli_fetch_assoc($menuItemResult)) {
    // Menu Item details
    $itemName = $menuItem['ItemName'];
    $description = $menuItem['Description'];
    $price = $menuItem['Price'];
    $specialDiscount = $menuItem['SpecialDiscount'];
    $imageURL = "../restaurant/" . $menuItem['ImageURL'];

    // Fetch restaurant details
    $fetchRestaurantSql = "SELECT RestaurantName FROM restaurants WHERE RestaurantID = ?";
    $fetchRestaurantStmt = mysqli_prepare($conn, $fetchRestaurantSql);
    mysqli_stmt_bind_param($fetchRestaurantStmt, "i", $restaurantID);
    mysqli_stmt_execute($fetchRestaurantStmt);
    $restaurantResult = mysqli_stmt_get_result($fetchRestaurantStmt);

    if ($restaurant = mysqli_fetch_assoc($restaurantResult)) {
        $restaurantName = $restaurant['RestaurantName'];
    } else {
        $restaurantName = "Unknown Restaurant";
    }

} else {
    echo "Menu item not found.";
    exit;
}

// Process order form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deliveryAddress = $_POST["deliveryAddress"];
    $paymentStatus = "Cash on Delivery";
    $orderStatus = "Pending";

    // Insert order details into the database
    $insertOrderSql = "INSERT INTO Orders (UserID, MenuItemID, RestaurantID, DeliveryAddress, PaymentStatus, OrderStatus) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $insertOrderStmt = mysqli_prepare($conn, $insertOrderSql);
    mysqli_stmt_bind_param($insertOrderStmt, "iiisss", $_SESSION["id"], $menuItemID, $restaurantID, $deliveryAddress, $paymentStatus, $orderStatus);
    mysqli_stmt_execute($insertOrderStmt);
    mysqli_stmt_close($insertOrderStmt);

    // Display success message
    $successMessage = "Order placed successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Now</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container mb-5">
        <?php if (isset($successMessage)) : ?>
            <div class="alert alert-success mt-3" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <h2>Order Now - <?php echo $itemName; ?> at <?php echo $restaurantName; ?></h2>

        <!-- Display Menu Item details -->
        <div class="card mb-4">
            <img src="<?php echo $imageURL; ?>" class="card-img-top" alt="Menu Item Image ">
            <div class="card-body">
                <h5 class="card-title"><?php echo $itemName; ?></h5>
                <p class="card-text"><?php echo $description; ?></p>
                <p class="card-text">Price: $<?php echo $price; ?></p>
                <p class="card-text">Special Discount: <?php echo $specialDiscount; ?></p>
            </div>
        </div>

        <!-- Order Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?menuItemID=$menuItemID&restaurantID=$restaurantID"); ?>" method="post">
            <div class="form-group">
                <label for="deliveryAddress">Delivery Address:</label>
                <textarea name="deliveryAddress" class="form-control" rows="3" required></textarea>
            </div>
            <p style="color:red">Payment Would be Cash On Delivery</p>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
