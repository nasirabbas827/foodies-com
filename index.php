<?php
include('config.php');
session_start();

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
<html>
<head>
    <title>Foodies.com </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        /* Custom CSS styles */
        body {
            background-color: aquamarine;

        }
        .course-card {
            margin-bottom: 20px;
        }
        /* Style for the carousel */
        .carousel-item {
            height: 500px; 
            position: relative;
        }
        .carousel-caption {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            padding: 20px;
        }
    /* Add linear gradient overlay */
    .gradient-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0) 100%);
    }
</style>
</head>
<body>
    <?php include('navbar.php'); ?>
 
    <div id="carouselExample" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselExample" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExample" data-slide-to="1"></li>
        <li data-target="#carouselExample" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="./images/Pic1.png" class="d-block w-100" alt="Tour Slide 1">
            <div class="gradient-overlay"></div>  
            <div class="carousel-caption">
                <h3>Welcome to Foodies.com</h3>
                <p>Discover a world of restaurants, menus, and orders on our platform.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./images/Pic2.jpg" class="d-block w-100" alt="Tour Slide 2">
            <div class="gradient-overlay"></div>  
            <div class="carousel-caption">
                <h3>Explore Diverse Menus and Culinary Delights</h3>
                <p>Indulge in a variety of cuisines and dishes from our curated selection of restaurants.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="./images/Pic3.jpg" class="d-block w-100" alt="Tour Slide 3">
            <div class="gradient-overlay"></div>  
            <div class="carousel-caption">
                <h3>Order with Ease and Enjoy Delicious Meals</h3>
                <p>Make every meal special with easy ordering and delightful culinary experiences.</p>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>



 
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
                        <img src="./restaurant/<?php echo $restaurant['RestaurantImage']; ?>" class="card-img-top"
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


<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; <?php echo date("Y"); ?> Foodies.com . All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
