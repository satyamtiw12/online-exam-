<?php
include("db.php");  // PDO connection
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){ 
    die("Access Denied"); 
}

// Delete question
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: manage_questions.php");
    exit;
}

// Fetch all questions
$stmt = $conn->prepare("SELECT * FROM questions ORDER BY id DESC");
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Questions</title>
<style>
body{
    font-family: Arial;
    padding: 20px;
    background: #f4f6f9;
}

table{
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px; 
    overflow: hidden;
}

th, td{
    padding: 12px; 
    border-bottom: 1px solid #ddd; 
    text-align: left;
}

th{
    background: #667eea; 
    color: white;
}

a.btn{
    padding: 6px 12px; 
    background: #667eea;
    color: white; 
    text-decoration: none;
    border-radius: 5px;
    text-align: center;
}

a.btn:hover{
    background: #5563c1;
}

.back-btn{
    display: inline-block;
    padding: 10px 20px;
    background: #e61c43;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: 0.3s;
}

.back-btn:hover{
    background: #ff1b6d;
}

.header-actions{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

/* Vertical action buttons */
.action-buttons {
    display: flex;
    flex-direction: column; 
    gap: 5px; 
    align-items: flex-start;
}

td .action-buttons a{
    width: 70px;
}
</style>
</head>
<body>
<h2>Manage Questions</h2>

<div class="header-actions">
    <a href="add_question.php" class="btn">Add New Question</a>
    <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

<table>
<tr>
<th>ID</th>
<th>Exam ID</th>
<th>Question</th>
<th>A</th>
<th>B</th>
<th>C</th>
<th>D</th>
<th>Answer</th>
<th>Actions</th>
</tr>

<?php foreach($questions as $row){ ?>
<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['exam_id']; ?></td>
<td><?= htmlspecialchars($row['question']); ?></td>
<td><?= htmlspecialchars($row['option_a']); ?></td>
<td><?= htmlspecialchars($row['option_b']); ?></td>
<td><?= htmlspecialchars($row['option_c']); ?></td>
<td><?= htmlspecialchars($row['option_d']); ?></td>
<td><?= $row['correct_option']; ?></td>
<td>
    <div class="action-buttons">
        <a class="btn" href="edit_question.php?id=<?= $row['id']; ?>">Edit</a>
        <a class="btn" href="manage_questions.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </div>
</td>
</tr>
<?php } ?>
</table>

</body>
</html>