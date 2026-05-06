<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Resources – Online Examination Portal</title>
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
            <a href="resources.php">Resources</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="profile.php">My Profile</a>
            <a href="help.php">Help</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>📚 Exam Study Resources</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Please review the following academic materials and syllabi before attempting your examination.</p>

            <div style="background: var(--bg); padding: 24px; border-radius: var(--radius); border-left: 4px solid var(--accent); margin-bottom: 24px;">
                <h3 style="color: var(--primary); font-size: 1.1rem; margin-bottom: 8px;">General Knowledge Syllabus</h3>
                <ul class="exam-rules" style="margin-left: 0; padding-left: 0; list-style-type: none;">
                    <li style="position: relative; padding-left: 20px; margin-bottom: 6px;">History and Geography</li>
                    <li style="position: relative; padding-left: 20px; margin-bottom: 6px;">Computer Science Fundamentals</li>
                    <li style="position: relative; padding-left: 20px; margin-bottom: 6px;">Global Current Affairs</li>
                    <li style="position: relative; padding-left: 20px; margin-bottom: 6px;">Basic Mathematics and Logic</li>
                </ul>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 32px;">
                <div style="border: 1px solid var(--border); border-radius: var(--radius); padding: 20px;">
                    <h4 style="color: var(--primary); margin-bottom: 8px;">📖 Computer Science 101</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Reference material covering programming basics, operating systems, and networking.</p>
                    <a href="#" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;" onclick="alert('PDF Download initiated.'); return false;">Download PDF</a>
                </div>
                <div style="border: 1px solid var(--border); border-radius: var(--radius); padding: 20px;">
                    <h4 style="color: var(--primary); margin-bottom: 8px;">🌍 Geography Handout</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Capital cities, continents, and major world geographical landmarks.</p>
                    <a href="#" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;" onclick="alert('PDF Download initiated.'); return false;">Download PDF</a>
                </div>
                <div style="border: 1px solid var(--border); border-radius: var(--radius); padding: 20px;">
                    <h4 style="color: var(--primary); margin-bottom: 8px;">🧮 Logic Puzzles</h4>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px;">Practice questions to sharpen your mathematical reasoning.</p>
                    <a href="#" class="btn btn-outline" style="font-size: 0.8rem; padding: 6px 12px;" onclick="alert('Link opened in new tab.'); return false;">View Online Resource</a>
                </div>
            </div>
            
            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center;">
                <p style="margin-bottom: 16px;">Once you are prepared, you may proceed to your dashboard to begin the examination.</p>
                <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
