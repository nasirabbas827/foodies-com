<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Delete feedback if feedbackID is provided in the query string
if (isset($_GET["feedbackID"])) {
    $feedbackID = $_GET["feedbackID"];
    $deleteFeedbackSql = "DELETE FROM Feedbacks WHERE RatingID = ?";
    $deleteFeedbackStmt = mysqli_prepare($conn, $deleteFeedbackSql);
    mysqli_stmt_bind_param($deleteFeedbackStmt, "i", $feedbackID);
    
    if(mysqli_stmt_execute($deleteFeedbackStmt)) {
        echo '<script>alert("Feedback deleted successfully.");</script>';
    } else {
        echo '<script>alert("Error deleting feedback.");</script>';
    }
    
    mysqli_stmt_close($deleteFeedbackStmt);
}

// Fetch all feedbacks
$fetchFeedbacksSql = "SELECT * FROM Feedbacks";
$fetchFeedbacksStmt = mysqli_prepare($conn, $fetchFeedbacksSql);
mysqli_stmt_execute($fetchFeedbacksStmt);
$feedbacksResult = mysqli_stmt_get_result($fetchFeedbacksStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('admin_navbar.php'); ?>
    <div class="container">
        <h2>All Feedbacks</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>RatingID</th>
                    <th>User</th>
                    <th>Restaurant</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($feedback = mysqli_fetch_assoc($feedbacksResult)) : ?>
                    <tr>
                        <td><?php echo $feedback['RatingID']; ?></td>
                        <td><?php echo getUsername($conn, $feedback['UserID']); ?></td>
                        <td><?php echo getRestaurantName($conn, $feedback['RestaurantID']); ?></td>
                        <td><?php echo $feedback['Rating']; ?></td>
                        <td><?php echo $feedback['Comment']; ?></td>
                        <td>
                            <a href="?feedbackID=<?php echo isset($feedback['RatingID']) ? $feedback['RatingID'] : ''; ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this feedback?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Function to get the username based on the user ID
function getUsername($conn, $userID)
{
    $fetchUsernameSql = "SELECT Username FROM Users WHERE id = ?";
    $fetchUsernameStmt = mysqli_prepare($conn, $fetchUsernameSql);
    mysqli_stmt_bind_param($fetchUsernameStmt, "i", $userID);
    mysqli_stmt_execute($fetchUsernameStmt);
    $usernameResult = mysqli_stmt_get_result($fetchUsernameStmt);

    if ($username = mysqli_fetch_assoc($usernameResult)) {
        return $username['Username'];
    } else {
        return "N/A";
    }
}

// Function to get the restaurant name based on the restaurant ID
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
?>
