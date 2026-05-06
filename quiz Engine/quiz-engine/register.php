<?php
include 'config/db.php';
session_start();

if (isset($_POST['register'])) {
    $name       = trim($_POST['name']);
    $email      = trim($_POST['email']);
    $reg_number = trim($_POST['reg_number']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO students (name, email, reg_number, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $reg_number, $password);

    if ($stmt->execute()) {
        header("Location: login.php?msg=Registration successful! Please login.");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration – Online Examination Portal</title>
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
            <a href="login.php">Login</a>
            <a href="admin/admin-login.php">Admin</a>
        </div>
    </nav>

    <div class="container" style="max-width: 520px;">
        <div class="card">
            <h2>Student Registration</h2>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <form action="" method="POST" onsubmit="return validateRegistration()">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="As per college records" required>
                </div>
                <div class="form-group">
                    <label>College Email Address</label>
                    <input type="email" name="email" placeholder="yourname@college.edu" required>
                </div>
                <div class="form-group">
                    <label>Register Number / Roll No.</label>
                    <input type="text" name="reg_number" placeholder="e.g. 22CSE001" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Create a strong password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="confirm_password" placeholder="Re-enter password" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary" style="width: 100%; padding: 12px;">Create Account</button>
            </form>
            <p style="margin-top: 1.2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
    <script src="js/script.js"></script>
</body>
</html>
