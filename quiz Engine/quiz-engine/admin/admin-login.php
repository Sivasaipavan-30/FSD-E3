<?php
include '../config/db.php';
session_start();

if (isset($_POST['admin_login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password']) || $password === $row['password']) {
            $_SESSION['admin_id']   = $row['admin_id'];
            $_SESSION['admin_name'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Examination Portal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="top-banner">
        Online Examination Portal – Admin
    </div>
    <nav class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="../index.php">← Back to Portal</a>
        </div>
    </nav>

    <div class="container" style="max-width: 420px;">
        <div class="card">
            <h2>Administrator Login</h2>
            <?php if (isset($error)) echo "<p class='fail'>$error</p>"; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Admin username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Admin password" required>
                </div>
                <button type="submit" name="admin_login" class="btn btn-primary" style="width: 100%; padding: 12px;">Login</button>
            </form>
        </div>
    </div>

    <div class="footer">&copy; <?php echo date('Y'); ?> Online Examination Portal &mdash; Academic Affairs Division</div>
</body>
</html>
