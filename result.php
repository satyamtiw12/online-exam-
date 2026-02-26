<?php
include("db.php");
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$exam_id = intval($_GET['exam_id']);
$user_id = $_SESSION['user_id'];

// ✅ Fetch last result using PDO
$stmt = $conn->prepare("SELECT * FROM results WHERE user_id=:user_id AND exam_id=:exam_id ORDER BY id DESC LIMIT 1");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':exam_id', $exam_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Fetch exam title
$stmt2 = $conn->prepare("SELECT exam_title FROM exams WHERE id=:exam_id");
$stmt2->bindParam(':exam_id', $exam_id);
$stmt2->execute();
$exam = $stmt2->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Result Screen</title>
<style>
body{
    margin:0; 
    font-family:Arial, Helvetica, sans-serif; 
    background:#dcdcdc;
}

.window-bar{
    background:#e6e6e6;
    padding:12px; 
    text-align:center;
    font-weight:bold; 
    border-bottom:1px solid #c5c5c5;
}
.result-box{
    width:650px;
    margin:40px auto;
    background:#fff; 
    padding:30px 40px;
    border:1px solid #cfcfcf;
}
.exam-title{
    font-weight:bold;
    margin-bottom:25px;
}
.score{
    font-size:20px;
    margin-bottom:8px;
}
.message{
    color:#444; 
    margin-bottom:30px;
}
.button-group{
    display:flex; 
    gap:15px;
}
.btn-blue{
    background:#4a90e2; 
    color:white; 
    padding:8px 20px; 
    border:1px solid #357ab8; 
    cursor:pointer; 
    text-decoration:none;
    font-size:14px;
}
.btn-blue:hover{
    background:#357ab8;
}
.btn-grey{
    background:#b5b5b5; 
    color:white; 
    padding:8px 20px;
    border:1px solid #8e8e8e; 
    cursor:pointer;
    text-decoration:none; 
    font-size:14px;
}
.btn-grey:hover{
    background:#8e8e8e;
}
</style>
</head>
<body>

<div class="window-bar">Result Screen</div>
<div class="result-box">
    <div class="exam-title">[<?= htmlspecialchars($exam['exam_title'] ?? 'Exam Title'); ?>]</div>
    <div class="score">Your Score: <?= $row['score'] ?? 0; ?> / <?= $row['total_marks'] ?? 0; ?></div>
    <div class="message">Well done! Check the dashboard for performance.</div>
    <div class="button-group">
        <a href="dashboard.php" class="btn-blue">Go to Dashboard</a>
        <a href="logout.php" class="btn-grey">Logout</a>
    </div>
</div>
</body>
</html>