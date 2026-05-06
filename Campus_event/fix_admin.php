<?php
// fix_admin.php - Temporary script to fix admin credentials
require_once 'api/db.php';

$email = 'admin@campus.com';
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE id = ?");
        $stmt->execute([$hash, $user['id']]);
        echo "<h1>Admin updated!</h1><p>You can now login with: <br>Email: <b>$email</b><br>Password: <b>$password</b></p>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('System Admin', ?, ?, 'admin')");
        $stmt->execute([$email, $hash]);
        echo "<h1>Admin created!</h1><p>You can now login with: <br>Email: <b>$email</b><br>Password: <b>$password</b></p>";
    }
    
    echo "<br><a href='admin_login.php' style='padding:10px 20px; background:#6366f1; color:white; text-decoration:none; border-radius:5px;'>Go to Login Page</a>";

} catch (PDOException $e) {
    echo "<h1>Error!</h1><p>" . $e->getMessage() . "</p>";
}
?>
