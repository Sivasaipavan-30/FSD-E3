<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS support_queries (
    query_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open', 'Resolved') DEFAULT 'Open',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if (isset($_POST['submit_query'])) {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO support_queries (student_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['student_id'], $subject, $message);
    if ($stmt->execute()) {
        $msg = "Your query has been submitted to the Academic Affairs Division. We will respond to your registered email address shortly.";
    } else {
        $error = "Failed to submit query. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support – Online Examination Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .faq-item {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .faq-item h4 { color: var(--primary); margin-bottom: 8px; font-size: 1rem; }
        .faq-item p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; }
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
            <a href="resources.php">Resources</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="profile.php">My Profile</a>
            <a href="help.php">Help</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; align-items: start;">
            
            <!-- FAQ Section -->
            <div class="card">
                <h2>💬 Frequently Asked Questions</h2>
                
                <div class="faq-item">
                    <h4>What happens if my internet disconnects during the exam?</h4>
                    <p>The timer will continue running server-side. Once your connection is restored, refresh the page and continue. If the time expires while you are disconnected, the exam will be auto-submitted.</p>
                </div>
                
                <div class="faq-item">
                    <h4>When will I receive my certificate?</h4>
                    <p>Certificates are generated instantly upon successful completion of the exam with a passing score (60% or higher). You can download it directly from the results page or your dashboard history.</p>
                </div>
                
                <div class="faq-item">
                    <h4>Can I retake the examination?</h4>
                    <p>Yes, you can take the examination multiple times. All attempts are recorded in your Dashboard history, and your best score will be reflected on the Leaderboard.</p>
                </div>
                
                <div class="faq-item">
                    <h4>I forgot my password, how can I reset it?</h4>
                    <p>Currently, password resets must be done manually by the administrator. Please submit a query using the contact form on this page.</p>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="card" style="border-top-color: var(--accent);">
                <h3 style="border-bottom: none; font-size: 1.2rem; margin-bottom: 12px;">Contact Support</h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px;">Need further assistance? Message the Examination Controller.</p>
                
                <?php if (isset($error)) echo "<p class='fail' style='font-size: 0.85rem;'>$error</p>"; ?>
                <?php if (isset($msg)) echo "<p class='pass' style='font-size: 0.85rem;'>$msg</p>"; ?>
                
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Issue Subject</label>
                        <select name="subject" required style="padding: 10px; font-size: 0.9rem;">
                            <option value="">Select a topic...</option>
                            <option value="tech">Technical Issue / Bug</option>
                            <option value="exam">Question regarding Exam Content</option>
                            <option value="account">Account Access / Password Reset</option>
                            <option value="other">Other Inquiry</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message Detail</label>
                        <textarea name="message" rows="5" placeholder="Describe your issue..." required style="padding: 10px; font-size: 0.9rem;"></textarea>
                    </div>
                    <button type="submit" name="submit_query" class="btn btn-primary" style="width: 100%;">Submit Query</button>
                </form>
            </div>
            
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
