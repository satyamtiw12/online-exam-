<?php
include("db.php");
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = intval($_GET['exam_id']);

// Get active exam
$exam = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM exams WHERE id='$exam_id' AND status='active'")
);
if(!$exam){
    echo "Invalid exam.";
    exit;
}

// Check if already attempted
$check = mysqli_query($conn,"SELECT id FROM results WHERE user_id='$user_id' AND exam_id='$exam_id'");
if(mysqli_num_rows($check) > 0){
    echo "Already Attempted.";
    exit;
}

$duration = $exam['duration'] * 60;

// Get questions
$questions = mysqli_query($conn,"SELECT * FROM questions WHERE exam_id='$exam_id'");
$totalQuestions = mysqli_num_rows($questions);
?>

<!DOCTYPE html>
<html>
<head>
<title>Exam Screen</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f2f2f2; margin:0;
}
.exam-header{
    background:#e9e9e9;
    padding:15px 25px;
    display:flex;
    justify-content:space-between; 
    align-items:center; 
    border-bottom:1px solid #ccc; 
    font-weight:bold;
}
.exam-container{
    width:750px; 
    margin:30px auto; 
    background:#fff; 
    padding:25px 35px; 
    border-radius:6px; 
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
}
.question-count{
    font-size:20px;
    font-weight:bold;
    margin-bottom:15px;
}
.question-text{
    font-size:18px; 
    margin-bottom:20px;
}
.option{
    margin:12px 0;
    font-size:16px;
}
.button-row{
    margin-top:25px; 
    display:flex; 
    justify-content:space-between;
}
.nav-btn{
    padding:8px 20px;
    border:1px solid #b5b5b5; 
    background:#f5f5f5; 
    border-radius:4px;
    cursor:pointer;
}
.nav-btn:hover{
    background:#e1e1e1;
}
.next-btn{
    background:#3a7bd5; 
    color:#fff; 
    border:none;
}
.next-btn:hover{
    background:#2c5fa3;
}
.submit-container{
    text-align:center;
    margin-top:20px;
}
.submit-btn{
    background:#3a7bd5; 
    color:#fff;
    padding:10px 30px;
    border:none; 
    border-radius:5px;
    font-size:16px;
    cursor:pointer;
}
.submit-btn:hover{
    background:#2c5fa3;
}
.modal{
    position:fixed; 
    top:0; left:0; 
    width:100%; 
    height:100%;
    background:rgba(0,0,0,0.5); 
    display:flex;
    align-items:center;
    justify-content:center; 
    visibility:hidden; 
    opacity:0; 
    transition:0.3s ease; 
    z-index:9999;
}
.modal.active{
    visibility:visible; 
    opacity:1;
}
.modal-box{
    background:#fff;
    padding:30px 40px; 
    border-radius:8px;
    text-align:center;
    width:350px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}
</style>

<script>
let timeLeft = <?php echo $duration; ?>;
let isSubmitting = false;
let questions = [];
let currentIndex = 0;
let answers = {};

function startTimer(){
    let timer = setInterval(function(){
        let minutes = Math.floor(timeLeft/60);
        let seconds = timeLeft % 60;
        document.getElementById("timer").innerHTML = minutes + ":" + (seconds<10?"0"+seconds:seconds);
        timeLeft--;
        if(timeLeft<0){ clearInterval(timer); submitExam("Time is up! Submitting exam..."); }
    },1000);
}

function showModal(message){
    document.getElementById("modalMessage").innerText = message;
    document.getElementById("submitModal").classList.add("active");
}

function submitExam(message){
    if(isSubmitting) return;
    isSubmitting = true;
    showModal(message);
    document.getElementById("answersInput").value = JSON.stringify(answers);
    setTimeout(function(){ document.getElementById("examForm").submit(); },1000);
}

function manualSubmit(){ submitExam("Submitting your exam..."); }

document.addEventListener("visibilitychange", function(){ 
    if(document.hidden && !isSubmitting){ submitExam("Tab switch detected. Auto submitting..."); } 
});
document.addEventListener("contextmenu", event => event.preventDefault());
history.pushState(null,null,location.href);
window.onpopstate = function(){ history.go(1); };

function renderQuestion(index){
    let q = questions[index];
    let container = document.getElementById('examContainer');
    container.innerHTML = `
        <div class="question-count">Question ${index+1}/${questions.length}</div>
        <div class="question-text">${q.question}</div>
        <div class="option"><label><input type="radio" name="q${q.id}" value="A" ${answers[q.id]=="A"?"checked":""}> ${q.option_a}</label></div>
        <div class="option"><label><input type="radio" name="q${q.id}" value="B" ${answers[q.id]=="B"?"checked":""}> ${q.option_b}</label></div>
        <div class="option"><label><input type="radio" name="q${q.id}" value="C" ${answers[q.id]=="C"?"checked":""}> ${q.option_c}</label></div>
        <div class="option"><label><input type="radio" name="q${q.id}" value="D" ${answers[q.id]=="D"?"checked":""}> ${q.option_d}</label></div>
        <div class="button-row">
            <button type="button" class="nav-btn" id="prevBtn">◀ Previous</button>
            <button type="button" class="nav-btn next-btn" id="nextBtn">Next ▶</button>
        </div>
        <div class="submit-container" ${index==questions.length-1?"":"style='display:none;'"} >
            <button type="button" class="submit-btn" onclick="manualSubmit()">Submit Exam</button>
        </div>
    `;
    let opts = container.querySelectorAll('input[type="radio"]');
    opts.forEach(opt=>{ opt.addEventListener('change',()=>{ answers[q.id] = opt.value; }); });
    document.getElementById('prevBtn').style.display = index==0?"none":"inline-block";
    document.getElementById('nextBtn').style.display = index==questions.length-1?"none":"inline-block";
    document.getElementById('prevBtn').onclick = ()=>{ currentIndex--; renderQuestion(currentIndex); }
    document.getElementById('nextBtn').onclick = ()=>{ currentIndex++; renderQuestion(currentIndex); }
}

<?php
mysqli_data_seek($questions,0);
$allQ = [];
while($row = mysqli_fetch_assoc($questions)){
    $allQ[] = [
        'id'=>$row['id'],
        'question'=>$row['question'],
        'option_a'=>$row['option_a'],
        'option_b'=>$row['option_b'],
        'option_c'=>$row['option_c'],
        'option_d'=>$row['option_d']
    ];
}
?>
questions = <?php echo json_encode($allQ); ?>;
window.onload = function(){ startTimer(); renderQuestion(0); };
</script>
</head>
<body>

<div class="exam-header">
    <div>Exam Screen</div>
    <div>Time Left: <span id="timer"></span></div>
</div>

<form id="examForm" action="submit.php" method="POST">
    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
    <input type="hidden" id="answersInput" name="answers">
    <div class="exam-container" id="examContainer"></div>
</form>

<div class="modal" id="submitModal">
    <div class="modal-box">
        <h3 id="modalMessage">Submitting...</h3>
    </div>
</div>

</body>
</html>