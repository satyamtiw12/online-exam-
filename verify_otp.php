<?php
include("db.php");
session_start();

if(!isset($_GET['email'])){
    die("Invalid Access");
}

$email = trim($_GET['email']);
$msg = "";

if(isset($_POST['verify_otp'])){

    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);
    $current_time = date("Y-m-d H:i:s");

    // ✅ PDO prepared statement
    $stmt = $conn->prepare("SELECT id FROM users 
                            WHERE email=:email AND otp=:otp AND otp_expiry > :current_time");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':otp', $otp);
    $stmt->bindParam(':current_time', $current_time);
    $stmt->execute();

    if($stmt->rowCount() == 1){

        // Clear OTP
        $clear = $conn->prepare("UPDATE users SET otp=NULL, otp_expiry=NULL WHERE email=:email");
        $clear->bindParam(':email', $email);
        $clear->execute();

        header("Location: reset_password.php?email=" . urlencode($email));
        exit();

    } else {
        $msg = "Invalid or Expired OTP ❌";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="styles.css">
        <style>
    /* ================= RESET ================= */
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    body, html{
        height:100%;
        font-family: Arial, sans-serif;
    }

    /* ================= BACKGROUND ================= */
    .login-wrapper{
        display:flex;
        justify-content:center;
        align-items:center;
        height:100vh;
        background-image:url('https://erp.mmumullana.org/assets/assets1/images/bg-auth.jpg');
        background-size:cover;
        background-position:center;
        background-repeat:no-repeat;
    }

    /* ================= AUTH CARD ================= */
    .auth-box{
        background:#ffffff;
        width:400px;
        padding:35px;
        border-radius:15px;
        text-align:center;
        box-shadow:0 15px 35px rgba(0,0,0,0.2);
        transition:0.3s;
    }

    .auth-box:hover{
        transform:translateY(-5px);
    }

    /* ================= LOGO ================= */
    .logo-dark{
        width:100%;
        max-width:230px;
        margin-bottom:20px;
    }

    /* ================= HEADINGS ================= */
    .auth-box h2{
        margin-bottom:20px;
        font-size:24px;
        color:#333;
    }

    /* ================= FORM ================= */
    .auth-box form{
        display:flex;
        flex-direction:column;
        gap:18px;  /* Input aur button ke beech spacing */
        margin-top:10px;
    }

    /* ================= INPUT ================= */
    .auth-box input{
        width:100%;
        padding:14px;
        border-radius:10px;
        border:1px solid #ddd;
        font-size:15px;
        transition:0.3s;
    }

    .auth-box input:focus{
        border-color:#1900ff;
        outline:none;
    }

    /* ================= BUTTON ================= */
    .auth-box button{
        width:100%;
        padding:14px;
        border-radius:10px;
        font-size:16px;
        font-weight:bold;
        cursor:pointer;
        /* background:linear-gradient(135deg,#1900ff,#6a11cb); */
        background:#1900ff;
        color:white;
        border:none;
        transition:0.3s ease;
    }

    .auth-box button:hover{
        transform:translateY(-2px);
        box-shadow:0 8px 20px rgba(0,0,0,0.2);
    }

    /* ================= MESSAGE ================= */
    .msg{
        margin-top:15px;
        font-weight:bold;
        color:red;
    }

    .msg.success{
        color:green;
    }

    /* ================= LINKS ================= */
    .signup-text{
        margin-top:18px;
    }

    .signup-text a{
        color:#4a6cf7;
        text-decoration:none;
        font-weight:600;
    }

    .signup-text a:hover{
        text-decoration:underline;
    }

    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="auth-box">
                   <img src="https://erp.mmumullana.org/assets/assets1/images/logo.webp" alt="dark logo" width="100%" class="logo-dark">                    



        <h2>Verify OTP</h2>

        


        <form method="POST">
    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit" name="verify_otp">Verify OTP</button>
</form>

<?php if(!empty($msg)){ ?>
    <p class="msg"><?= $msg ?></p>
<?php } ?>

        <p class="signup-text">
            <a href="login.php">Back to Login</a>
        </p>
    </div>
</div>
</body>
</html>



