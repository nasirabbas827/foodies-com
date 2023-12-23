<?php
include('config.php');
session_start();

// Check if the restaurant owner is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: ../index.php");
    exit;
}

// Check if the restaurantID is provided in the URL
if (!isset($_GET["restaurantID"])) {
    header("location: select_restaurant.php"); // Redirect to the select restaurant page or any appropriate page
    exit;
}

$restaurantID = $_GET["restaurantID"];

// Fetch the restaurant details
$fetchRestaurantSql = "SELECT * FROM restaurants WHERE RestaurantID = ? AND OwnerID = ?";
$fetchRestaurantStmt = mysqli_prepare($conn, $fetchRestaurantSql);
mysqli_stmt_bind_param($fetchRestaurantStmt, "ii", $restaurantID, $_SESSION["id"]);
mysqli_stmt_execute($fetchRestaurantStmt);
$restaurantResult = mysqli_stmt_get_result($fetchRestaurantStmt);

// Check if the restaurant with the specified ID belongs to the logged-in owner
if (mysqli_num_rows($restaurantResult) === 0) {
    header("location: select_restaurant.php"); // Redirect if the restaurant doesn't belong to the owner
    exit;
}

$restaurant = mysqli_fetch_assoc($restaurantResult);

// Fetch all chat messages with the specific restaurant and their replies
$fetchChatSql = "SELECT m.id, m.sender, m.message, m.timestamp, m.reply 
                 FROM chat_messages m
                 WHERE m.restaurantID = ?
                 ORDER BY m.timestamp ASC";
$fetchChatStmt = mysqli_prepare($conn, $fetchChatSql);
mysqli_stmt_bind_param($fetchChatStmt, "i", $restaurantID);
mysqli_stmt_execute($fetchChatStmt);
$chatResult = mysqli_stmt_get_result($fetchChatStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Chat - <?php echo $restaurant['RestaurantName']; ?></title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Add this CSS to your existing stylesheet or create a new one */

.container {
    margin-top: 20px;
}

#chatMessages {
    max-width: 600px;
    margin: 0 auto;
}

.chat-container {
    display: flex;
    flex-direction: column;
}

.message {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.message-content {
    padding: 10px;
    border-radius: 8px;
    max-width: 70%;
}

.sender {
    font-weight: bold;
    margin-bottom: 5px;
}

.timestamp {
    font-size: 12px;
    color: #888;
}

.text {
    word-wrap: break-word;
}

.reply {
    display: flex;
    justify-content: flex-end;
    margin-top: 5px;
}

.reply-text {
    padding: 5px;
    border-radius: 8px;
    background-color: #e6e6e6;
    max-width: 70%;
}

textarea {
    width: 100%;
    margin-bottom: 5px;
}

.btn-primary {
    margin-top: 5px;
}

    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mb-5">
    <h2>Chat with Users</h2>
    <p>Restaurant: <?php echo $restaurant['RestaurantName']; ?></p>
    <div id="chatMessages">
        <div class="chat-container">
            <?php while ($message = mysqli_fetch_assoc($chatResult)) : ?>
                <div class="message <?php echo ($message['sender'] == 'restaurant') ? 'restaurant' : 'user'; ?>">
                    <div class="message-content">
                        <p class="sender"><?php echo ucfirst($message['sender']); ?></p>
                        <p class="text"><?php echo $message['message']; ?></p>
                        <p class="timestamp"><?php echo $message['timestamp']; ?></p>
                    </div>
                    <div class="reply">
                        <?php if (empty($message['reply'])) : ?>
                            <form method="post" action="">
                                <input type="hidden" name="messageID" value="<?php echo $message['id']; ?>">
                                <textarea name="reply" placeholder="Reply to this message"></textarea>
                                <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                            </form>
                        <?php else : ?>
                            <p class="reply-text"><?php echo $message['reply']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>


    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Handle replying to messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply']) && isset($_POST['messageID'])) {
    $replyMessage = $_POST['reply'];
    $messageID = $_POST['messageID'];

    // Insert the reply into the database
    $insertReplySql = "UPDATE chat_messages SET reply = ? WHERE id = ?";
    $insertReplyStmt = mysqli_prepare($conn, $insertReplySql);
    mysqli_stmt_bind_param($insertReplyStmt, "si", $replyMessage, $messageID);
    mysqli_stmt_execute($insertReplyStmt);
}
?>
