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
    $restaurantName = $_POST["restaurantName"];
    $location = $_POST["location"];

    // Process and upload the restaurant image
    $imageDirectory = "uploads/"; // Set your image upload directory
    $imageName = basename($_FILES["restaurantImage"]["name"]);
    $imagePath = $imageDirectory . $imageName;
    $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

    // Check if the file is an image
    $check = getimagesize($_FILES["restaurantImage"]["tmp_name"]);
    if ($check !== false) {
        // Check if the image file already exists
        if (file_exists($imagePath)) {
            echo "Sorry, the image file already exists.";
        } else {
            // Upload the image file
            move_uploaded_file($_FILES["restaurantImage"]["tmp_name"], $imagePath);

            // Insert restaurant details into the database
            $sql = "INSERT INTO restaurants (ownerID, restaurantName, location, restaurantImage) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isss", $ownerID, $restaurantName, $location, $imagePath);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        echo "Resturant Details uploaded Successfully";

        }
    } else {
        echo "File is not an image.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Owner Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Welcome, Restaurant Owner!</h2>

        <!-- Restaurant Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="restaurantName">Restaurant Name:</label>
                <input type="text" name="restaurantName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="restaurantImage">Restaurant Image:</label>
                <input type="file" name="restaurantImage" class="form-control-file" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Restaurant</button>
            <a class="btn btn-outline-dark" href="view_restaurants.php">View Resaurants</a>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
