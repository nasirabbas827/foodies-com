<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if restaurantID is provided in the query string
if (!isset($_GET["restaurantID"])) {
    echo "Invalid request. Please provide a valid restaurantID.";
    exit;
}

$restaurantID = $_GET["restaurantID"];

// Fetch unique categories for the specified restaurant
$fetchCategoriesSql = "SELECT DISTINCT c.CategoryID, c.CategoryName 
                       FROM Categories c
                       JOIN MenuItems mi ON c.CategoryID = mi.CategoryID
                       WHERE mi.RestaurantID = ?";
$fetchCategoriesStmt = mysqli_prepare($conn, $fetchCategoriesSql);
mysqli_stmt_bind_param($fetchCategoriesStmt, "i", $restaurantID);
mysqli_stmt_execute($fetchCategoriesStmt);
$categoriesResult = mysqli_stmt_get_result($fetchCategoriesStmt);

// Fetch menu items with category names for the specified restaurant and category
$fetchMenuItemsSql = "SELECT mi.*, c.CategoryName 
                     FROM MenuItems mi
                     JOIN Categories c ON mi.CategoryID = c.CategoryID
                     WHERE mi.RestaurantID = ?";

// Check if a category filter is applied
if (isset($_GET["category"])) {
    $selectedCategory = $_GET["category"];
    $fetchMenuItemsSql .= " AND c.CategoryName = ?";
}

$fetchMenuItemsStmt = mysqli_prepare($conn, $fetchMenuItemsSql);

// Bind parameters
if (isset($_GET["category"])) {
    mysqli_stmt_bind_param($fetchMenuItemsStmt, "is", $restaurantID, $selectedCategory);
} else {
    mysqli_stmt_bind_param($fetchMenuItemsStmt, "i", $restaurantID);
}

mysqli_stmt_execute($fetchMenuItemsStmt);
$menuItemsResult = mysqli_stmt_get_result($fetchMenuItemsStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <h2>Menu Items</h2>

        <!-- Add category filter form -->
        <form action="" method="get">
            <label for="category">Filter by Category:</label>
            <select class="form-control " name="category" id="category">
                <?php while ($category = mysqli_fetch_assoc($categoriesResult)) : ?>
                    <option value="<?php echo $category['CategoryName']; ?>"><?php echo $category['CategoryName']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="hidden" name="restaurantID" value="<?php echo $restaurantID; ?>">
            <button type="submit" class="m-2 btn btn-primary">Apply Filter</button>
        </form>

        <div class="row">
            <?php while ($menuItem = mysqli_fetch_assoc($menuItemsResult)) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="../restaurant/<?php echo $menuItem['ImageURL']; ?>" class="card-img-top" alt="Menu Item Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $menuItem['ItemName']; ?></h5>
                            <p class="card-text"><?php echo $menuItem['Description']; ?></p>
                            <p class="card-text">Category: <?php echo $menuItem['CategoryName']; ?></p>
                            <p class="card-text">Price: $<?php echo $menuItem['Price']; ?></p>
                            <p class="card-text">Special Discount: <?php echo $menuItem['SpecialDiscount']; ?></p>
                            <a href="order.php?menuItemID=<?php echo $menuItem['MenuItemID']; ?>&restaurantID=<?php echo $restaurantID; ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
