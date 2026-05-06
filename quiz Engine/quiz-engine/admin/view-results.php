<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$results = $conn->query("
    SELECT r.*, s.name as student_name, s.reg_number 
    FROM results r 
    JOIN students s ON r.student_id = s.student_id 
    ORDER BY r.date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Results – Admin Panel</title>
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
            <h2>Examination Results</h2>
            <?php if ($results->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Percentage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $results->fetch_assoc()):
                            $percentage  = ($row['score'] / $row['total_questions']) * 100;
                            $passed      = $percentage >= 60;
                            $statusClass = $passed ? 'pass' : 'fail';
                            $statusLabel = $passed ? 'Pass' : 'Fail';
                        ?>
                        <tr>
                            <td style="font-size: 0.85rem;"><?php echo date('d M Y, H:i', strtotime($row['date'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['student_name']); ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['reg_number']); ?></span>
                            </td>
                            <td><span style="background: var(--bg); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($row['category']); ?></span></td>
                            <td><strong><?php echo $row['score']; ?></strong> / <?php echo $row['total_questions']; ?></td>
                            <td><?php echo number_format($percentage, 1); ?>%</td>
                            <td class="<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 16px 0;">No exams have been taken yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
