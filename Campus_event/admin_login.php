<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="form-container glass" style="max-width: 500px; border-top: 4px solid var(--primary);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="background: var(--primary); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <span style="font-size: 24px;">🛡️</span>
            </div>
            <h2 class="form-title" style="margin-bottom: 0.5rem;">Admin Portal</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Secure access for campus administrators and faculty.</p>
        </div>
        
        <div id="admin-login-alert"></div>
        
        <form id="admin-login-form">
            <div class="form-group">
                <label for="email">Administrator Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="admin@campus.com">
            </div>
            <div class="form-group">
                <label for="password">Security Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.9rem; margin-top: 1rem;">
                Authorized Login
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 2rem; font-size: 0.8rem; color: var(--text-muted);">
            Unauthorized access is strictly prohibited.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
