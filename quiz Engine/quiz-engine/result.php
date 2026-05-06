<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['submit_quiz'])) {
    header("Location: dashboard.php");
    exit();
}

$student_id      = $_SESSION['student_id'];
$question_ids    = $_POST['question_ids'];
$score           = 0;
$total_questions = count($question_ids);

$stmt_q = $conn->prepare("SELECT correct_answer FROM questions WHERE question_id = ?");

foreach ($question_ids as $q_id) {
    $q_id        = (int)$q_id;
    $user_answer = isset($_POST['answer_' . $q_id]) ? (int)$_POST['answer_' . $q_id] : 0;

    $stmt_q->bind_param("i", $q_id);
    $stmt_q->execute();
    $res = $stmt_q->get_result();
    $row = $res->fetch_assoc();

    if ($row && $user_answer === (int)$row['correct_answer']) {
        $score++;
    }
}

$category = isset($_SESSION['quiz_category']) ? $_SESSION['quiz_category'] : 'General Knowledge';
$stmt_r = $conn->prepare("INSERT INTO results (student_id, score, total_questions, category) VALUES (?, ?, ?, ?)");
$stmt_r->bind_param("iiis", $student_id, $score, $total_questions, $category);
$stmt_r->execute();
$result_id = $conn->insert_id;

$percentage = ($score / $total_questions) * 100;
$pass       = $percentage >= 60;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Result – Online Examination Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-banner">
        Online Examination Portal
    </div>
    <nav class="navbar">
        <div class="logo">Online Examination Portal</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="assignments.php">Assignments</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="profile.php">My Profile</a>
            <a href="help.php">Help</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card result-summary">

            <h2 style="border-bottom: none; text-align: center; margin-bottom: 24px;"><?php echo htmlspecialchars($category ?? 'General Knowledge'); ?> Examination Result</h2>

            <div class="score-circle">
                <span class="score-value"><?php echo $score; ?>/<?php echo $total_questions; ?></span>
                <span style="font-size: 1rem; color: var(--text-muted); font-weight: 700;"><?php echo number_format($percentage, 1); ?>%</span>
            </div>

            <h3 style="font-size: 1.25rem; text-transform: none; letter-spacing: 0; text-align: center; margin-bottom: 12px;" class="<?php echo $pass ? 'pass' : 'fail'; ?>">
                <?php echo $pass ? '🎓 Congratulations! You have Passed.' : '✘ You did not Pass this Examination.'; ?>
            </h3>

            <p style="color: var(--text-muted); margin-bottom: 2rem; text-align: center; font-size: 0.95rem;">
                <?php if ($pass): ?>
                    You have successfully completed the examination with <strong><?php echo number_format($percentage,1); ?>%</strong>. Your certificate of achievement is ready to download.
                <?php else: ?>
                    You need a minimum of <strong>60%</strong> to pass and earn a certificate. You scored <strong><?php echo number_format($percentage,1); ?>%</strong>. Please review the material and try again.
                <?php endif; ?>
            </p>

            <div class="actions" style="justify-content: center;">
                <?php if ($pass): ?>
                    <a href="certificate.php?id=<?php echo $result_id; ?>" class="btn btn-success">🏆 Download Certificate</a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
