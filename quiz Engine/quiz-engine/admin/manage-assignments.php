<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

if (isset($_POST['add_assignment'])) {
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);
    $due   = $_POST['due_date'];
    
    $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $desc, $due);
    if ($stmt->execute()) {
        $msg = "Assignment posted successfully. Now modify it to add questions!";
    } else {
        $error = "Failed to post assignment.";
    }
}

// Handle Delete Assignment
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $msg = "Assignment deleted successfully (all related questions and results removed).";
    } else {
        $error = "Failed to delete assignment.";
    }
}

$assignments = $conn->query("
    SELECT a.*, 
           (SELECT COUNT(*) FROM assignment_questions WHERE assignment_id = a.id) as q_count,
           (SELECT COUNT(*) FROM assignment_results WHERE assignment_id = a.id) as submission_count 
    FROM assignments a 
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignments – Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-banner">Online Examination Portal – Admin</div>
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
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
            
            <div class="card">
                <h3 style="border-bottom: none; font-size: 1.1rem; margin-bottom: 12px;">Post New Assignment</h3>
                <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
                <?php if (isset($msg)) echo "<p class='pass'>$msg</p>"; ?>
                
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Assignment Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Assignment Description / Unit Topic</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" required>
                    </div>
                    <button type="submit" name="add_assignment" class="btn btn-primary" style="width: 100%;">Post Assignment</button>
                </form>
            </div>
            
            <div class="card">
                <h2 style="margin-bottom: 16px;">Active Assignments</h2>
                <?php if ($assignments->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Questions</th>
                                <th>Submissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $assignments->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                <td style="color: var(--danger); font-size: 0.85rem; font-weight: 700;"><?php echo date('d M Y', strtotime($row['due_date'])); ?></td>
                                <td>
                                    <?php 
                                    $is_expired = strtotime($row['due_date']) < strtotime(date('Y-m-d'));
                                    if ($is_expired) {
                                        echo "<span style='color: white; background: var(--danger); padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;'>EXPIRED</span>";
                                    } else {
                                        echo "<span style='color: white; background: var(--success); padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700;'>ACTIVE</span>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['q_count']; ?> Qs</td>
                                <td><?php echo $row['submission_count']; ?> Submitted</td>
                                <td>
                                    <a href="manage-assignment-questions.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.75rem; margin-bottom: 4px; display: inline-block;">+ Questions</a>
                                    <br>
                                    <a href="view-submissions.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="padding: 4px 10px; font-size: 0.75rem; display: inline-block; margin-bottom: 4px;">Results</a>
                                    <br>
                                    <a href="?delete_id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 4px 10px; font-size: 0.75rem; display: inline-block;"
                                       onclick="return confirm('Are you sure? This will delete all questions and student submissions for this assignment.')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-muted);">No assignments posted yet.</p>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
