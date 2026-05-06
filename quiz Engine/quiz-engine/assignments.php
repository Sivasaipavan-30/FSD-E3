<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$assignments = $conn->query("
    SELECT a.*, 
           (SELECT COUNT(*) FROM assignment_questions WHERE assignment_id = a.id) as q_count,
           r.score, r.total_questions, r.submitted_at
    FROM assignments a 
    LEFT JOIN assignment_results r ON a.id = r.assignment_id AND r.student_id = $student_id
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assignments – Online Examination Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="top-banner">Online Examination Portal</div>
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
        <div class="card">
            <h2>Course Assignments</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Complete your pending assignment evaluations below. Each assignment acts as a miniature quiz.</p>

            <?php if (isset($_GET['success'])): ?>
                <p class='pass'>Assignment evaluated successfully! Your score has been recorded.</p>
            <?php endif; ?>

            <?php if ($assignments->num_rows > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php while($row = $assignments->fetch_assoc()): ?>
                        <div style="border: 1px solid var(--border); border-radius: var(--radius); padding: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="color: var(--primary); font-size: 1.2rem; margin-bottom: 6px;"><?php echo htmlspecialchars($row['title']); ?></h3>
                                    <p style="font-size: 0.95rem; color: #555; margin-bottom: 12px;"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                                    
                                    <div style="display: flex; gap: 16px; font-size: 0.85rem; font-weight: 700;">
                                        <span style="color: var(--danger);">Due: <?php echo date('d M Y', strtotime($row['due_date'])); ?></span>
                                        <span style="color: var(--accent);">Questions: <?php echo $row['q_count']; ?></span>
                                        <?php 
                                        $is_expired = strtotime($row['due_date']) < strtotime(date('Y-m-d'));
                                        if ($is_expired && $row['submitted_at'] === null) {
                                            echo "<span style='color: white; background: var(--danger); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;'>TIME UP</span>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div style="text-align: right; min-width: 140px;">
                                    <?php if ($row['submitted_at'] !== null): ?>
                                        <div style="background: var(--bg); padding: 10px; border-radius: 6px; border: 1px solid var(--border);">
                                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px;">Score Achieved</p>
                                            <div style="font-size: 1.4rem; font-weight: 800; color: var(--primary);">
                                                <?php echo $row['score']; ?> <span style="font-size: 1rem; color: #777;">/ <?php echo $row['total_questions']; ?></span>
                                            </div>
                                            <?php 
                                            $perc = ($row['score'] / max(1, $row['total_questions'])) * 100;
                                            $cls = $perc >= 60 ? 'pass' : 'fail';
                                            echo "<div class='$cls' style='font-size: 0.8rem; margin-top: 4px; padding: 2px 0;'>".number_format($perc,1)."%</div>";
                                            ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if ($row['q_count'] > 0): ?>
                                            <?php if ($is_expired): ?>
                                                <span style="color: var(--danger); font-size: 0.85rem; font-weight: 700; border: 1px solid var(--danger); padding: 6px 12px; border-radius: 4px;">Deadline Passed</span>
                                            <?php else: ?>
                                                <a href="take-assignment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.9rem; display: inline-block;">Start Assignment</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Processing...</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); padding: 16px 0;">No active assignments found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
