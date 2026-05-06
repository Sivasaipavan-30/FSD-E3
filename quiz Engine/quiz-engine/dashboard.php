<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT * FROM results WHERE student_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$results = $stmt->get_result();

$categories = $conn->query("SELECT DISTINCT category FROM questions ORDER BY category ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard – Online Examination Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-banner">
        Online Examination Portal
    </div>
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

    <div class="container">

        <div class="card" style="border-top-color: var(--accent); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="border-bottom: none; margin-bottom: 4px;">Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem;">Select an examination category below to begin your assessment.</p>
                </div>
                <div style="background: var(--accent-light); padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent);">STUDENT PORTAL</span>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; margin-bottom: 1.25rem; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                <span style="background: var(--primary); color: white; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 6px; font-size: 0.9rem;">▶</span>
                Available Examinations
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <div class="card" style="margin: 0; padding: 24px; transition: transform 0.2s, box-shadow 0.2s; border-top-width: 4px;">
                            <h3 style="font-size: 1.1rem; margin-bottom: 12px; color: var(--primary);"><?php echo htmlspecialchars($cat['category']); ?></h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5;">
                                Standardized assessment for <?php echo htmlspecialchars($cat['category']); ?>. 10 questions, 10 minutes.
                            </p>
                            <a href="quiz.php?category=<?php echo urlencode($cat['category']); ?>" class="btn btn-primary" style="width: 100%; text-align: center; padding: 10px;">
                                Appear for Exam
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <p style="color: var(--text-muted);">No examination categories currently available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>Examination History</h2>
            <?php if ($results->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Date &amp; Time</th>
                            <th>Score</th>
                            <th>Total</th>
                            <th>Percentage</th>
                            <th>Result</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while($row = $results->fetch_assoc()):
                            $percentage  = ($row['score'] / $row['total_questions']) * 100;
                            $passed      = $percentage >= 60;
                            $statusClass = $passed ? 'pass' : 'fail';
                            $statusLabel = $passed ? '✔ Pass' : '✘ Fail';
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['category']); ?></strong></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['date'])); ?></td>
                            <td><strong><?php echo $row['score']; ?></strong></td>
                            <td><?php echo $row['total_questions']; ?></td>
                            <td><?php echo number_format($percentage, 1); ?>%</td>
                            <td class="<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></td>
                            <td>
                                <?php if ($passed): ?>
                                    <a href="certificate.php?id=<?php echo $row['result_id']; ?>" class="btn btn-success" style="padding: 5px 14px; font-size: 0.78rem;">Download</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 16px 0;">No examination records found. Click <strong>Start Examination</strong> above to take your first test.</p>
            <?php endif; ?>
        </div>

    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
