 <div class="login-card">
    <h2>Reset Your Password</h2>
    <?php if (!$resetData): ?>
    <div class="alert alert-danger">This reset link is invalid or has expired. <a href="index.php?page=login">Request a new one</a>.</div>
    <?php elseif ($err==='short'): ?>
    <div class="alert alert-danger">Password must be at least 6 characters.</div>
    <?php elseif ($err==='mismatch'): ?>
    <div class="alert alert-danger">Passwords do not match.</div>
    <?php else: ?>
    <p class="lsub">Hello <strong><?= e($resetData['full_name']?:$resetData['username']) ?></strong>, enter your new password below.</p>
    <form method="POST" action="index.php">
      <input type="hidden" name="action" value="do_reset_password">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="form-field"><label>New Password</label><input class="input" type="password" name="new_password" placeholder="Min 6 characters" required autofocus></div>
      <div class="form-field"><label>Confirm Password</label><input class="input" type="password" name="confirm_password" placeholder="Repeat new password" required></div>
      <button type="submit" class="btn btn-primary btn-lg btn-full"><i class="bx bx-key"></i> Reset Password</button>
    </form>
    <?php endif; ?>
    <div style="text-align:center;margin-top:.9rem"><a href="index.php?page=login" style="font-size:.78rem"><i class="bx bx-arrow-back"></i> Back to Sign In</a></div>
  </div>
  