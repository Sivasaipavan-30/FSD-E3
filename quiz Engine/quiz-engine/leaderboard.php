<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Get top 10 scores
$sql = "
    SELECT r.score, r.total_questions, r.date, s.name, s.reg_number 
    FROM results r
    JOIN students s ON r.student_id = s.student_id
    WHERE (r.score / r.total_questions) >= 0.6
    ORDER BY score DESC, date ASC
    LIMIT 10
";
$top_scorers = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Scorers Leaderboard – Online Examination Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .rank-1 { color: #b8860b; font-weight: 800; font-size: 1.1rem; }
        .rank-2 { color: #8e8e8e; font-weight: 800; font-size: 1.1rem; }
        .rank-3 { color: #cd7f32; font-weight: 800; font-size: 1.1rem; }
        .medal { font-size: 1.2rem; margin-right: 6px; }
    </style>
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
        <div class="card">
            <h2>🏆 Top Scorers Leaderboard</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Displaying the highest performing students on the platform (passed exams only).</p>

            <?php if ($top_scorers->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student Name</th>
                            <th>Reg Number</th>
                            <th>Score</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1; 
                        while($row = $top_scorers->fetch_assoc()): 
                            $percentage = ($row['score'] / $row['total_questions']) * 100;
                            
                            $rankHtml = $rank;
                            if ($rank == 1) $rankHtml = "<span class='rank-1'><span class='medal'>🥇</span>1</span>";
                            if ($rank == 2) $rankHtml = "<span class='rank-2'><span class='medal'>🥈</span>2</span>";
                            if ($rank == 3) $rankHtml = "<span class='rank-3'><span class='medal'>🥉</span>3</span>";
                        ?>
                        <tr>
                            <td><?php echo $rankHtml; ?></td>
                            <td style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
                            <td><strong><?php echo $row['score']; ?></strong> / <?php echo $row['total_questions']; ?></td>
                            <td style="color: var(--success); font-weight: 700;"><?php echo number_format($percentage, 1); ?>%</td>
                        </tr>
                        <?php $rank++; endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: var(--text-muted); padding: 24px;">No exam results recorded or no passing scores yet. Be the first!</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
