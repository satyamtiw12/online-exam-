<?php
include("db.php");
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = intval($_GET['exam_id']);

// Get username
$user_query = mysqli_query($conn,"SELECT username FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Check if exam is active
$exam = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM exams WHERE id='$exam_id' AND status='active'"));
if(!$exam){
    die("Invalid or inactive exam.");
}

// Check if user already attempted
$check = mysqli_query($conn,"SELECT id FROM results WHERE user_id='$user_id' AND exam_id='$exam_id'");
if(mysqli_num_rows($check) > 0){
    die("You already attempted this exam.");
}

// Set total marks dynamically from questions
$total_questions = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM questions WHERE exam_id='$exam_id'"));
$total_marks = $total_questions['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Exam Instructions</title>
<style>
body{
    margin:0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background:#e5e5e5;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.container{
    height:auto;
    width:600px;
    background:#ffffff;
    padding:35px 40px;
    border-radius:10px;
    box-shadow:0 10px 30px rgba(0,0,0,0.15);
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}
.top-content{
    flex:1;
}
h3{
    margin:0 0 18px 0;
    font-size:22px;
    font-weight:600;
}
.exam-title{
    font-weight:600;
    font-size:18px;
    margin-bottom:15px;
}
.details{
    margin-bottom:18px;
    font-size:16px;
    line-height:1.6;
}
.rules{
    margin-top:10px;
}
.rules strong{
    font-size:17px;
}
.rules ul{
    padding-left:22px;
    margin-top:10px;
}
.rules ul li{
    margin-bottom:8px;
    font-size:15px;
}
.bottom-section{
    margin-top:15px;
}
.checkbox{
    font-size:15px;
    margin-bottom:12px;
}
.btn{
    width:180px;
    background:#4a90e2;
    color:white;
    padding:12px 20px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:16px;
    transition:0.3s;
}
.btn:hover{
    background:#2f75c1;
}
.btn:disabled{
    background:#a8a8a8;
    cursor:not-allowed;
}
</style>
<script>
function toggleButton(){
    var check = document.getElementById("agree");
    var btn = document.getElementById("startBtn");
    btn.disabled = !check.checked;
}

function startExam(){
    // Server-side check for attempt
    window.location.href = "exam.php?exam_id=<?php echo $exam_id; ?>";
}
</script>
</head>
<body>

<div class="container">
    <div class="top-content">
        <h3>Good Luck, <?php echo htmlspecialchars($user['username']); ?>!</h3>
        <div class="exam-title">[<?php echo htmlspecialchars($exam['exam_title']); ?>]</div>

        <div class="details">
            Duration: <?php echo htmlspecialchars($exam['duration']); ?> minutes<br>
            Total Marks: <?php echo $total_marks; ?>
        </div>

        <div class="rules">
            <strong>Instructions / Rules</strong>
            <ul>
                <li>All questions are compulsory.</li>
                <li>Do not refresh the page.</li>
                <li>Switching tabs will auto-submit the exam.</li>
                <li>Exam cannot be paused once started.</li>
            </ul>
        </div>
    </div>

    <div class="bottom-section">
        <div class="checkbox">
            <input type="checkbox" id="agree" onclick="toggleButton()">
            I agree to the Terms & Conditions
        </div>

        <button class="btn" id="startBtn" onclick="startExam()" disabled>
            Start Exam
        </button>
    </div>
</div>

</body>
</html>