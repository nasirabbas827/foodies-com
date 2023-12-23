<?php
session_start();
include('config.php');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if the restaurantID is provided in the URL
if (!isset($_GET["restaurantID"])) {
    header("location: user_messages.php");
    exit;
}

$restaurantID = $_GET["restaurantID"];

// Fetch the restaurant details
$fetchRestaurantSql = "SELECT * FROM restaurants WHERE RestaurantID = ?";
$fetchRestaurantStmt = mysqli_prepare($conn, $fetchRestaurantSql);
mysqli_stmt_bind_param($fetchRestaurantStmt, "i", $restaurantID);
mysqli_stmt_execute($fetchRestaurantStmt);
$restaurantResult = mysqli_stmt_get_result($fetchRestaurantStmt);
$restaurant = mysqli_fetch_assoc($restaurantResult);

// Fetch the chat messages with the specific restaurant
$fetchChatSql = "SELECT * FROM chat_messages WHERE (userID = ? AND restaurantID = ?) OR (userID = ? AND restaurantID = ?) ORDER BY timestamp ASC";
$fetchChatStmt = mysqli_prepare($conn, $fetchChatSql);
mysqli_stmt_bind_param($fetchChatStmt, "iiii", $_SESSION['id'], $restaurantID, $restaurantID, $_SESSION['id']);
mysqli_stmt_execute($fetchChatStmt);
$chatResult = mysqli_stmt_get_result($fetchChatStmt);

// Handle sending a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $newMessage = $_POST['message'];

    // Fetch the logged-in user's username
    $fetchUsernameSql = "SELECT username FROM users WHERE id = ?";
    $fetchUsernameStmt = mysqli_prepare($conn, $fetchUsernameSql);
    mysqli_stmt_bind_param($fetchUsernameStmt, "i", $_SESSION['id']);
    mysqli_stmt_execute($fetchUsernameStmt);
    $usernameResult = mysqli_stmt_get_result($fetchUsernameStmt);
    $usernameRow = mysqli_fetch_assoc($usernameResult);
    $sender = $usernameRow['username'];

    // Insert the new message into the database
    $insertMessageSql = "INSERT INTO chat_messages (userID, restaurantID, sender, message, reply) VALUES (?, ?, ?, ?, NULL)";
    $insertMessageStmt = mysqli_prepare($conn, $insertMessageSql);
    mysqli_stmt_bind_param($insertMessageStmt, "iiss", $_SESSION['id'], $restaurantID, $sender, $newMessage);
    mysqli_stmt_execute($insertMessageStmt);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Chat with <?php echo $restaurant['RestaurantName']; ?></title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <h2>Chat with <?php echo $restaurant['RestaurantName']; ?></h2>
        <div class="row">

            <div class="col-md-8">
                <div id="chatMessages">
                    <?php while ($message = mysqli_fetch_assoc($chatResult)) : ?>
                        <div class="message-container">
                            <p><strong><?php echo $message['sender']; ?>:</strong> <?php echo $message['message']; ?></p>
                            <?php if ($message['reply'] !== null) : ?>
                                <p class="text-muted"><strong>Restaurant:</strong> <?php echo $message['reply']; ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="mt-3">
                    <form method="post">
                        <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type your message...">
                        <button type="submit" class="mt-2 btn btn-primary">Send</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
