<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

// Handle resolving query
if (isset($_GET['resolve'])) {
    $q_id = (int)$_GET['resolve'];
    $conn->query("UPDATE support_queries SET status = 'Resolved' WHERE query_id = $q_id");
    header("Location: manage-queries.php?msg=Query+marked+as+resolved.");
    exit();
}

// Ensure table exists just in case
$conn->query("CREATE TABLE IF NOT EXISTS support_queries (
    query_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open', 'Resolved') DEFAULT 'Open',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$queries = $conn->query("
    SELECT q.*, s.name as student_name, s.email, s.reg_number 
    FROM support_queries q 
    JOIN students s ON q.student_id = s.student_id 
    ORDER BY FIELD(status, 'Open', 'Resolved'), date_submitted DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Help & Support – Admin Panel</title>
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
            <h2>Help & Support Queries</h2>
            <?php if (isset($_GET['msg'])) echo "<p class='pass'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>

            <?php if ($queries->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $queries->fetch_assoc()): ?>
                        <tr>
                            <td style="font-size: 0.85rem;"><?php echo date('d M, H:i', strtotime($row['date_submitted'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['student_name']); ?></strong><br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['email']); ?></span>
                            </td>
                            <td><strong style="color: var(--primary);"><?php echo htmlspecialchars(ucfirst($row['subject'])); ?></strong></td>
                            <td style="max-width: 300px; font-size: 0.9rem;"><?php echo htmlspecialchars($row['message']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Open'): ?>
                                    <span style="color: #9b1c1c; font-weight: 700;">Open</span>
                                <?php else: ?>
                                    <span style="color: #1a6b3c; font-weight: 700;">Resolved</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Open'): ?>
                                    <a href="?resolve=<?php echo $row['query_id']; ?>" 
                                       class="btn btn-primary" 
                                       style="padding: 6px 14px; font-size: 0.78rem;">Resolve</a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 16px 0;">No support queries submitted.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
