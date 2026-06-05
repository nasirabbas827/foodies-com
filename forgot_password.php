<?php
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

// Define variables and initialize with empty values
$email = $email_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // If no errors, generate a reset token and send an email to the user
    if (empty($email_err)) {
        $token = bin2hex(random_bytes(16));

        $sql = "UPDATE users SET reset_token = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $token, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Send email with reset link
        $resetLink = "http://localhost/foodiesprototype/reset_password.php?token=$token";
        
        try {
            $mail = new PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'nasiryt.827@gmail.com'; // Replace with your SMTP username
            $mail->Password = "YOUR_OWN_API_KEY"; // Replace with your SMTP password
            $mail->Port = 587; // Replace with your SMTP port (usually 587)

            // Email content
            $mail->setFrom('nasiryt.827@gmail.com', 'NASIR ABBAS'); // Replace with your email and name
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset';
            $mail->Body = 'Click the following link to reset your password: ' . $resetLink;

            // Send the email
            $mail->send();

            // Redirect to a page indicating that the reset link has been sent
            header("location: reset_link_sent.php");
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
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
        <h2 class="text-center">Forgot Password</h2>
        <p class="text-center">Enter your email to receive a password reset link.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Reset Password" class="btn btn-primary">
            </div>
        </form>
        <p class="text-center"><a href="login.php">Back to Login</a></p>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
