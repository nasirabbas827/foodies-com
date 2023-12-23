<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all users from the database
$sql = "SELECT id, username, email, phone, age, user_type, status FROM users";
$result = mysqli_query($conn, $sql);

// Check if any users are found
if ($result) {
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Admin Users</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>

        <?php include('admin_navbar.php'); ?>

        <div class="container mt-5">
            <h2 class="text-center">Admin Users</h2>
            <a class=" float-right btn btn-outline-success m-3" href="add_users.php">Add Users</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Age</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phone']}</td>";
                        echo "<td>{$row['age']}</td>";
                        echo "<td>{$row['user_type']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td><a href='update_status.php?id={$row['id']}'>Update Status</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>

    </html>
    <?php

    // Free result set
    mysqli_free_result($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
