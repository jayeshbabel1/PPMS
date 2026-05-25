<div class="login-card">
    <h2>Sign in to your account</h2>
    <p class="lsub">Access is restricted to authorised users only.</p>
    <?php if ($err==1): ?><div class="alert alert-danger">Incorrect username or password.</div><?php endif; ?>
    <?php if (($_GET['info']??'')==='reset_sent'): ?><div class="alert alert-success">If that email exists, a reset link has been sent.</div><?php endif; ?>
    <?php if (($_GET['info']??'')==='pw_reset_done'): ?><div class="alert alert-success">Password reset successfully. Please sign in.</div><?php endif; ?>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="login">
      <div class="form-field"><label>Username</label><input class="input" type="text" name="username" placeholder="Enter username" autocomplete="username" required autofocus></div>
      <div class="form-field"><label>Password</label><input class="input" type="password" name="password" placeholder="Enter password" autocomplete="current-password" required></div>
      <button type="submit" class="btn btn-primary btn-lg btn-full" style="margin-top:.3rem"><i class="bx bx-log-in"></i> Sign In</button>
    </form>
    <div style="text-align:center;margin-top:.9rem">
      <a href="#" onclick="showForgot();return false" style="font-size:.78rem;color:var(--primary-d)">Forgot password?</a>
    </div>
    <!-- Forgot password form (hidden) -->
    <div id="forgotForm" style="display:none;margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border)">
      <p style="font-size:.8rem;color:var(--t3);margin-bottom:.8rem">Enter your email address and we'll send you a reset link.</p>
      <form method="POST" action="index.php">
        <input type="hidden" name="action" value="forgot_password">
        <div class="form-field"><label>Email Address</label><input class="input" type="email" name="email" placeholder="your@email.com" required></div>
        <button type="submit" class="btn btn-primary btn-md btn-full"><i class="bx bx-envelope"></i> Send Reset Link</button>
      </form>
    </div>
      </div>