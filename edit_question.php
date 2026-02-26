<?php
include("db.php");
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ die("Access Denied"); }

$error = $success = "";
$id = intval($_GET['id']);
$question = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM questions WHERE id='$id'"));

if(!$question){ die("Question not found"); }

if(isset($_POST['update_question'])){
    $exam_id = intval($_POST['exam_id']);
    $qtext = mysqli_real_escape_string($conn,$_POST['question']);
    $a = mysqli_real_escape_string($conn,$_POST['option_a']);
    $b = mysqli_real_escape_string($conn,$_POST['option_b']);
    $c = mysqli_real_escape_string($conn,$_POST['option_c']);
    $d = mysqli_real_escape_string($conn,$_POST['option_d']);
    $correct = $_POST['correct_option'];

    $query = "UPDATE questions SET exam_id='$exam_id', question='$qtext', option_a='$a', option_b='$b', option_c='$c', option_d='$d', correct_option='$correct' WHERE id='$id'";
    if(mysqli_query($conn,$query)){
        $success = "Question updated successfully ✅";
        $question = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM questions WHERE id='$id'"));
    } else { $error = mysqli_error($conn); }
}

// Fetch exams
$exams = mysqli_query($conn,"SELECT * FROM exams WHERE status='active'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Question</title>
<style>
body{font-family:Arial; padding:20px; background:#f4f6f9;}
form{background:white; padding:20px; border-radius:10px; max-width:600px;}
input, textarea, select{width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc;}
button{padding:12px 20px; background:#667eea; color:white; border:none; border-radius:5px; cursor:pointer;}
button:hover{background:#5563c1;}
.success{color:green;} .error{color:red;}
</style>
</head>
<body>
<h2>Edit Question</h2>
<?php if($success) echo "<p class='success'>$success</p>"; ?>
<?php if($error) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
<label>Exam:</label>
<select name="exam_id" required>
<option value="">Select Exam</option>
<?php while($row = mysqli_fetch_assoc($exams)){ ?>
<option value="<?= $row['id']; ?>" <?= $row['id']==$question['exam_id']?'selected':''; ?>><?= htmlspecialchars($row['exam_title']); ?></option>
<?php } ?>
</select>

<label>Question:</label>
<textarea name="question" required><?= htmlspecialchars($question['question']); ?></textarea>
<label>Option A:</label>
<input type="text" name="option_a" value="<?= htmlspecialchars($question['option_a']); ?>" required>
<label>Option B:</label>
<input type="text" name="option_b" value="<?= htmlspecialchars($question['option_b']); ?>" required>
<label>Option C:</label>
<input type="text" name="option_c" value="<?= htmlspecialchars($question['option_c']); ?>" required>
<label>Option D:</label>
<input type="text" name="option_d" value="<?= htmlspecialchars($question['option_d']); ?>" required>
<label>Correct Option:</label>
<select name="correct_option" required>
<option value="A" <?= $question['correct_option']=='A'?'selected':''; ?>>A</option>
<option value="B" <?= $question['correct_option']=='B'?'selected':''; ?>>B</option>
<option value="C" <?= $question['correct_option']=='C'?'selected':''; ?>>C</option>
<option value="D" <?= $question['correct_option']=='D'?'selected':''; ?>>D</option>
</select>

<button type="submit" name="update_question">Update Question</button>
</form>
</body>
</html>