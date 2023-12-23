<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the restaurantID is provided in the URL
if (!isset($_GET['restaurantID']) || empty($_GET['restaurantID'])) {
    header("location: view_restaurants.php");
    exit;
}

// Fetch restaurant details
$restaurantID = $_GET['restaurantID'];
$ownerID = $_SESSION["id"];

$sql = "SELECT * FROM restaurants WHERE RestaurantID = ? AND ownerID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $restaurantID, $ownerID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check if the restaurant exists
if (!$row) {
    header("location: view_restaurants.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newRestaurantName = $_POST["newRestaurantName"];
    $newLocation = $_POST["newLocation"];

    // Process and upload the new restaurant image
    $imageDirectory = "uploads/"; // Set your image upload directory
    $newImageName = basename($_FILES["newRestaurantImage"]["name"]);
    $newImagePath = $imageDirectory . $newImageName;
    $newImageFileType = strtolower(pathinfo($newImagePath, PATHINFO_EXTENSION));

    // Check if a new image is provided
    if (!empty($_FILES["newRestaurantImage"]["tmp_name"])) {
        $check = getimagesize($_FILES["newRestaurantImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            exit;
        }

        // Move the new image file
        move_uploaded_file($_FILES["newRestaurantImage"]["tmp_name"], $newImagePath);
    } else {
        // Use the existing image path if no new image is provided
        $newImagePath = $row['RestaurantImage'];
    }

    // Update restaurant details
    $updateSql = "UPDATE restaurants SET RestaurantName = ?, Location = ?, RestaurantImage = ? WHERE RestaurantID = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "sssi", $newRestaurantName, $newLocation, $newImagePath, $restaurantID);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    // Redirect to view_restaurants.php after update
    header("location: view_restaurants.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Restaurant</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Edit Restaurant</h2>

        <!-- Edit Restaurant Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?restaurantID=" . $restaurantID; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="newRestaurantName">New Restaurant Name:</label>
                <input type="text" name="newRestaurantName" class="form-control" value="<?php echo $row['RestaurantName']; ?>" required>
            </div>
            <div class="form-group">
                <label for="newLocation">New Location:</label>
                <input type="text" name="newLocation" class="form-control" value="<?php echo $row['Location']; ?>" required>
            </div>
            <div class="form-group">
                <label for="newRestaurantImage">New Restaurant Image:</label>
                <input type="file" name="newRestaurantImage" class="form-control-file" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
