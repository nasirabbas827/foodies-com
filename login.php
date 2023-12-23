<?php
include('config.php');

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no errors, check credentials and log in user
    if (empty($email_err) && empty($password_err)) {
        $sql = "SELECT id, email, password, user_type, status FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password, $user_type, $status);
            if (mysqli_stmt_fetch($stmt)) {
                if ($status == 'approved') {  // Check if the user is approved
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start session and log in user
                        session_start();
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;

                        // Redirect based on user type
                        if ($user_type == "food_lover") {
                            header("location: ./user/user_dashboard.php");
                        } elseif ($user_type == "restaurant_owner") {
                            header("location: ./restaurant/resturant_dashboard.php");
                        } else {
                            // Handle other user types or provide a default redirection
                            header("location: index.php");
                        }
                    } else {
                        // Password is incorrect
                        $password_err = "The password you entered is incorrect.";
                    }
                } else {
                    // User is not approved
                    $email_err = "Your account is pending approval.";
                }
            }
        } else {
            // Email not found in database
            $email_err = "No account found with that email.";
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>User Login</title>
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
<?php
include('navbar.php');
?>
    <div class="container mt-5">
        <h2 class="text-center">User Login</h2>
        <p class="text-center">Please fill in your credentials to log in.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span><?php echo $password_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Log in" class="btn btn-primary">
            </div>
        </form>
        <p class="text-center">Have'nt any Account <a href="register.php">Register here</a></p>
        
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>

