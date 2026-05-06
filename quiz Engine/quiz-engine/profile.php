<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (isset($_POST['update_profile'])) {
    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (password_verify($current_pass, $student['password'])) {
        if ($new_pass === $confirm_pass) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ?");
            $upd->bind_param("si", $hashed, $student_id);
            if ($upd->execute()) {
                $msg = "Password updated successfully.";
                // update local instance so verify keeps working
                $student['password'] = $hashed; 
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – Online Examination Portal</title>
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

    <div class="container" style="max-width: 640px;">
        <div class="card">
            <h2>Student Profile</h2>
            <div style="display: flex; gap: 40px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border); flex-wrap: wrap;">
                <div>
                    <h3 style="color: var(--text-muted); margin-bottom: 4px;">Full Name</h3>
                    <p style="font-size: 1.1rem; font-weight: 700; color: var(--primary);"><?php echo htmlspecialchars($student['name']); ?></p>
                </div>
                <div>
                    <h3 style="color: var(--text-muted); margin-bottom: 4px;">Register Number</h3>
                    <p style="font-size: 1.1rem; font-weight: 700;"><?php echo htmlspecialchars($student['reg_number']); ?></p>
                </div>
                <div>
                    <h3 style="color: var(--text-muted); margin-bottom: 4px;">Email Address</h3>
                    <p style="font-size: 1.1rem; color: var(--text);"><?php echo htmlspecialchars($student['email']); ?></p>
                </div>
            </div>

            <h3 style="margin-bottom: 16px;">Update Password</h3>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <?php if (isset($msg)) echo "<p class='pass'>$msg</p>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
