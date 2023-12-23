<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Fetch orders for the logged-in user
$fetchOrdersSql = "SELECT o.OrderID, o.MenuItemID, o.RestaurantID, o.DeliveryAddress, o.PaymentStatus, o.OrderStatus, m.ItemName AS MenuItemName, r.RestaurantName
                   FROM Orders o
                   JOIN MenuItems m ON o.MenuItemID = m.MenuItemID
                   JOIN Restaurants r ON o.RestaurantID = r.RestaurantID
                   WHERE o.UserID = ?";
$fetchOrdersStmt = mysqli_prepare($conn, $fetchOrdersSql);
mysqli_stmt_bind_param($fetchOrdersStmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($fetchOrdersStmt);
$ordersResult = mysqli_stmt_get_result($fetchOrdersStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <h2>View Orders</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>OrderID</th>
                    <th>MenuItem</th>
                    <th>Restaurant</th>
                    <th>Delivery Address</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($ordersResult)) : ?>
                    <tr>
                        <td><?php echo $order['OrderID']; ?></td>
                        <td><?php echo $order['MenuItemName']; ?></td>
                        <td><?php echo $order['RestaurantName']; ?></td>
                        <td><?php echo $order['DeliveryAddress']; ?></td>
                        <td><?php echo $order['PaymentStatus']; ?></td>
                        <td><?php echo $order['OrderStatus']; ?></td>
                        <td>
                            <?php if ($order['OrderStatus'] === 'Delivered') : ?>
                                <a href="feedback.php?orderID=<?php echo $order['OrderID']; ?>&restaurantID=<?php echo $order['RestaurantID']; ?>" class="btn btn-primary">Provide Feedback</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
