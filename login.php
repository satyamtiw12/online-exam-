<?php
include("db.php");
session_start();

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // ✅ PDO prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row){
        if(password_verify($password, $row['password'])){
            // ✅ Set proper session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // 🔹 Redirect based on role
            if($row['role'] == 'admin'){
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Wrong Password ❌";
        }
    } else {
        $error = "User not found ❌";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-box">
        <a href="#" class="auth-brand d-flex justify-content-center mb-3">
           <img src="https://erp.mmumullana.org/assets/assets1/images/logo.webp" alt="dark logo" width="100%" class="logo-dark">                    
        </a>
        <h4 class="fw-semibold mb-1 fs-28 text-start">Welcome Back</h4>
        <h4 class="fw-normal mb-3 fs-16 text-start">Log in to your account</h4>

      <form id="loginForm" method="POST">
  
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" placeholder="Username / Roll No" required>

          <label>Password</label>
          <input type="password" name="password"
           placeholder="Password"
           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
           title="Password must be at least 8 characters including uppercase, lowercase, number and special character"
           required>

          <button type="submit" name="login">Login</button>

          <p class="signup-text">
            Don’t have an account?
            <a href="register.php">Register</a>
          </p>
          <p style="margin-top:10px;">
            <a href="forgot_password.php">Forgot Password?</a>
          </p>
      </form>

      <?php if (!empty($error)) { ?>
          <p style="color:red;"><?= $error ?></p>
      <?php } ?>

    </div>
  </div>
</body>
</html>
