<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id   = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage-questions.php?msg=Question+deleted+successfully");
    exit();
}

$questions = $conn->query("SELECT * FROM questions ORDER BY question_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions – Admin Panel</title>
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

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
                <h2 style="margin-bottom: 0; border-bottom: none;">Question Bank</h2>
            </div>

            <?php if (isset($_GET['msg'])) echo "<p class='pass'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Question</th>
                        <th>Options</th>
                        <th>Correct</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['question_id']; ?></td>
                        <td><span style="background: var(--bg); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($row['category']); ?></span></td>
                        <td style="min-width: 250px;"><strong><?php echo htmlspecialchars($row['question']); ?></strong></td>
                        <td style="font-size: 0.82rem; line-height: 1.8;">
                            A. <?php echo htmlspecialchars($row['option1']); ?><br>
                            B. <?php echo htmlspecialchars($row['option2']); ?><br>
                            C. <?php echo htmlspecialchars($row['option3']); ?><br>
                            D. <?php echo htmlspecialchars($row['option4']); ?>
                        </td>
                        <td style="font-weight: 700; color: var(--success);">
                            <?php echo chr(64 + (int)$row['correct_answer']); ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="edit-question.php?id=<?php echo $row['question_id']; ?>" 
                                   class="btn btn-primary" 
                                   style="padding: 5px 14px; font-size: 0.78rem;">Edit</a>
                                <a href="?delete=<?php echo $row['question_id']; ?>"
                                   class="btn btn-danger"
                                   style="padding: 5px 14px; font-size: 0.78rem;"
                                   onclick="return confirmDelete()">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
    <script src="../js/script.js"></script>
</body>
</html>
