<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$category = isset($_GET['category']) ? trim($_GET['category']) : 'General Knowledge';
$_SESSION['quiz_category'] = $category;

$stmt = $conn->prepare("SELECT * FROM questions WHERE category = ? ORDER BY RAND() LIMIT 10");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
} else {
    die("No questions found in the database. Please contact the administrator.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination – Quiz Engine</title>
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
        
        <!-- 5 Second Countdown Overlay -->
        <div id="countdown-overlay" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh;">
            <h2 style="font-size: 2rem; color: var(--primary); margin-bottom: 20px;">Get Ready!</h2>
            <div id="countdown-number" style="font-size: 6rem; font-weight: 900; color: #b8860b; background: white; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; border-radius: 50%; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 4px solid var(--primary);">5</div>
            <p style="margin-top: 20px; color: var(--text-muted); font-size: 1.1rem;">Your <?php echo htmlspecialchars($category); ?> examination will start shortly.</p>
        </div>

        <!-- Actual Quiz -->
        <div id="actual-quiz" class="card" style="display: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--accent-light);">
                <div>
                    <h2 style="margin-bottom: 2px; border-bottom: none;"><?php echo htmlspecialchars($category); ?> Examination</h2>
                    <p style="color: var(--text-muted); font-size: 0.88rem;">Answer all <?php echo count($questions); ?> questions. All answers are required before submitting.</p>
                </div>
                <div>
                    <div id="timer-display" class="timer">10:00</div>
                    <p style="font-size: 0.7rem; text-align: center; color: var(--danger); margin-top: 4px; font-weight: 600;">TIME REMAINING</p>
                </div>
            </div>

            <form id="quiz-form" action="result.php" method="POST">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-block" style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                        <p style="font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                            <span style="background: var(--primary); color: white; padding: 2px 10px; border-radius: 4px; font-size: 0.85rem; margin-right: 10px;">Q<?php echo ($index + 1); ?></span>
                            <?php echo htmlspecialchars($q['question']); ?>
                        </p>
                        <input type="hidden" name="question_ids[]" value="<?php echo $q['question_id']; ?>">

                        <?php foreach (['option1','option2','option3','option4'] as $i => $opt): ?>
                        <label class="quiz-option">
                            <input type="radio" name="answer_<?php echo $q['question_id']; ?>" value="<?php echo ($i+1); ?>" required>
                            <span style="font-size: 0.82rem; font-weight: 700; color: var(--accent); min-width: 20px;"><?php echo chr(65+$i); ?>.</span>
                            <?php echo htmlspecialchars($q[$opt]); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <button type="submit" name="submit_quiz" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 0.95rem;">
                    Submit Examination
                </button>
            </form>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
    <script src="js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
