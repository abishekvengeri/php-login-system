<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('session_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
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
  </style>
</head>
<body>
  <div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <div class="link">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
