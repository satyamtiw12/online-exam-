<?php

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("db.php");

date_default_timezone_set("Asia/Kolkata");

$msg = "";

if(isset($_POST['send_otp'])){

    $email = trim($_POST['email']);

    // ✅ Check if email exists (PDO)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if($stmt->rowCount() == 1){

        // Generate OTP
        $otp = rand(100000,999999);
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // ✅ Update OTP in DB (PDO)
        $update = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $update->execute([$otp, $expire, $email]);

        if($update->rowCount() > 0){

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                 $mail->Username = 'satyamtiwari098765@gmail.com';
            $mail->Password = 'ekpdlreuxoishdgc';   // safer
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom(getenv('EMAIL_USER'), 'Auth System');
                $mail->addAddress($email);

                $mail->Subject = 'Password Reset OTP';
                $mail->Body    = "Your OTP is: $otp\nValid for 5 minutes.";

                $mail->send();

                header("Location: verify_otp.php?email=" . urlencode($email));
                exit();

            } catch (Exception $e) {
                $msg = "Mailer Error: " . $mail->ErrorInfo;
            }

        } else {
            $msg = "OTP not stored ❌";
        }

    } else {
        $msg = "Email not found ❌";
    }
}
?>


<!-- <!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
 
<h2>Enter Your Email to Reset Password</h2>

<form method="POST">
    <input type="email" name="email" placeholder="Enter Email" required>
    <button type="submit" name="send_otp">Send OTP</button>
</form>

</body>
</html> -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

        <img src="https://erp.mmumullana.org/assets/assets1/images/logo.webp"
             alt="University Logo"
             class="logo-dark">

        <h2>Forgot Password</h2>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="send_otp">Send OTP</button>
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

