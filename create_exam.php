<?php
include("db.php");
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    die("Access Denied");
}

if(isset($_POST['create'])){

    $exam_title = mysqli_real_escape_string($conn, $_POST['exam_title']);
    $exam_date  = $_POST['exam_date'];
    $duration   = intval($_POST['duration']);
    $expiry_raw = $_POST['expiry_date'];

    // Convert datetime-local to MySQL DATETIME format
    $expiry_date = date("Y-m-d H:i:s", strtotime($expiry_raw));

    // Validation: expiry must be future
    if(strtotime($expiry_date) <= time()){
        $error = "Expiry date must be in the future!";
    } else {

        mysqli_query($conn,"INSERT INTO exams 
            (exam_title, exam_date, duration, status, expiry_date) 
            VALUES 
            ('$exam_title','$exam_date','$duration','active','$expiry_date')
        ");

        header("Location: admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Exam</title>
<style>
body{
    font-family: Arial;
    background:#f4f6f9;
    padding:30px;
}
.container{
    max-width:500px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}
input, button{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:6px;
    border:1px solid #ccc;
}
button{
    background:#667eea;
    color:white;
    border:none;
    font-weight:600;
    cursor:pointer;
}
button:hover{
    background:#5563c1;
}
a.back-btn{
    display:inline-block;
    margin-top:10px;
    padding:10px 20px;
    background:#667eea;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
a.back-btn:hover{
    background:#5563c1;
}
.error{
    color:red;
    font-weight:600;
}
</style>
</head>
<body>

<div class="container">
<h2>Create New Exam</h2>

<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">

    <label>Exam Title</label>
    <input type="text" name="exam_title" placeholder="Exam Title" required>

    <label>Exam Date</label>
    <input type="date" name="exam_date" required>

    <label>Duration (Minutes)</label>
    <input type="number" name="duration" placeholder="Duration in Minutes" required>

    <label>Expiry Date & Time</label>
    <input type="datetime-local" name="expiry_date" required>

    <button type="submit" name="create">Create Exam</button>

</form>

<a class="back-btn" href="admin_dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>