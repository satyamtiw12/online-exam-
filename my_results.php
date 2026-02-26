<?php
include("db.php");  // PDO connection file
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch active exams + user's results
$stmt = $conn->prepare("
    SELECT exams.exam_title,
           results.score,
           results.total_marks,
           results.submitted_at
    FROM exams
    LEFT JOIN results ON results.exam_id = exams.id AND results.user_id = :user_id
    WHERE exams.status = 'active'
    ORDER BY exams.id ASC
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{background:linear-gradient(135deg,#000,#fff);min-height:100vh;padding:40px;}
        .container{max-width:900px;margin:auto;}
        .title{color:white;margin-bottom:35px;font-size:32px;font-weight:700;text-align:center;}
        .card{background:white;padding:40px;border-radius:20px;box-shadow:0 15px 40px rgba(0,0,0,0.18);margin-bottom:25px;text-align:center;transition:0.3s ease;}
        .card:hover{transform:translateY(-6px);}
        .exam-table{width:100%;border-collapse: collapse;margin-bottom:30px;}
        .exam-table th,.exam-table td{padding:12px 15px;border:1px solid #ddd;font-size:16px;}
        .exam-table th{background:#007bff;color:white;font-weight:700;}
        .badge{padding:6px 14px;border-radius:20px;font-size:14px;font-weight:bold;}
        .pass{background:#d4edda;color:#155724;}
        .fail{background:#f8d7da;color:#721c24;}
        .button-group{margin-top:20px;display:flex;gap:20px;justify-content:center;}
        .button-group a{padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px;transition:0.3s ease;}
        .btn-blue{background-color:#007bff;color:white;}
        .btn-blue:hover{background-color:#0056b3;}
        .btn-grey{background-color:#dc3545;color:white;}
        .btn-grey:hover{background-color:#a71d2a;}
        .no-result-msg{font-size:18px;color:#666;padding:20px 0;}
    </style>
</head>
<body>

<div class="container">
    <div class="title">My Assessment Results</div>

    <div class="card">
        <?php
        if(count($exams) > 0){
            $hasAttempt = false;
            foreach($exams as $row){
                if(!is_null($row['score'])){
                    $hasAttempt = true;
                    break;
                }
            }

            if($hasAttempt){
                echo '<table class="exam-table">
                        <tr>
                            <th>Exam Name</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                            <th>Submitted On</th>
                        </tr>';

                foreach($exams as $row){
                    if(is_null($row['score'])) continue;

                    $exam = htmlspecialchars($row['exam_title']);
                    $score = $row['score'];
                    $total = $row['total_marks'];
                    $date = $row['submitted_at'];

                    $percentage = ($total > 0) ? ($score / $total) * 100 : 0;
                    $status = ($percentage >= 40) ? "PASS" : "FAIL";
                    $badge_class = ($percentage >= 40) ? "badge pass" : "badge fail";

                    echo "<tr>
                            <td>{$exam}</td>
                            <td>{$score} / {$total}</td>
                            <td>".round($percentage,2)."%</td>
                            <td><span class='{$badge_class}'>{$status}</span></td>
                            <td>".date("d M Y, h:i A", strtotime($date))."</td>
                        </tr>";
                }

                echo '</table>';
            } else {
                echo '<div class="no-result-msg">You have not attempted any exams yet.</div>';
            }

        } else {
            echo '<div class="no-result-msg">No exams available.</div>';
        }
        ?>

        <div class="button-group">
            <a href="dashboard.php" class="btn-blue">Go to Dashboard</a>
            <a href="logout.php" class="btn-grey">Logout</a>
        </div>
    </div>
</div>

</body>
</html>