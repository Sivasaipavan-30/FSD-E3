<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$userName = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus Events</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">CampusEvents</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#events">Events</a></li>
            <?php if (!$isLoggedIn): ?>
                <li><a href="admin_login.php">Admin Login</a></li>
            <?php endif; ?>
            <?php if ($isLoggedIn && $userRole !== 'admin'): ?>
                <li><a href="index.php#my-bookings">My Bookings</a></li>
            <?php endif; ?>
            <?php if ($isLoggedIn && $userRole === 'admin'): ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="javascript:void(0)" onclick="toggleHelpCenter()">Help</a></li>
        </ul>
        <script>
            // Pass PHP session data to JS
            const USER_ROLE = '<?php echo $userRole; ?>';
            const IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        </script>
        <div class="nav-auth">
            <?php if ($isLoggedIn): ?>
                <span style="margin-right: 15px; color: var(--text-muted)">Hi, <?php echo htmlspecialchars($userName); ?></span>
                <a href="api/auth.php?action=logout" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>
