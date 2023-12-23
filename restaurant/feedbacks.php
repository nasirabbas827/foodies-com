<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Fetch feedbacks for the restaurants owned by the logged-in owner
$fetchFeedbacksSql = "SELECT f.*, r.RestaurantName
                      FROM Feedbacks f
                      JOIN Restaurants r ON f.RestaurantID = r.RestaurantID
                      WHERE r.OwnerID = ?";
$fetchFeedbacksStmt = mysqli_prepare($conn, $fetchFeedbacksSql);
mysqli_stmt_bind_param($fetchFeedbacksStmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($fetchFeedbacksStmt);
$feedbacksResult = mysqli_stmt_get_result($fetchFeedbacksStmt);
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
        <h2>Your Restaurants' Feedbacks</h2>

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
                        <td><?php echo $feedback['RestaurantName']; ?></td>
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
?>
