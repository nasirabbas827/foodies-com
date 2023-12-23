<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Fetch orders for the restaurant owner
$fetchOrdersSql = "SELECT O.*, M.ItemName, M.Description AS MenuItemDescription, U.UserName, R.RestaurantName
                   FROM Orders O
                   JOIN MenuItems M ON O.MenuItemID = M.MenuItemID
                   JOIN Users U ON O.UserID = U.id
                   JOIN Restaurants R ON M.RestaurantID = R.RestaurantID
                   WHERE R.OwnerID = ?";
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
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>View Orders</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>OrderID</th>
                    <th>MenuItem</th>
                    <th>Username</th>
                    <th>RestaurantName</th>
                    <th>Delivery Address</th>
                    <th>Payment Status</th>
                    <th>Order Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($ordersResult)) : ?>
                    <tr>
                        <td><?php echo $order['OrderID']; ?></td>
                        <td><?php echo $order['ItemName']; ?></td>
                        <td><?php echo $order['UserName']; ?></td>
                        <td><?php echo $order['RestaurantName']; ?></td>
                        <td><?php echo $order['DeliveryAddress']; ?></td>
                        <td><?php echo $order['PaymentStatus']; ?></td>
                        <td><?php echo $order['OrderStatus']; ?></td>
                        <td>
                            <form method="post">
                                <select name="newOrderStatus" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <input type="hidden" name="orderID" value="<?php echo $order['OrderID']; ?>">
                                <button type="submit" name="updateStatus" class="mt-2 btn btn-primary">Update Status</button>
                            </form>
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

<?php
// Update order status
if (isset($_POST['updateStatus'])) {
    $newOrderStatus = $_POST['newOrderStatus'];
    $orderID = $_POST['orderID'];

    $updateStatusSql = "UPDATE Orders SET OrderStatus = ? WHERE OrderID = ?";
    $updateStatusStmt = mysqli_prepare($conn, $updateStatusSql);
    mysqli_stmt_bind_param($updateStatusStmt, "si", $newOrderStatus, $orderID);
    mysqli_stmt_execute($updateStatusStmt);
    mysqli_stmt_close($updateStatusStmt);

    // Redirect to refresh the page after updating status
    header("location: view_orders.php");
    exit;
}
?>
