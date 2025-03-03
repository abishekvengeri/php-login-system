
<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('config.php');

// Initialize variables for error/success messages
$error_message   = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $error_message = "Database connection failed. Please try again later.";
    } else {
        // Get and sanitize user inputs
        $user         = trim(htmlspecialchars($_POST['reg_username']));
        $email        = trim(htmlspecialchars($_POST['reg_email']));
        $pass         = $_POST['reg_password'];
        $confirm_pass = $_POST['confirm_password'];
        
        // Validation
        if (strlen($user) < 3) {
            $error_message = "Username must be at least 3 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } elseif ($pass !== $confirm_pass) {
            $error_message = "Passwords do not match.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $pass)) {
            // Password must be at least 8 characters long and include one uppercase, one lowercase, one number, and one special character.
            $error_message = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.";
        } else {
            // Hash the password
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            
            // Check for existing username/email
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            if (!$stmt) {
                $error_message = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("ss", $user, $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error_message = "Username or email already exists!";
                } else {
                    // Insert new user into the database (without created_at column)
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    if (!$stmt) {
                        $error_message = "Prepare failed: " . $conn->error;
                    } else {
                        $stmt->bind_param("sss", $user, $email, $hashed_password);
                        if ($stmt->execute()) {
                            $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
                        } else {
                            $error_message = "Registration failed: " . $stmt->error;
                        }
                    }
                }
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Create Account</title>
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
         background-color: var(--light-color);
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
      .logo { margin-bottom: 25px; }
      h2 { color: var(--dark-color); margin-bottom: 30px; font-weight: 700; }
      .form-group { margin-bottom: 20px; position: relative; text-align: left; }
      .form-group label { display: block; margin-bottom: 8px; color: var(--dark-color); font-weight: 600; font-size: 14px; }
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
      .toggle-password {
         position: absolute;
         right: 15px;
         top: 41px;
         cursor: pointer;
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
      .btn:hover { background-color: #2e59d9; }
      .link { margin-top: 25px; font-size: 14px; color: var(--dark-color); }
      .link a { color: var(--primary-color); text-decoration: none; font-weight: 600; }
      .link a:hover { text-decoration: underline; }
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
      .alert-success {
         background-color: #d4edda;
         color: #155724;
         border: 1px solid #c3e6cb;
      }
      .password-policy {
         font-size: 12px;
         color: #777;
         margin-top: 5px;
         line-height: 1.4;
      }
   </style>
</head>
<body>
   <div class="container">
      <div class="logo">
         <!-- Optional: Place your logo here -->
      </div>
      <h2>Create Your Account</h2>
      <?php if (!empty($error_message)): ?>
         <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
         </div>
      <?php endif; ?>
      <?php if (!empty($success_message)): ?>
         <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
         </div>
      <?php endif; ?>
      <form method="post" id="registrationForm">
         <div class="form-group">
            <label for="reg_username">Username</label>
            <input type="text" class="form-control" id="reg_username" name="reg_username" required
               value="<?php echo isset($_POST['reg_username']) ? htmlspecialchars($_POST['reg_username']) : ''; ?>">
            <i class="fas fa-user"></i>
         </div>
         <div class="form-group">
            <label for="reg_email">Email Address</label>
            <input type="email" class="form-control" id="reg_email" name="reg_email" required
               value="<?php echo isset($_POST['reg_email']) ? htmlspecialchars($_POST['reg_email']) : ''; ?>">
            <i class="fas fa-envelope"></i>
         </div>
         <div class="form-group" style="position: relative;">
            <label for="reg_password">Password</label>
            <input type="password" class="form-control" id="reg_password" name="reg_password" required>
            <i class="fas fa-lock"></i>
            <span class="toggle-password" onclick="togglePassword('reg_password')">
               <i class="fas fa-eye"></i>
            </span>
            <p class="password-policy">
               Password must be at least 8 characters long,<br>
               contain at least one uppercase letter,<br>
               one lowercase letter, one digit, and one special character.
            </p>
         </div>
         <div class="form-group" style="position: relative;">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            <i class="fas fa-lock"></i>
            <span class="toggle-password" onclick="togglePassword('confirm_password')">
               <i class="fas fa-eye"></i>
            </span>
         </div>
         <button type="submit" name="register" class="btn">Register</button>
      </form>
      <div class="link">
         <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
   </div>
   <script>
      function togglePassword(fieldId) {
         var passwordField = document.getElementById(fieldId);
         var toggleIcon = document.querySelector('#' + fieldId + ' + .toggle-password i');
         if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
         } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
         }
      }
   </script>
</body>
</html>
