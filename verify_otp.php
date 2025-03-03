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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $otp = $_POST['otp'];
    $email = $_SESSION['reset_email'];

    $stmt = $conn->prepare("SELECT otp_expiry FROM users WHERE email = ? AND otp_code = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (strtotime($row['otp_expiry']) >= time()) {
            header("Location: reset_password.php");
            exit();
        } else {
            $error_message = "OTP expired.";
        }
    } else {
        $error_message = "Invalid OTP.";
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify OTP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <style>
    :root {
      --primary-color: #4e73df;
      --secondary-color: #1cc88a;
      --dark-color: #5a5c69;
      --light-color: #f8f9fc;
      --danger-color: #e74a3b;
      --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background-color: #f8f9fc;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      background-color: white;
      border-radius: 10px;
      box-shadow: var(--box-shadow);
      width: 100%;
      max-width: 450px;
      padding: 40px;
      text-align: center;
    }
    h2 {
      color: var(--dark-color);
      margin-bottom: 30px;
      font-weight: 700;
    }
    .form-group {
      margin-bottom: 20px;
      position: relative;
      text-align: left;
    }
    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--dark-color);
      font-weight: 600;
      font-size: 14px;
    }
    .form-control {
      width: 100%;
      padding: 12px 15px 12px 40px;
      border: 1px solid #e3e6f0;
      border-radius: 5px;
      font-size: 16px;
      transition: border-color 0.3s;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .form-group i {
      position: absolute;
      left: 15px;
      top: 41px;
      color: #b7b9cc;
    }
    .btn {
      width: 100%;
      padding: 12px;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 10px;
    }
    .btn:hover {
      background-color: #2e59d9;
    }
    .link {
      margin-top: 25px;
      font-size: 14px;
      color: var(--dark-color);
    }
    .link a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 600;
    }
    .link a:hover {
      text-decoration: underline;
    }
    .alert {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-size: 14px;
      text-align: left;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Verify OTP</h2>
    <?php if (!empty($error_message)): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
      </div>
    <?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label for="otp">Enter OTP</label>
        <i class="fas fa-key"></i>
        <input type="text" id="otp" name="otp" class="form-control" required>
      </div>
      <input type="submit" name="verify_otp" value="Verify OTP" class="btn">
    </form>
    <div class="link">
      <p>Didn't receive OTP? <a href="forgot_password.php">Resend OTP</a></p>
    </div>
  </div>
</body>
</html


