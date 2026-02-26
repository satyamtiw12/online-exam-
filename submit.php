<?php
include("db.php");
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $exam_id = intval($_POST['exam_id']);
    $answers_json = $_POST['answers'] ?? '{}';
    $answers = json_decode($answers_json,true);
    if(!$answers) $answers = [];

    // ✅ Fetch questions using PDO
    $stmtQ = $conn->prepare("SELECT * FROM questions WHERE exam_id=:exam_id");
    $stmtQ->bindParam(':exam_id', $exam_id);
    $stmtQ->execute();
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

    $totalMarks = count($questions);
    $score = 0;

    foreach($questions as $row){
        $q_id = $row['id'];
        $correct = trim(strtoupper($row['correct_option']));
        $userAnswer = trim(strtoupper($answers[$q_id] ?? ''));

        if($userAnswer && $userAnswer === $correct){
            $score++;
        }
    }

    // ✅ Insert results using PDO
    $stmt = $conn->prepare("INSERT INTO results (user_id, exam_id, score, total_marks, submitted_at) 
                            VALUES (:user_id, :exam_id, :score, :total_marks, NOW())");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':exam_id', $exam_id);
    $stmt->bindParam(':score', $score);
    $stmt->bindParam(':total_marks', $totalMarks);
    $stmt->execute();

    header("Location: result.php?exam_id=".$exam_id);
    exit;
}
?>