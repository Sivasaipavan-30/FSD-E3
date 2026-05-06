<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Examination Portal - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="top-banner">
        Online Examination Portal
    </div>

    <nav class="navbar">
        <div class="logo">Online Examination Portal</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['student_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="admin/admin-login.php">Admin</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">

        <!-- Hero -->
        <div class="hero">
            <h1>Welcome to the Online Examination Portal</h1>
            <p>Appear for your college examinations securely from anywhere. Answer all MCQ questions within the time limit and receive your certificate instantly upon passing.</p>
            <div class="actions">
                <?php if (isset($_SESSION['student_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Register Now</a>
                    <a href="login.php" class="btn-outline">Student Login</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-card">
                <h3>📋 Step 1 – Register</h3>
                <p>Create your student account using your college email and registration number.</p>
            </div>
            <div class="info-card">
                <h3>🖥️ Step 2 – Appear for Exam</h3>
                <p>Login and start the timed multiple-choice examination. Answer all questions before time runs out.</p>
            </div>
            <div class="info-card">
                <h3>🏆 Step 3 – Get Certificate</h3>
                <p>Score 60% or above to pass and instantly download your verified certificate of achievement.</p>
            </div>
        </div>

        <!-- Exam Rules -->
        <div class="card">
            <h2>Examination Guidelines</h2>
            <ul class="exam-rules">
                <li>Each student must register with a valid college email address and registration number.</li>
                <li>The exam consists of multiple-choice questions (MCQs). Each question has four options.</li>
                <li>A strict time limit applies. The exam will auto-submit when time expires.</li>
                <li>You must answer all questions before submitting the exam.</li>
                <li>A minimum score of <strong>60%</strong> is required to pass and download the certificate.</li>
                <li>Do not refresh or navigate away from the exam page during the test.</li>
            </ul>
        </div>

    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division
    </div>

    <script src="js/script.js"></script>
</body>
</html>
