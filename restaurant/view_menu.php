<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Function to delete a menu item
function deleteMenuItem($conn, $menuItemID) {
    $deleteSql = "DELETE FROM MenuItems WHERE MenuItemID = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "i", $menuItemID);
    mysqli_stmt_execute($deleteStmt);
    mysqli_stmt_close($deleteStmt);
}

// Check if the delete button is clicked
if (isset($_POST['delete']) && isset($_POST['menuItemID'])) {
    deleteMenuItem($conn, $_POST['menuItemID']);
}

// Fetch user's menu items with category name, restaurant name, and picture
$fetchMenuItemsSql = "SELECT M.*, C.CategoryName, R.RestaurantName, R.RestaurantImage
                      FROM MenuItems M
                      INNER JOIN Categories C ON M.CategoryID = C.CategoryID
                      INNER JOIN Restaurants R ON M.RestaurantID = R.RestaurantID
                      WHERE R.OwnerID = ?";
$fetchMenuItemsStmt = mysqli_prepare($conn, $fetchMenuItemsSql);
mysqli_stmt_bind_param($fetchMenuItemsStmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($fetchMenuItemsStmt);
$menuItemsResult = mysqli_stmt_get_result($fetchMenuItemsStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Menu Items</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>View Menu Items</h2>

        <!-- Display Menu Items -->
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Special Discount</th>
                    <th>Restaurant</th>
                    <th>Category Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) : ?>
                    <tr>
                        <td><?php echo $menuItem['ItemName']; ?></td>
                        <td><?php echo $menuItem['Description']; ?></td>
                        <td><?php echo $menuItem['Price']; ?></td>
                        <td><?php echo $menuItem['CategoryName']; ?></td>
                        <td><?php echo $menuItem['SpecialDiscount']; ?></td>
                        <td><?php echo $menuItem['RestaurantName']; ?></td>
                        <td>
                            <img src="<?php echo $menuItem['RestaurantImage']; ?>" alt="Restaurant Image" width="50">
                        </td>
                        <td>
                            <a href="edit_menu_item.php?menuItemID=<?php echo $menuItem['MenuItemID']; ?>" class="btn btn-warning">Edit</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="menuItemID" value="<?php echo $menuItem['MenuItemID']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this menu item?')">Delete</button>
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
