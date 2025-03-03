<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('config.php');
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_SESSION['reset_email'];

    $stmt = $conn->prepare("UPDATE users SET password = ?, otp_code = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $new_pass, $email);

    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        echo "<p class='alert alert-success' style='text-align:center;'>Password updated! <a href='login.php'>Login</a></p>";
        exit();
    } else {
        $error_message = "Error updating password.";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        body {
            background-color: #f4f7fc;
            font-family: 'Roboto', sans-serif;
            color: #444;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 500;
            font-size: 16px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 2px solid #ddd;
            border-radius: 5px;
            margin-top: 5px;
            transition: 0.3s;
        }

        input[type="password"]:focus {
            border-color: #4e73df;
            outline: none;
        }

        .show-password {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #4e73df;
        }

        .alert {
            color: #e74a3b;
            text-align: center;
            font-size: 16px;
            margin: 20px 0;
        }

        .alert-success {
            color: #1cc88a;
        }

        .submit-btn {
            background-color: #4e73df;
            color: #fff;
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #3e63b5;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .back-link a {
            color: #4e73df;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>

        <?php if (!empty($error_message)): ?>
            <p class="alert"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
                <i class="fas fa-eye show-password" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="reset_password" class="submit-btn">Reset Password</button>
        </form>

        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById('new_password');
            var passwordIcon = document.querySelector('.show-password');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

