<?php
include("db.php");
session_start();

// Admin check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

// Form submit
if(isset($_POST['add_question'])){
    $exam_id = intval($_POST['exam_id']);
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
    $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
    $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
    $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
    $correct_option = $_POST['correct_option'];

    $query = "INSERT INTO questions (exam_id, question, option_a, option_b, option_c, option_d, correct_option)
              VALUES ('$exam_id','$question','$option_a','$option_b','$option_c','$option_d','$correct_option')";

    if(mysqli_query($conn, $query)){
        $success = "Question added successfully ✅";
    } else {
        $error = "Error: ".mysqli_error($conn);
    }
}

// Fetch active exams
$exams = mysqli_query($conn, "SELECT * FROM exams WHERE status='active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Question</title>
<style>
/* Reset and body */
* {margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif;}
body {background:#f0f4f8; padding:20px;}

/* Container */
.container {
    max-width:700px;
    margin:0 auto;
}

/* Card */
.card {
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    margin-top:20px;
}

/* Headings */
.card h2 {
    color:#333;
    margin-bottom:20px;
    font-weight:700;
    text-align:center;
}

/* Inputs and select */
input, textarea, select {
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:16px;
}

/* Textarea */
textarea {
    resize:none;
    min-height:80px;
}

/* Buttons */
button {
    padding:12px 25px;
    background:#667eea;
    color:white;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
    transition:0.3s;
}
button:hover {
    background:#5563c1;
}

/* Success/Error messages */
.success {color:green; text-align:center; margin-bottom:15px;}
.error {color:red; text-align:center; margin-bottom:15px;}

/* Back button */
.back-btn{
    display:inline-block;
    margin-bottom:20px;
    padding:8px 15px;
    background:#5563c1;
    color:white;
    text-decoration:none;
    border-radius:6px;
    transition:0.3s;
}
.back-btn:hover{background:#4450a1;}
</style>
</head>
<body>
<div class="container">
    <!-- Back button -->
    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

    <!-- Card -->
    <div class="card">
        <h2>Add New Question</h2>

        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Exam:</label>
            <select name="exam_id" required>
                <option value="">Select Exam</option>
                <?php while($row = mysqli_fetch_assoc($exams)){ ?>
                    <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['exam_title']); ?></option>
                <?php } ?>
            </select>

            <label>Question:</label>
            <textarea name="question" required></textarea>

            <label>Option A:</label>
            <input type="text" name="option_a" required>
            <label>Option B:</label>
            <input type="text" name="option_b" required>
            <label>Option C:</label>
            <input type="text" name="option_c" required>
            <label>Option D:</label>
            <input type="text" name="option_d" required>

            <label>Correct Option:</label>
            <select name="correct_option" required>
                <option value="">Select Correct Option</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <button type="submit" name="add_question">Add Question</button>
        </form>
    </div>
</div>
</body>
</html>