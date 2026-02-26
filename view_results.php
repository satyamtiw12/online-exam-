<?php
include("db.php");
session_start();

// Check admin login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Delete result if 'delete' parameter is set
if(isset($_GET['delete'])){
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM results WHERE id = :id");
    $stmt->bindParam(':id', $del_id);
    $stmt->execute();
    header("Location: view_results.php"); // Refresh the page
    exit();
}

// Fetch all results
$sql = "SELECT r.id, u.username, e.exam_title, r.score, r.total_marks, r.submitted_at
        FROM results r
        JOIN users u ON r.user_id = u.id
        JOIN exams e ON r.exam_id = e.id
        ORDER BY r.submitted_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Results</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 25px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}
table {
    width: 100%;
    border-collapse: collapse;
}
table th, table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
table th {
    background: #667eea;
    color: white;
    font-weight: 600;
}
table tr:nth-child(even) {
    background: #f9f9f9;
}
table tr:hover {
    background: #f1f1f1;
}
.back-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background: #667eea;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
}
.back-btn:hover {
    background: #5563c1;
}
.delete-btn {
    padding: 6px 12px;
    background: #e74c3c;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.delete-btn:hover {
    background: #c0392b;
}
</style>
</head>
<body>

<div class="container">
    <h2>Student Exam Results</h2>

    <table>
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Exam</th>
        <th>Score</th>
        <th>Total Marks</th>
        <th>Submitted At</th>
        <th>Action</th>
    </tr>

    <?php foreach($results as $row) { ?>
    <tr>
        <td><?= $row['id']; ?></td>
        <td><?= htmlspecialchars($row['username']); ?></td>
        <td><?= htmlspecialchars($row['exam_title']); ?></td>
        <td><?= $row['score']; ?></td>
        <td><?= $row['total_marks']; ?></td>
        <td><?= $row['submitted_at']; ?></td>
        <td>
            <a class="delete-btn" href="view_results.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this result?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

    <a class="back-btn" href="admin_dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>