<?php
// api/auth.php
/**
 * Authentication Handler
 * Handles user registration, login, and session checks.
 * All POST requests use password hashing for security.
 */
require_once 'db.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        if (empty($name) || empty($email) || empty($password)) {
            sendResponse(false, 'All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendResponse(false, 'Please provide a valid email address');
        }

        if ($password !== $confirmPassword) {
            sendResponse(false, 'Passwords do not match');
        }

        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            sendResponse(false, 'Email already registered');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        
        try {
            $stmt->execute([$name, $email, $hashedPassword, $role]);

            // Send Welcome Email
            try {
                $subject = "Welcome to Smart Campus Events!";
                $message = "Hello $name,\n\n";
                $message .= "Thank you for creating an account on Smart Campus Events.\n";
                $message .= "You can now log in to browse and book tickets for the most exciting events happening on campus.\n\n";
                $message .= "Your registered email: $email\n\n";
                $message .= "Best Regards,\nSmart Campus Events Team";
                
                $headers = "From: noreply@campusevents.com\r\n";
                $headers .= "Reply-To: support@campusevents.com\r\n";
                
                @mail($email, $subject, $message, $headers);
            } catch (Exception $mailEx) {
                // Silently fail if mail configuration is missing, do not break the registration flow
            }

            sendResponse(true, 'Registration successful! You can now login.');
        } catch (PDOException $e) {
            sendResponse(false, 'Registration failed: ' . $e->getMessage());
        }
    }

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            sendResponse(false, 'Email and password are required');
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            sendResponse(true, 'Login successful', [
                'name' => $user['name'],
                'role' => $user['role']
            ]);
        } else {
            sendResponse(false, 'Invalid email or password');
        }
    }
}

if ($action === 'logout') {
    session_destroy();
    header('Location: ../login.php');
    exit;
}

if ($action === 'list') {
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        sendResponse(false, 'Unauthorized access');
    }

    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    sendResponse(true, 'User directory loaded', $stmt->fetchAll());
}

if ($action === 'me') {
    if (isset($_SESSION['user_id'])) {
        sendResponse(true, 'User session active', [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        ]);
    } else {
        sendResponse(false, 'No active session');
    }
}
?>
