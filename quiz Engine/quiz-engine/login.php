<?php
include 'config/db.php';
session_start();

if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id']   = $row['student_id'];
            $_SESSION['student_name'] = $row['name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "No account found with this email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login – Online Examination Portal</title>
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
            <a href="register.php">Register</a>
            <a href="admin/admin-login.php">Admin</a>
        </div>
    </nav>

    <div class="container" style="max-width: 500px;">
        <div class="card">
            <h2>Student Login</h2>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <?php if (isset($_GET['msg'])) echo "<p class='pass'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your college email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary" style="width: 100%; padding: 12px;">Login to Portal</button>
            </form>
            <p style="margin-top: 1.2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                New student? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
