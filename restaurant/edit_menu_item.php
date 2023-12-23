<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the menu item ID is provided in the URL
if (!isset($_GET['menuItemID']) || empty($_GET['menuItemID'])) {
    header("location: view_menu.php");
    exit;
}

$menuItemID = $_GET['menuItemID'];

// Fetch menu item details for the given ID
$fetchMenuItemSql = "SELECT M.*, C.CategoryName
                     FROM MenuItems M
                     INNER JOIN Categories C ON M.CategoryID = C.CategoryID
                     WHERE M.MenuItemID = ?";
$fetchMenuItemStmt = mysqli_prepare($conn, $fetchMenuItemSql);
mysqli_stmt_bind_param($fetchMenuItemStmt, "i", $menuItemID);
mysqli_stmt_execute($fetchMenuItemStmt);
$menuItemResult = mysqli_stmt_get_result($fetchMenuItemStmt);

if (!$menuItem = mysqli_fetch_assoc($menuItemResult)) {
    // If the menu item is not found, redirect to the menu view page
    header("location: view_menu.php");
    exit;
}

// Check if the form is submitted for updating the menu item
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST["itemName"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $categoryID = $_POST["categoryID"];
    $specialDiscount = $_POST["specialDiscount"];

    // Process and upload the updated menu item image (if needed)
    $imageDirectory = "uploads/menu/"; // Set your menu item image upload directory
    $imageURL = $menuItem['ImageURL']; // Default value

    if ($_FILES["imageURL"]["size"] > 0) {
        $imageName = basename($_FILES["imageURL"]["name"]);
        $imagePath = $imageDirectory . $imageName;
        $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["imageURL"]["tmp_name"]);
        if ($check !== false) {
            // Upload the image file
            move_uploaded_file($_FILES["imageURL"]["tmp_name"], $imagePath);
            $imageURL = $imagePath;
        } else {
            echo "File is not an image.";
            exit;
        }
    }

// Update menu item details in the database
$updateSql = "UPDATE MenuItems 
              SET ItemName = ?, Description = ?, Price = ?, CategoryID = ?, ImageURL = ?, SpecialDiscount = ?
              WHERE MenuItemID = ?";
$updateStmt = mysqli_prepare($conn, $updateSql);
mysqli_stmt_bind_param($updateStmt, "ssdissi", $itemName, $description, $price, $categoryID, $imageURL, $specialDiscount, $menuItemID);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);


    // Redirect to the menu view page after update
    header("location: view_menu.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mb-5">
        <h2>Edit Menu Item</h2>

        <!-- Edit Menu Item Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?menuItemID=" . $menuItemID); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="itemName">Menu Item Name:</label>
                <input type="text" name="itemName" class="form-control" value="<?php echo $menuItem['ItemName']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo $menuItem['Description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $menuItem['Price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="categoryID">Category:</label>
                <!-- Fetch categories from the database and populate the dropdown -->
                <select name="categoryID" class="form-control" required>
                    <?php
                    $fetchCategoriesSql = "SELECT * FROM Categories";
                    $fetchCategoriesStmt = mysqli_prepare($conn, $fetchCategoriesSql);
                    mysqli_stmt_execute($fetchCategoriesStmt);
                    $categoriesResult = mysqli_stmt_get_result($fetchCategoriesStmt);

                    while ($row = mysqli_fetch_assoc($categoriesResult)) {
                        $selected = ($row['CategoryID'] == $menuItem['CategoryID']) ? "selected" : "";
                        echo "<option value='" . $row['CategoryID'] . "' $selected>" . $row['CategoryName'] . "</option>";
                    }

                    mysqli_stmt_close($fetchCategoriesStmt);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="specialDiscount">Special Discount:</label>
                <input type="text" name="specialDiscount" class="form-control" maxlength="255" value="<?php echo $menuItem['SpecialDiscount']; ?>">
            </div>
            <div class="form-group">
                <label for="imageURL">Menu Item Image:</label>
                <input type="file" name="imageURL" class="form-control-file" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Update Menu Item</button>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
