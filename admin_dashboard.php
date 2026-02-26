<?php
include("db.php");
session_start();

// ✅ Admin check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Current timestamp
$now = date('Y-m-d H:i:s');

// Fetch stats
$total_questions = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM questions"));
$total_exams = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM exams"));
$total_students = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='student'"));

// Fetch active exams count
$active_exams = mysqli_num_rows(mysqli_query($conn, 
    "SELECT id FROM exams WHERE status='active' AND expiry_date >= '$now'"
));

// Delete student result (if delete requested)
if(isset($_GET['delete_result'])){
    $res_id = intval($_GET['delete_result']);
    mysqli_query($conn,"DELETE FROM results WHERE id='$res_id'");
    header("Location: admin_dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
*{margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif;}
body{display:flex; background:#f4f6f9; min-height:100vh;}

/* Sidebar */
.sidebar{
    width:250px;
    height:100vh;
    background:linear-gradient(180deg,#141e30,#243b55);
    padding:25px;
    color:white;
    position:fixed;
}
.sidebar h2{
    margin-bottom:30px; 
    font-weight:600;
}
.sidebar a{
    display:block;
    color:white; 
    text-decoration:none;
    margin:15px 0;
    padding:10px; 
    border-radius:6px; 
    transition:0.3s;
}
.sidebar a:hover{
    background:rgba(255,255,255,0.2);
}

/* Main Content */
.main{
    margin-left:250px;
    padding:40px; 
    flex:1;
}
.dashboard-header{
    background:white;
    padding:25px; 
    border-radius:15px; 
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    margin-bottom:30px;
}
.dashboard-header h1{
    font-size:28px; 
    font-weight:700; 
    margin-bottom:5px;
}
.sub-text{
    color:#666; 
    font-size:16px;
}

/* Cards */
.card{
    background:white;
    padding:25px;
    border-radius:15px; 
    box-shadow:0 8px 20px rgba(0,0,0,0.08); 
    margin-bottom:20px; 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
    transition:0.3s;
}
.card:hover{
    transform:translateY(-5px);
}
.card h3{
    font-size:20px; 
    margin-bottom:8px;
}
.card p{
    font-size:16px;
    margin:3px 0;
}
.card span{
    font-weight:700;
}
.btn{
    padding:12px 22px; 
    border:none; 
    border-radius:8px; 
    color:white; 
    cursor:pointer; 
    text-decoration:none; 
    font-weight:600; 
    transition:0.3s;
}
.btn-questions{background:#28a745;} /* green */
.btn-exams{background:#007bff;} /* blue */
.btn-students{background:#ffc107;} /* yellow */
.btn-results{background:#17a2b8;} /* teal */
.btn:hover{opacity:0.9;}

/* Results Table */
table{
    width:100%; 
    border-collapse:collapse; 
    margin-top:20px; 
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}
th, td{
    padding:12px; 
    text-align:center; 
    border-bottom:1px solid #ddd;
}
th{background:#667eea; color:white;}
tr:nth-child(even){background:#f9f9f9;}
tr:hover{background:#f1f1f1;}
.delete-btn{
    background:#dc3545; 
    padding:6px 12px; 
    border-radius:5px; 
    color:white; 
    text-decoration:none;
    font-weight:600;
}
.delete-btn:hover{opacity:0.8;}
</style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="add_question.php">Add Question</a>
    <a href="manage_questions.php">Manage Questions</a>
    <a href="create_exam.php">Create Exam</a>
    <a href="view_results.php">View Results</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
        <p class="sub-text">Admin Panel Overview</p>
    </div>

    <!-- Cards -->
    <div class="card">
        <div>
            <h3>Total Questions</h3>
            <p><span><?php echo $total_questions; ?></span></p>
        </div>
        <a class="btn btn-questions" href="manage_questions.php">Manage</a>
    </div>

    <div class="card">
        <div>
            <h3>Total Exams</h3>
            <p><span><?php echo $total_exams; ?></span> | Active: <?php echo $active_exams; ?></p>
        </div>
        <a class="btn btn-exams" href="create_exam.php">Create / Manage</a>
    </div>

    <div class="card">
        <div>
            <h3>Total Students</h3>
            <p><span><?php echo $total_students; ?></span></p>
        </div>
        <a class="btn btn-students" href="view_results.php">View Results</a>
    </div>

    <!-- Recent Results -->
    <h2 style="margin-top:30px;">Recent Results</h2>
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
        <?php
        $results = mysqli_query($conn,
            "SELECT r.id, u.username, e.exam_title, r.score, r.total_marks, r.submitted_at
            FROM results r
            JOIN users u ON r.user_id = u.id
            JOIN exams e ON r.exam_id = e.id
            ORDER BY r.submitted_at DESC LIMIT 10"
        );
        while($row = mysqli_fetch_assoc($results)){
        ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['exam_title']); ?></td>
            <td><?= $row['score']; ?></td>
            <td><?= $row['total_marks']; ?></td>
            <td><?= $row['submitted_at']; ?></td>
            <td>
                <a class="delete-btn" href="admin_dashboard.php?delete_result=<?= $row['id']; ?>" onclick="return confirm('Delete this result?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>