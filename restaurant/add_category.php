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
    $ownerID = $_SESSION["id"];
    $categoryName = $_POST["categoryName"];

    // Insert category details into the database
    $sql = "INSERT INTO categories (UserID, CategoryName) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $ownerID, $categoryName);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "Category Added Successfully";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Add Category</h2>

        <!-- Add Category Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- No need to select a specific restaurant -->
            <div class="form-group">
                <label for="categoryName">Category Name:</label>
                <input type="text" name="categoryName" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Category</button>
            <a class="btn btn-outline-dark" href="view_category.php">View Categories</a>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
