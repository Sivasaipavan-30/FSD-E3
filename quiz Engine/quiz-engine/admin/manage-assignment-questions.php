<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage-assignments.php");
    exit();
}

$assignment_id = (int)$_GET['id'];

// Get info
$a_stmt = $conn->prepare("SELECT title FROM assignments WHERE id = ?");
$a_stmt->bind_param("i", $assignment_id);
$a_stmt->execute();
$a_res = $a_stmt->get_result();
if ($a_res->num_rows == 0) {
    die("Assignment not found.");
}
$assignment = $a_res->fetch_assoc();

if (isset($_POST['add_q'])) {
    $question = trim($_POST['question']);
    $opt1 = trim($_POST['opt1']);
    $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']);
    $opt4 = trim($_POST['opt4']);
    $correct = (int)$_POST['correct'];
    
    $stmt = $conn->prepare("INSERT INTO assignment_questions (assignment_id, question, opt1, opt2, opt3, opt4, correct) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $assignment_id, $question, $opt1, $opt2, $opt3, $opt4, $correct);
    if ($stmt->execute()) {
        $msg = "Question added to assignment successfully.";
    } else {
        $error = "Failed to add question.";
    }
}

$questions = $conn->query("SELECT * FROM assignment_questions WHERE assignment_id = $assignment_id ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignment Questions – Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-banner">Online Examination Portal – Admin</div>
    <nav class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage-questions.php">Questions</a>
            <a href="manage-assignments.php">Assignments</a>
            <a href="manage-students.php">Students</a>
            <a href="view-results.php">Results</a>
            <a href="manage-queries.php">Support</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
                <h2>Manage Assignment Questions</h2>
                <p style="color: var(--primary); font-weight: 700; font-size: 1.1rem; border-left: 3px solid var(--primary); padding-left: 12px;"><?php echo htmlspecialchars($assignment['title']); ?></p>
            </div>
            <a href="manage-assignments.php" class="btn btn-outline" style="padding: 8px 16px;">← Back</a>
        </div>

        <div class="card" style="margin-bottom: 24px;">
            <h3 style="border-bottom: none; font-size: 1.1rem; margin-bottom: 16px;">Add New Question</h3>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <?php if (isset($msg)) echo "<p class='pass'>$msg</p>"; ?>
            
            <form action="" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Question Text</label>
                    <textarea name="question" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="opt1" required>
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="opt2" required>
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="opt3" required>
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="opt4" required>
                </div>
                <div class="form-group">
                    <label>Correct Answer</label>
                    <select name="correct" required>
                        <option value="1">Option A</option>
                        <option value="2">Option B</option>
                        <option value="3">Option C</option>
                        <option value="4">Option D</option>
                    </select>
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" name="add_q" class="btn btn-primary" style="width: 100%; height: 42px;">+ Add Question</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3>Current Questions</h3>
            <?php if ($questions->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Correct Opt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; while($row = $questions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td style="font-size: 0.9rem;">
                                <strong><?php echo htmlspecialchars($row['question']); ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    A. <?php echo htmlspecialchars($row['opt1']); ?> | B. <?php echo htmlspecialchars($row['opt2']); ?> | C. <?php echo htmlspecialchars($row['opt3']); ?> | D. <?php echo htmlspecialchars($row['opt4']); ?>
                                </span>
                            </td>
                            <td style="font-weight: 700; color: var(--success); text-align: center;">Opt <?php echo $row['correct']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted);">No questions added to this assignment yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
