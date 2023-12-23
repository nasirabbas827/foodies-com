<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if categoryID is provided in the URL
if (!isset($_GET["categoryID"])) {
    header("location: view_categories.php");
    exit;
}

$categoryID = $_GET["categoryID"];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updateCategory"])) {
    $newCategoryName = $_POST["newCategoryName"];

    // Update category in the database
    $updateSql = "UPDATE categories SET CategoryName = ? WHERE CategoryID = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "si", $newCategoryName, $categoryID);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    echo "Category updated successfully.";
}

// Fetch category details
$fetchCategorySql = "SELECT CategoryName FROM categories WHERE CategoryID = ?";
$fetchCategoryStmt = mysqli_prepare($conn, $fetchCategorySql);
mysqli_stmt_bind_param($fetchCategoryStmt, "i", $categoryID);
mysqli_stmt_execute($fetchCategoryStmt);
$categoryResult = mysqli_stmt_get_result($fetchCategoryStmt);

// Check if the category exists
if (mysqli_num_rows($categoryResult) == 0) {
    header("location: view_categories.php");
    exit;
}

$row = mysqli_fetch_assoc($categoryResult);
$categoryName = $row["CategoryName"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Edit Category</h2>

        <!-- Edit Category Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?categoryID=" . $categoryID; ?>" method="post">
            <div class="form-group">
                <label for="newCategoryName">New Category Name:</label>
                <input type="text" name="newCategoryName" class="form-control" value="<?php echo $categoryName; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary" name="updateCategory">Update Category</button>
            <a class="btn btn-outline-dark" href="view_categories.php">Cancel</a>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
