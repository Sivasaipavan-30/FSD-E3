<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (isset($_POST['add_question'])) {
    $category = trim($_POST['category']);
    $question = trim($_POST['question']);
    $opt1 = trim($_POST['opt1']);
    $opt2 = trim($_POST['opt2']);
    $opt3 = trim($_POST['opt3']);
    $opt4 = trim($_POST['opt4']);
    $correct = (int)$_POST['correct'];

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO questions (category, question, option1, option2, option3, option4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $category, $question, $opt1, $opt2, $opt3, $opt4, $correct);

    if ($stmt->execute()) {
        header("Location: manage-questions.php?msg=Question+added+successfully");
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
    <title>Add Question – Admin Panel</title>
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
            <h2>Add New Question</h2>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Quiz Category</label>
                    <input type="text" name="category" list="category-list" 
                           value="<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>"
                           placeholder="Enter or select category" required style="padding: 10px; font-size: 0.95rem;">
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
                    <textarea name="question" rows="3" placeholder="Enter the full question here..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Option A</label>
                    <input type="text" name="opt1" placeholder="First option" required>
                </div>
                <div class="form-group">
                    <label>Option B</label>
                    <input type="text" name="opt2" placeholder="Second option" required>
                </div>
                <div class="form-group">
                    <label>Option C</label>
                    <input type="text" name="opt3" placeholder="Third option" required>
                </div>
                <div class="form-group">
                    <label>Option D</label>
                    <input type="text" name="opt4" placeholder="Fourth option" required>
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
                <button type="submit" name="add_question" class="btn btn-primary" style="width: 100%; padding: 12px;">Add to Question Bank</button>
            </form>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
