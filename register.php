<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

include "db.php";
session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "❌ Invalid Email Format";
    }

    // Strong password validation
    elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
        $msg = "❌ Password must be at least 8 characters, include Uppercase, Lowercase, Number and Special Character";
    }

    else {
        try {
            // Check existing username or email
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $msg = "❌ Username or Email already exists";
            } else {
                // Hash password
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt2 = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $stmt2->bindParam(':username', $username);
                $stmt2->bindParam(':email', $email);
                $stmt2->bindParam(':password', $hash);

                if ($stmt2->execute()) {
                    $msg = "✅ Registration successful";
                    // Optionally, auto login
                    $_SESSION['username'] = $username;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $msg = "❌ Registration failed";
                }
            }
        } catch(PDOException $e){
            $msg = "❌ Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="styles.css">
</head>
<style>
  



.login-text a {
    color: rgb(125, 125, 235);
    border: none;
    background-color: transparent;
    font-size: 1rem;
    font-weight: bold;
}

/* mobile */
@media(max-width:600px){
  .login-box{
    max-width:95%;
    padding:24px;
  }

  .mmdu-logo{
    max-width:320px;
  }
}


</style>
<body>
<div class="login-wrapper">
  <div class="login-box">

 <img src="https://erp.mmumullana.org/assets/assets1/images/logo.webp" alt="dark logo" width="100%" class="logo-dark">                    
         

    <h2>Create Account</h2>
    <p class="subtitle"><h4> </h4></p>

    
        
      <form method="POST">

  <label>Username</label>
  <input type="text" name="username" placeholder="Username / Roll No" required>

  <label>Email</label>
  <input type="email" name="email"  placeholder="Email" required>

  <label>Password</label>
 <input type="password" name="password"
placeholder="Password"
pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$"
title="Password must be at least 8 characters including uppercase, lowercase, number and special character"
required>

  <button type="submit" name="register">Register</button>

</form>




    <?php if (!empty($msg)) { ?>
  <p style="color:green;">
    <?= $msg ?>
  </p>
<?php } ?>


    
    <p  class="login-text" style="margin-top:15px">
      Already have an account?
      <a href="login.php">Login</a>
    </p>

  </div>
</div>
</body>
</html>



