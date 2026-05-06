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

$a_stmt = $conn->prepare("SELECT title FROM assignments WHERE id = ?");
$a_stmt->bind_param("i", $assignment_id);
$a_stmt->execute();
$a_res = $a_stmt->get_result();
if ($a_res->num_rows == 0) {
    die("Assignment not found.");
}
$assignment = $a_res->fetch_assoc();

$submissions = $conn->query("
    SELECT s.*, st.name as student_name, st.reg_number 
    FROM assignment_results s
    JOIN students st ON s.student_id = st.student_id
    WHERE s.assignment_id = $assignment_id
    ORDER BY s.submitted_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Results – Admin Panel</title>
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

    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 2px solid var(--accent-light); padding-bottom: 16px;">
                <div>
                    <h2 style="border: none; margin-bottom: 4px;">Assignment Results</h2>
                    <p style="color: var(--primary); font-weight: 700; font-size: 1.1rem;"><?php echo htmlspecialchars($assignment['title']); ?></p>
                </div>
                <a href="manage-assignments.php" class="btn btn-outline" style="padding: 8px 16px;">← Back</a>
            </div>

            <?php if ($submissions->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Reg Number</th>
                            <th>Submitted On</th>
                            <th>Score</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $submissions->fetch_assoc()): 
                            $perc = ($row['score'] / max(1, $row['total_questions'])) * 100;
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('d M Y, H:i', strtotime($row['submitted_at'])); ?></td>
                            <td style="font-weight: 700; font-size: 1.1rem; color: var(--primary);">
                                <?php echo $row['score']; ?> / <?php echo $row['total_questions']; ?>
                            </td>
                            <td class="<?php echo $perc >= 60 ? 'pass' : 'fail'; ?>" style="font-weight: 700;">
                                <?php echo number_format($perc, 1); ?>%
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 24px 0; text-align: center;">No students have submitted this assignment yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
