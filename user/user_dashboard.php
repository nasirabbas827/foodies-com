<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if a search query is submitted
if (isset($_GET['search'])) {
    $searchQuery = '%' . $_GET['search'] . '%';

    // Fetch restaurants from the database based on the search query
    $fetchRestaurantsSql = "SELECT * FROM restaurants WHERE RestaurantName LIKE ? OR Location LIKE ?";
    $fetchRestaurantsStmt = mysqli_prepare($conn, $fetchRestaurantsSql);
    mysqli_stmt_bind_param($fetchRestaurantsStmt, "ss", $searchQuery, $searchQuery);
    mysqli_stmt_execute($fetchRestaurantsStmt);
    $restaurantsResult = mysqli_stmt_get_result($fetchRestaurantsStmt);

    // Fetch feedbacks and calculate average ratings based on the search query
    $fetchFeedbacksSql = "SELECT f.*, u.Username, r.RestaurantName
                          FROM Feedbacks f
                          JOIN Users u ON f.UserID = u.id
                          JOIN Restaurants r ON f.RestaurantID = r.RestaurantID
                          WHERE r.RestaurantName LIKE ? OR r.Location LIKE ?";
    $fetchFeedbacksStmt = mysqli_prepare($conn, $fetchFeedbacksSql);
    mysqli_stmt_bind_param($fetchFeedbacksStmt, "ss", $searchQuery, $searchQuery);

    if ($fetchFeedbacksStmt) {
        mysqli_stmt_execute($fetchFeedbacksStmt);
        $feedbacksResult = mysqli_stmt_get_result($fetchFeedbacksStmt);
    } else {
        echo "Error in preparing the feedbacks statement: " . mysqli_error($conn);
    }
} else {
    // If no search query, fetch all restaurants and feedbacks
    $fetchRestaurantsSql = "SELECT * FROM restaurants";
    $fetchRestaurantsStmt = mysqli_prepare($conn, $fetchRestaurantsSql);
    mysqli_stmt_execute($fetchRestaurantsStmt);
    $restaurantsResult = mysqli_stmt_get_result($fetchRestaurantsStmt);

    $fetchFeedbacksSql = "SELECT f.*, u.Username, r.RestaurantName
                          FROM Feedbacks f
                          JOIN Users u ON f.UserID = u.id
                          JOIN Restaurants r ON f.RestaurantID = r.RestaurantID";
    $fetchFeedbacksStmt = mysqli_prepare($conn, $fetchFeedbacksSql);

    if ($fetchFeedbacksStmt) {
        mysqli_stmt_execute($fetchFeedbacksStmt);
        $feedbacksResult = mysqli_stmt_get_result($fetchFeedbacksStmt);
    } else {
        echo "Error in preparing the feedbacks statement: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Dashboard</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <!-- Add Font Awesome for star ratings -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <?php include('user_navbar.php'); ?>

    <div class="container">
        <!-- Search Bar -->
        <form class="form-inline mb-4 mt-4">
            <input class="form-control " type="search" placeholder="Search by Location or Name"
                aria-label="Search" name="search">
            <button class="btn btn-outline-success m-2" type="submit">Search</button>
        </form>

        <!-- Restaurants Section -->
        <h2>Restaurants</h2>
        <div class="row">
            <?php while ($restaurant = mysqli_fetch_assoc($restaurantsResult)) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="../restaurant/<?php echo $restaurant['RestaurantImage']; ?>" class="card-img-top"
                            alt="Restaurant Image">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $restaurant['RestaurantName']; ?></h5>
                            <p class="card-text"><?php echo $restaurant['Location']; ?></p>
                            <p class="card-text"><strong>Average Rating:</strong>
                                <?php
                                if (isset($averageRatings[$restaurant['RestaurantID']])) {
                                    $averageRating = $averageRatings[$restaurant['RestaurantID']];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $averageRating) {
                                            echo '<i class="fas fa-star" style="color: yellow;"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="color: yellow;"></i>';
                                        }
                                    }
                                } else {
                                    echo 'No ratings yet.';
                                }
                                ?>
                            </p>
                            <a href="view_menu_items.php?restaurantID=<?php echo $restaurant['RestaurantID']; ?>"
                                class="btn btn-primary">View Menu Items</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Feedbacks Section -->
        <h2>Feedbacks</h2>
        <div class="row">
            <?php
            if (isset($feedbacksResult)) {
                while ($feedback = mysqli_fetch_assoc($feedbacksResult)) :
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $feedback['RestaurantName']; ?></h5>
                                <p class="card-text"><strong>User:</strong> <?php echo $feedback['Username']; ?></p>
                                <p class="card-text"><strong>Rating:</strong>
                                    <?php
                                    $rating = $feedback['Rating'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star" style="color: yellow;"></i>';
                                        } else {
                                            echo '<i class="far fa-star" style="color: yellow;"></i>';
                                        }
                                    }
                                    ?>
                                </p>
                                <p class="card-text"><strong>Comment:</strong> <?php echo $feedback['Comment']; ?></p>
                            </div>
                        </div>
                    </div>
            <?php endwhile;
            } else {
                echo "Error: Feedbacks result is not set.";
            }
            ?>
        </div>
    </div>

    <!-- Add Bootstrap JS (jQuery and Popper.js are required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
