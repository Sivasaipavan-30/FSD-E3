<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-container glass">
        <h2 class="form-title">Create Account</h2>
        <div id="register-alert"></div>
        <form id="register-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="john@campus.com">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Re-enter password">
            </div>
            <div class="form-group">
                <label for="role">Account Type</label>
                <select id="role" name="role" class="form-control">
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem;">Register</button>
        </form>
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary);">Login here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
