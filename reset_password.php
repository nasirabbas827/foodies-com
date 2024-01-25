<?php
include('config.php');

// Validate the token from the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT id, email FROM users WHERE reset_token = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $id, $email);
        mysqli_stmt_fetch($stmt);

        // Token is valid, allow the user to reset the password
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            $updateSql = "UPDATE users SET password = ?, reset_token = NULL WHERE id = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "si", $newPassword, $id);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);

            // Redirect to the login page after successful password reset
            header("location: login.php");
            exit();
        }
    } else {
        // Invalid or expired token
        echo "Invalid or expired token.";
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    // Token not provided
    echo "Token not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("https://images.unsplash.com/photo-1640340434855-6084b1f4901c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=464&q=80");
            background-size: cover;
        }
        h2 , p , label {
            color:white;
        }
        label{
            font-size: 20px;
        }
        span{
            color: red;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center">Reset Password</h2>
        <p class="text-center">Enter your new password.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?token=<?php echo $token; ?>" method="post">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Reset Password" class="btn btn-primary">
            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
