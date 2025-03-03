<?php
session_start();
require_once('config.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $user = $_POST['login_username'];
    $pass = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['username'] = $user;
            header("Location: welcome.php");
            exit();
        } else {
            $error = "❌ Invalid credentials! Please check your password.";
        }
    } else {
        $error = "❌ Invalid credentials! Username not found.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #4a6ee0; --primary-hover: #3a5bc7; --error-color: #e74c3c; --shadow-color: rgba(0, 0, 0, 0.1); --background: #f8f9fa; --card-bg: #ffffff; --text-color: #333333; --text-light: #6c757d; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--background); height: 100vh; display: flex; justify-content: center; align-items: center; color: var(--text-color); }
        .container { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 8px 20px var(--shadow-color); width: 400px; padding: 40px; text-align: center; transition: transform 0.3s ease; }
        .container:hover { transform: translateY(-5px); }
        h2 { margin-bottom: 30px; font-weight: 600; color: var(--primary-color); }
        .error-message { background-color: rgba(231, 76, 60, 0.1); border-left: 4px solid var(--error-color); color: var(--error-color); padding: 12px; margin-bottom: 20px; text-align: left; border-radius: 4px; font-size: 14px; }
        .form-group { position: relative; margin-bottom: 24px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; }
        .input-with-icon { position: relative; }
        .input-with-icon i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-light); }
        .form-control { width: 100%; padding: 14px 14px 14px 40px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 15px; transition: all 0.3s ease; }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(74, 110, 224, 0.2); outline: none; }
        .btn { background-color: var(--primary-color); color: white; border: none; padding: 14px; width: 100%; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; margin-top: 10px; transition: background 0.3s ease; }
        .btn:hover { background-color: var(--primary-hover); }
        .link { margin-top: 25px; display: flex; flex-direction: column; gap: 10px; }
        .link p { font-size: 14px; color: var(--text-light); }
        .link a { color: var(--primary-color); text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        .link a:hover { color: var(--primary-hover); text-decoration: underline; }
        .separator { margin: 25px 0; display: flex; align-items: center; }
        .separator::before, .separator::after { content: ""; flex: 1; border-bottom: 1px solid #e0e0e0; }
        .separator span { padding: 0 10px; color: var(--text-light); font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome Back</h2>
        <?php if (!empty($error)): ?>
        <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="login_username">Username</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="login_username" name="login_username" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="login_password">Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="login_password" name="login_password" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="login" class="btn">Log In</button>
        </form>
        <div class="separator"><span>or</span></div>
        <div class="link">
            <p>Forgot your password? <a href="forgot_password.php">Reset here</a></p>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
