<?php
include('config.php');
session_start();

if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Function to get the name of a restaurant based on its ID
function getRestaurantName($conn, $restaurantID)
{
    $fetchRestaurantNameSql = "SELECT RestaurantName FROM Restaurants WHERE RestaurantID = ?";
    $fetchRestaurantNameStmt = mysqli_prepare($conn, $fetchRestaurantNameSql);
    mysqli_stmt_bind_param($fetchRestaurantNameStmt, "i", $restaurantID);
    mysqli_stmt_execute($fetchRestaurantNameStmt);
    $restaurantNameResult = mysqli_stmt_get_result($fetchRestaurantNameStmt);

    if ($restaurantName = mysqli_fetch_assoc($restaurantNameResult)) {
        return $restaurantName['RestaurantName'];
    } else {
        return "N/A";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderID = $_POST["orderID"];
    $restaurantID = $_POST["restaurantID"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    // Insert feedback into the database (Feedbacks table)
    $insertFeedbackSql = "INSERT INTO Feedbacks (UserID, RestaurantID, Rating, Comment) 
                          VALUES (?, ?, ?, ?)";
    $insertFeedbackStmt = mysqli_prepare($conn, $insertFeedbackSql);
    mysqli_stmt_bind_param($insertFeedbackStmt, "iiis", $_SESSION["id"], $restaurantID, $rating, $comment);
    mysqli_stmt_execute($insertFeedbackStmt);
    mysqli_stmt_close($insertFeedbackStmt);

    // Update order status to 'Feedback Provided'
    $updateOrderSql = "UPDATE Orders SET OrderStatus = 'Feedback Provided' WHERE OrderID = ?";
    $updateOrderStmt = mysqli_prepare($conn, $updateOrderSql);
    mysqli_stmt_bind_param($updateOrderStmt, "i", $orderID);
    mysqli_stmt_execute($updateOrderStmt);
    mysqli_stmt_close($updateOrderStmt);

    // Display success message
    $successMessage = "Feedback submitted successfully!";
}

// If the form is not submitted or there's an error, display the feedback form
$orderID = isset($_GET["orderID"]) ? $_GET["orderID"] : (isset($orderID) ? $orderID : '');
$restaurantID = isset($_GET["restaurantID"]) ? $_GET["restaurantID"] : (isset($restaurantID) ? $restaurantID : '');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provide Feedback</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <h2>Provide Feedback</h2>

        <?php if (isset($successMessage)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php else : ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="orderID" value="<?php echo $orderID; ?>">
                <input type="hidden" name="restaurantID" value="<?php echo $restaurantID; ?>">
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="number" name="rating" class="form-control" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="comment">Your Comment:</label>
                    <textarea name="comment" class="form-control" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
