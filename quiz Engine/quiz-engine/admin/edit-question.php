<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage-questions.php");
    exit();
}
$question_id = (int)$_GET['id'];

// Initial fetch
$stmt = $conn->prepare("SELECT * FROM questions WHERE question_id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Question not found.");
}
$q_row = $res->fetch_assoc();

if (isset($_POST['update_question'])) {
    $category = trim($_POST['category']);
    $question = trim($_POST['question']);
    $opt1 = trim($_POST['opt1']);
    $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']);
    $opt4 = trim($_POST['opt4']);
    $correct = (int)$_POST['correct'];

    $stmt = $conn->prepare("UPDATE questions SET category=?, question=?, option1=?, option2=?, option3=?, option4=?, correct_answer=? WHERE question_id=?");
    $stmt->bind_param("ssssssii", $category, $question, $opt1, $opt2, $opt3, $opt4, $correct, $question_id);

    if ($stmt->execute()) {
        header("Location: manage-questions.php?msg=Question+updated+successfully");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question – Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-banner">
        Online Examination Portal – Admin
    </div>
    <nav class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage-questions.php">Questions</a>
            <a href="manage-categories.php">Categories</a>
            <a href="manage-assignments.php">Assignments</a>
            <a href="manage-students.php">Students</a>
            <a href="view-results.php">Results</a>
            <a href="manage-queries.php">Support</a>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container" style="max-width: 640px;">
        <div class="card">
            <h2>Edit Question</h2>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Quiz Category</label>
                    <input type="text" name="category" list="category-list" value="<?php echo htmlspecialchars($q_row['category']); ?>" placeholder="Enter or select category" required style="padding: 10px; font-size: 0.95rem;">
                    <datalist id="category-list">
                        <?php
                        $cats = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
                        while($c = $cats->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($c['category_name']) . "'>";
                        }
                        ?>
                    </datalist>
                </div>
                
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea name="question" rows="3" required><?php echo htmlspecialchars($q_row['question']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="opt1" value="<?php echo htmlspecialchars($q_row['option1']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="opt2" value="<?php echo htmlspecialchars($q_row['option2']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="opt3" value="<?php echo htmlspecialchars($q_row['option3']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="opt4" value="<?php echo htmlspecialchars($q_row['option4']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Correct Answer</label>
                    <select name="correct" required>
                        <option value="1" <?php if($q_row['correct_answer']==1) echo 'selected'; ?>>Option A</option>
                        <option value="2" <?php if($q_row['correct_answer']==2) echo 'selected'; ?>>Option B</option>
                        <option value="3" <?php if($q_row['correct_answer']==3) echo 'selected'; ?>>Option C</option>
                        <option value="4" <?php if($q_row['correct_answer']==4) echo 'selected'; ?>>Option D</option>
                    </select>
                </div>
                <div class="actions">
                    <button type="submit" name="update_question" class="btn btn-primary" style="width: 100%; padding: 12px;">Update Question</button>
                    <a href="manage-questions.php" class="btn btn-outline" style="color: var(--text-muted); border-color: var(--border); padding: 12px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
