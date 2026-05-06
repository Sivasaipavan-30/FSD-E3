<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Get stats
$total_questions = $conn->query("SELECT COUNT(*) as count FROM questions")->fetch_assoc()['count'];
$total_students  = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_exams     = $conn->query("SELECT COUNT(*) as count FROM results")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Examination Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: var(--radius);
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
        }
        .stat-card h3 { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 10px; }
        .stat-card .value { font-size: 2.5rem; font-weight: 800; color: var(--primary); }
    </style>
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
        <div class="card" style="border-top-color: var(--accent);">
            <h2 style="border-bottom: none; margin-bottom: 8px;">Admin Dashboard</h2>
            <p style="color: var(--text-muted);">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>. Here is a summary of the portal.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 24px;">
            <div class="stat-card">
                <h3>Total Questions</h3>
                <div class="value"><?php echo number_format($total_questions); ?></div>
            </div>
            <div class="stat-card">
                <h3>Registered Students</h3>
                <div class="value"><?php echo number_format($total_students); ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Exams Taken</h3>
                <div class="value"><?php echo number_format($total_exams); ?></div>
            </div>
        </div>

        <div class="actions" style="justify-content: center; gap: 20px;">
            <a href="add-question.php" class="btn btn-primary" style="padding: 12px 24px;">+ Add New Question</a>
            <a href="view-results.php" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary); padding: 12px 24px;">View Recent Results</a>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
