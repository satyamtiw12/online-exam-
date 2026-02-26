<?php
include("db.php");
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$now = date('Y-m-d H:i:s');

// 🔹 Expired exams ko inactive karo
mysqli_query($conn, "
    UPDATE exams 
    SET status='inactive' 
    WHERE expiry_date IS NOT NULL 
    AND expiry_date <= NOW()
");

// 🔹 Sirf active exams fetch karo
$sql = "SELECT * FROM exams WHERE status='active'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    display:flex;
    background:#f4f6f9;
}

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

/* Welcome Section */
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

/* Exam Cards */
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

.exam-info h3{
    font-size:20px;
    margin-bottom:8px;
}

.exam-info p{
    color:#555;
    font-size:14px;
    margin:3px 0;
}

.btn{
    padding:12px 22px;
    background:#667eea;
    border:none;
    border-radius:8px;
    color:white;
    cursor:pointer;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}

.btn:hover{
    background:#5563c1;
}

/* No Exam */
.no-exam{
    background:white;
    padding:40px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
    font-size:18px;
    color:#555;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Exam System</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">View Profile</a>
    <a href="my_results.php">My Results</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <!-- Welcome Section -->
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
        <p class="sub-text">Here are your available exams</p>
    </div>

<?php
$available_exam = false;

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){

        $exam_id = $row['id'];
        $exam_title = htmlspecialchars($row['exam_title']);
        $exam_date = $row['exam_date'];
        $expiry_date = $row['expiry_date'];
        $duration = $row['duration'];

        // Hide Attempted Exams
        $check = mysqli_query($conn,
            "SELECT id FROM results 
             WHERE user_id='$user_id' 
             AND exam_id='$exam_id'"
        );

        if(mysqli_num_rows($check) == 0){
            $available_exam = true;
?>
        <div class="card">
            <div class="exam-info">
                <h3><?php echo $exam_title; ?></h3>
                <p><strong>Date:</strong> <?php echo $exam_date; ?></p>
                <p><strong>Duration:</strong> <?php echo $duration; ?> Minutes</p>
                <?php if(!empty($expiry_date)) { ?>
                <p><strong>Expires On:</strong> <?php echo date('d M Y, H:i', strtotime($expiry_date)); ?></p>
                <?php } ?>
            </div>

            <a class="btn" href="instructions.php?exam_id=<?php echo $exam_id; ?>">
                Start Exam
            </a>
        </div>
<?php
        }
    }

    if(!$available_exam){
        echo '<div class="no-exam">No available exams right now.</div>';
    }

}else{
    echo '<div class="no-exam">No active exams available.</div>';
}
?>

</div>

</body>
</html>