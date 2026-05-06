<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-container glass">
        <h2 class="form-title">Welcome Back</h2>
        <div id="login-alert"></div>
        <form id="login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="name@campus.com">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php" style="color: var(--primary);">Sign up here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
