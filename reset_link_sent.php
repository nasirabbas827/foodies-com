<!DOCTYPE html>
<html>

<head>
    <title>Password Reset Link Sent</title>
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
        <h2 class="text-center">Password Reset Link Sent</h2>
        <p class="text-center">An email with instructions to reset your password has been sent to your email address.
            Please follow the instructions in the email to reset your password.</p>
        <p class="text-center"><a href="login.php">Back to Login</a></p>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
