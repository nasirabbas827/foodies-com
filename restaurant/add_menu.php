<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ownerID = $_SESSION["id"];
    $restaurantID = $_POST["restaurantID"];
    $itemName = $_POST["itemName"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $categoryID = $_POST["categoryID"];
    $specialDiscount = $_POST["specialDiscount"];

    // Process and upload the menu item image (if needed)
    $imageDirectory = "uploads/menu/"; // Set your menu item image upload directory
    $imageURL = ""; // Default value

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

    // Insert menu item details into the database
    $insertSql = "INSERT INTO menuitems (RestaurantID, ItemName, Description, Price, CategoryID, ImageURL, SpecialDiscount) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    mysqli_stmt_bind_param($insertStmt, "issdiss", $restaurantID, $itemName, $description, $price, $categoryID, $imageURL, $specialDiscount);
    mysqli_stmt_execute($insertStmt);
    mysqli_stmt_close($insertStmt);
    echo("Menu Item Added Successfully");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mb-5">
        <h2>Add Menu Item</h2>

        <!-- Add Menu Item Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="restaurantID">Select Restaurant:</label>
                <!-- Fetch restaurants owned by the logged-in owner -->
                <select name="restaurantID" class="form-control" required>
                    <?php
                    $ownerID = $_SESSION["id"];
                    $fetchRestaurantsSql = "SELECT * FROM restaurants WHERE ownerID = ?";
                    $fetchRestaurantsStmt = mysqli_prepare($conn, $fetchRestaurantsSql);
                    mysqli_stmt_bind_param($fetchRestaurantsStmt, "i", $ownerID);
                    mysqli_stmt_execute($fetchRestaurantsStmt);
                    $result = mysqli_stmt_get_result($fetchRestaurantsStmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['RestaurantID'] . "'>" . $row['RestaurantName'] . "</option>";
                    }

                    mysqli_stmt_close($fetchRestaurantsStmt);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="itemName">Menu Item Name:</label>
                <input type="text" name="itemName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" name="price" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="categoryID">Category:</label>
                <!-- Fetch categories from the database and populate the dropdown -->
                <select name="categoryID" class="form-control" required>
                    <?php
                    $fetchCategoriesSql = "SELECT * FROM categories WHERE UserID = ?";
                    $fetchCategoriesStmt = mysqli_prepare($conn, $fetchCategoriesSql);
                    mysqli_stmt_bind_param($fetchCategoriesStmt, "i", $_SESSION["id"]);
                    mysqli_stmt_execute($fetchCategoriesStmt);
                    $categoriesResult = mysqli_stmt_get_result($fetchCategoriesStmt);

                    while ($row = mysqli_fetch_assoc($categoriesResult)) {
                        echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryName'] . "</option>";
                    }

                    mysqli_stmt_close($fetchCategoriesStmt);
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="specialDiscount">Special Discount:</label>
                <input type="text" name="specialDiscount" class="form-control" maxlength="255">
            </div>
            <div class="form-group">
                <label for="imageURL">Menu Item Image:</label>
                <input type="file" name="imageURL" class="form-control-file" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Add Menu Item</button>
            <a class="btn btn-outline-dark" href="view_menu.php">View Menu</a>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
