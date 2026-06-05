<?php
include('config.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Initialize variables
$successMessage = "";

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addOffer'])) {
        $restaurantID = $_POST['restaurantID'];
        $offerDescription = $_POST['offerDescription'];

        // Get restaurant details
        $getRestaurantSql = "SELECT RestaurantName, Location FROM restaurants WHERE RestaurantID = ?";
        $getRestaurantStmt = mysqli_prepare($conn, $getRestaurantSql);
        mysqli_stmt_bind_param($getRestaurantStmt, "i", $restaurantID);
        mysqli_stmt_execute($getRestaurantStmt);
        $resultRestaurant = mysqli_stmt_get_result($getRestaurantStmt);
        $rowRestaurant = mysqli_fetch_assoc($resultRestaurant);
        $restaurantName = $rowRestaurant['RestaurantName'];
        $restaurantLocation = $rowRestaurant['Location'];
        mysqli_stmt_close($getRestaurantStmt);

        // Insert the offer into the database
        $insertSql = "INSERT INTO offers (restaurantID, offerDescription) VALUES (?, ?)";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($insertStmt, "is", $restaurantID, $offerDescription);
        mysqli_stmt_execute($insertStmt);
        mysqli_stmt_close($insertStmt);

        // Get the list of food_lover users
        $getUsersSql = "SELECT email FROM users WHERE user_type = 'food_lover'";
        $result = mysqli_query($conn, $getUsersSql);

        while ($row = mysqli_fetch_assoc($result)) {
            $foodLoverEmail = $row['email'];

            // Send email to food_lover users with the offer
            $mail = new PHPMailer(true);
            // Configure PHPMailer for sending email (use your own SMTP details)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'nasiryt.827@gmail.com'; // Replace with your SMTP username
            $mail->Password = "YOUR_OWN_API_KEY"; // Replace with your SMTP password
            $mail->Port = 587; // Replace with your SMTP port (usually 587)

            // Email content
            $mail->setFrom('nasiryt.827@gmail.com', 'NASIR ABBAS');
            $mail->addAddress($foodLoverEmail);
            $mail->Subject = 'New Offer Available';
            $mail->Body = "Check out the new offer at $restaurantName, located in $restaurantLocation! Offer details: $offerDescription";
            $mail->send();
        }

        $successMessage = "Offer email has been sent successfully!";
    }
}

// Fetch the restaurants added by the current restaurant owner
$ownerID = $_SESSION["id"];
$sql = "SELECT * FROM restaurants WHERE ownerID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $ownerID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Owner Dashboard - Add Offers</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h2>Add Offers to Your Restaurants</h2>

        <?php
        // Display success message if set
        if (!empty($successMessage)) {
            echo '<div class="alert alert-success" role="alert">' . $successMessage . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Select Restaurant:</label>
                <select name="restaurantID" class="form-control" required>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['RestaurantID']}'>{$row['RestaurantName']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Offer Description:</label>
                <textarea name="offerDescription" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="addOffer" class="btn btn-primary">Add Offer</button>
            </div>
        </form>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
