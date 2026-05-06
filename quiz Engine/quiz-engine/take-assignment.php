<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: assignments.php");
    exit();
}

$assignment_id = (int)$_GET['id'];
$student_id = $_SESSION['student_id'];

// Check if already taken
$chk = $conn->query("SELECT id FROM assignment_results WHERE assignment_id = $assignment_id AND student_id = $student_id");
if ($chk->num_rows > 0) {
    echo "<script>alert('You have already submitted this assignment.'); window.location.href='assignments.php';</script>";
    exit();
}

$a_stmt = $conn->prepare("SELECT title, due_date FROM assignments WHERE id = ?");
$a_stmt->bind_param("i", $assignment_id);
$a_stmt->execute();
$a_res = $a_stmt->get_result();
if ($a_res->num_rows == 0) {
    die("Assignment not found.");
}
$assignment = $a_res->fetch_assoc();

// Check for expiration
if (strtotime($assignment['due_date']) < strtotime(date('Y-m-d'))) {
    echo "<script>alert('Error: The deadline for this assignment has passed.'); window.location.href='assignments.php';</script>";
    exit();
}

// Handle Submission
if (isset($_POST['submit_assignment_quiz'])) {
    $score = 0;
    $total = 0;
    
    // Evaluate Questions
    $q_res = $conn->query("SELECT id, correct FROM assignment_questions WHERE assignment_id = $assignment_id");
    while($q = $q_res->fetch_assoc()) {
        $total++;
        $ans = isset($_POST['q_'.$q['id']]) ? (int)$_POST['q_'.$q['id']] : 0;
        if ($ans == $q['correct']) {
            $score++;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO assignment_results (assignment_id, student_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $assignment_id, $student_id, $score, $total);
    $stmt->execute();
    
    header("Location: assignments.php?success=1");
    exit();
}

$questions = $conn->query("SELECT * FROM assignment_questions WHERE assignment_id = $assignment_id ORDER BY id ASC");
if ($questions->num_rows == 0) {
    echo "<script>alert('No questions available yet for this assignment.'); window.location.href='assignments.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($assignment['title']); ?> – Assignment Processor</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .question-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .question-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
        }
        .options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .option-label {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fdfdfd;
        }
        .option-label:hover {
            border-color: var(--primary);
            background: #f4f7fe;
        }
        .option-label input { margin-right: 12px; transform: scale(1.2); }
        .option-text { font-size: 0.95rem; }
    </style>
</head>
<body>
    <div class="top-banner">Online Examination Portal</div>
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

    <div class="container" style="max-width: 800px;">
        <div style="margin-bottom: 24px;">
            <a href="assignments.php" class="btn btn-outline" style="padding: 6px 14px; font-size: 0.85rem;">Cancel Assignment</a>
        </div>

        <div style="margin-bottom: 32px; border-bottom: 2px solid var(--accent-light); padding-bottom: 16px;">
            <h2 style="border: none; margin-bottom: 4px;"><?php echo htmlspecialchars($assignment['title']); ?></h2>
            <p style="color: var(--primary); font-weight: 700;">Please answer all <?php echo $questions->num_rows; ?> questions below before submitting.</p>
        </div>

        <form action="" method="POST" id="assignmentForm">
            <?php $q_num = 1; while($q = $questions->fetch_assoc()): ?>
                <div class="question-card">
                    <div class="question-text"><?php echo $q_num++; ?>. <?php echo htmlspecialchars($q['question']); ?></div>
                    <div class="options">
                        <?php for($i=1; $i<=4; $i++): ?>
                            <label class="option-label">
                                <input type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $i; ?>" required>
                                <span class="option-text"><?php echo htmlspecialchars($q['opt'.$i]); ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <div style="background: var(--card); padding: 24px; border-radius: var(--radius); border: 1px solid var(--border); text-align: center; margin-bottom: 40px; box-shadow: var(--shadow);">
                <h3 style="margin-bottom: 16px;">Ready to Submit?</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">Ensure you have answered all the questions above. You cannot retake this assignment.</p>
                <button type="submit" name="submit_assignment_quiz" class="btn btn-primary" style="padding: 14px 40px; font-size: 1.1rem; width: 100%;">Submit Final Answers</button>
            </div>
        </form>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
