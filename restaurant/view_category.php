<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the form is submitted for deleting a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deleteCategory"])) {
    $categoryID = $_POST["categoryID"];

    // Delete category from the database
    $deleteSql = "DELETE FROM categories WHERE CategoryID = ?";
    $deleteStmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($deleteStmt, "i", $categoryID);
    mysqli_stmt_execute($deleteStmt);
    mysqli_stmt_close($deleteStmt);
    echo "Category deleted successfully.";
}

// Fetch user's categories
$ownerID = $_SESSION["id"];
$fetchCategoriesSql = "SELECT CategoryID, CategoryName FROM categories WHERE UserID = ?";
$fetchCategoriesStmt = mysqli_prepare($conn, $fetchCategoriesSql);
mysqli_stmt_bind_param($fetchCategoriesStmt, "i", $ownerID);
mysqli_stmt_execute($fetchCategoriesStmt);
$categoriesResult = mysqli_stmt_get_result($fetchCategoriesStmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Categories</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Your Categories</h2>

        <!-- Display Categories -->
        <table class="table">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($categoriesResult)) { ?>
                    <tr>
                        <td><?php echo $row['CategoryID']; ?></td>
                        <td><?php echo $row['CategoryName']; ?></td>
                        <td><a class="btn btn-info" href="edit_category.php?categoryID=<?php echo $row['CategoryID']; ?> ">Edit</a></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                <input type="hidden" name="categoryID" value="<?php echo $row['CategoryID']; ?>">
                                <button type="submit" class="btn btn-danger" name="deleteCategory">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
