<?php
// ============================================================
//  setup.php — Run ONCE in your browser after uploading files
//  e.g. http://yoursite.com/aaraji/setup.php
//  DELETE this file immediately after setup is done!
// ============================================================

require_once __DIR__ . '/includes/config.php';

$step    = $_POST['step']    ?? '';
$message = '';
$error   = '';
$success = false;

// ── Handle form submission ────────────────────────────────────
if ($step === 'create_admin') {
    $username  = trim($_POST['username']  ?? '');
    $fullname  = trim($_POST['full_name'] ?? '');
    $password  = $_POST['password']       ?? '';
    $confirm   = $_POST['confirm']        ?? '';

    if (!$username || !$password) {
        $error = 'Username and password are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username may only contain letters, numbers, and underscores.';
    } else {
        try {
            // Check if username already exists
            $chk = db()->prepare('SELECT id FROM users WHERE username = ?');
            $chk->execute([$username]);
            if ($chk->fetch()) {
                $error = "Username \"$username\" already exists. Choose a different one.";
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                db()->prepare(
                    'INSERT INTO users (username, password, full_name, role, is_active)
                     VALUES (?, ?, ?, \'admin\', 1)'
                )->execute([$username, $hash, $fullname ?: $username]);
                $success = true;
                $message = "Admin user \"$username\" created successfully!";
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// ── Check DB connection ───────────────────────────────────────
$dbOk = false;
$dbMsg = '';
try {
    db()->query('SELECT 1');
    $dbOk = true;
    $dbMsg = 'Connected to <strong>' . DB_NAME . '</strong> on <strong>' . DB_HOST . '</strong>';
} catch (Throwable $e) {
    $dbMsg = 'Cannot connect: ' . $e->getMessage();
}

// ── Check tables exist ────────────────────────────────────────
$tablesOk = false;
$existingUsers = 0;
if ($dbOk) {
    try {
        $existingUsers = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $tablesOk = true;
    } catch (Throwable) {
        $tablesOk = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Aaraji Registry — Setup</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'Segoe UI', Inter, sans-serif;
    background: #0b0f1a;
    color: #c8d3ea;
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 2rem;
  }
  .wrap { max-width: 520px; width: 100%; }

  .brand {
    text-align: center; margin-bottom: 2rem;
  }
  .brand-icon {
    width: 60px; height: 60px; border-radius: 16px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 28px; margin-bottom: .8rem;
    box-shadow: 0 8px 28px rgba(37,99,235,.4);
  }
  .brand h1 { font-size: 1.5rem; color: #f0f4ff; font-weight: 700; }
  .brand p  { font-size: .78rem; color: #4a5a78; margin-top: 4px; letter-spacing: .06em; text-transform: uppercase; }

  .card {
    background: #161b22; border: 1px solid #2c3e5c;
    border-radius: 16px; padding: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.55);
  }
  .card h2 { font-size: 1rem; font-weight: 700; color: #f0f4ff; margin-bottom: .3rem; }
  .card .sub { font-size: .78rem; color: #7e93b8; margin-bottom: 1.5rem; }

  .check {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; border-radius: 8px;
    font-size: .82rem; margin-bottom: 10px;
    border: 1px solid;
  }
  .check.ok     { background: rgba(5,150,105,.12); border-color: rgba(5,150,105,.3); color: #6ee7b7; }
  .check.bad    { background: rgba(220,38,38,.12);  border-color: rgba(220,38,38,.3);  color: #fca5a5; }
  .check.warn   { background: rgba(217,119,6,.12);  border-color: rgba(217,119,6,.3);  color: #fcd34d; }

  .divider { height: 1px; background: #2c3e5c; margin: 1.3rem 0; }

  .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 1rem; }
  .field label { font-size: .7rem; font-weight: 600; color: #7e93b8; letter-spacing: .07em; text-transform: uppercase; }
  .field input {
    background: #1c2332; border: 1px solid #2c3e5c; border-radius: 8px;
    padding: 10px 13px; color: #f0f4ff; font-size: .88rem; outline: none;
    transition: border-color .18s, box-shadow .18s;
  }
  .field input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.2); }

  .btn {
    width: 100%; padding: 12px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; border: none; border-radius: 8px;
    font-size: .9rem; font-weight: 600; cursor: pointer;
    transition: all .18s; font-family: inherit;
  }
  .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,.45); }
  .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

  .alert {
    border-radius: 8px; padding: 10px 14px;
    font-size: .82rem; margin-bottom: 1rem;
    display: flex; align-items: flex-start; gap: 8px; border: 1px solid;
  }
  .alert.err  { background: rgba(220,38,38,.12); border-color: rgba(220,38,38,.3); color: #fca5a5; }
  .alert.ok   { background: rgba(5,150,105,.12); border-color: rgba(5,150,105,.3); color: #6ee7b7; }

  .delete-note {
    margin-top: 1.5rem; padding: 12px 16px;
    background: rgba(220,38,38,.1); border: 1px solid rgba(220,38,38,.3);
    border-radius: 8px; font-size: .78rem; color: #fca5a5; line-height: 1.6;
  }
  .delete-note strong { display: block; margin-bottom: 4px; font-size: .82rem; }
  code {
    background: #1c2332; border: 1px solid #2c3e5c;
    border-radius: 4px; padding: 1px 6px; font-size: .8rem;
    font-family: 'JetBrains Mono', monospace; color: #93c5fd;
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-icon">🗺</div>
    <h1>Aaraji Registry</h1>
    <p>One-time Setup Wizard</p>
  </div>

  <div class="card">
    <h2>System Status</h2>
    <p class="sub">Checking database connection and tables…</p>

    <!-- DB Connection -->
    <div class="check <?= $dbOk ? 'ok' : 'bad' ?>">
      <?= $dbOk ? '✓' : '✗' ?>
      <span><?= $dbMsg ?></span>
    </div>

    <!-- Tables -->
    <?php if ($dbOk): ?>
    <div class="check <?= $tablesOk ? 'ok' : 'bad' ?>">
      <?= $tablesOk ? '✓' : '✗' ?>
      <span>
        <?php if ($tablesOk): ?>
          Database tables found — <strong><?= $existingUsers ?></strong> user(s) exist
        <?php else: ?>
          Tables not found. Did you run <code>database.sql</code> yet?
        <?php endif; ?>
      </span>
    </div>
    <?php endif; ?>

    <?php if (!$dbOk): ?>
    <p style="margin-top:1rem;font-size:.8rem;color:#7e93b8">
      Edit <code>includes/config.php</code> with your correct DB credentials, then refresh this page.
    </p>
    <?php elseif (!$tablesOk): ?>
    <p style="margin-top:1rem;font-size:.8rem;color:#7e93b8">
      Run <code>database.sql</code> in your MariaDB client first, then refresh.
    </p>
    <?php else: ?>

    <div class="divider"></div>

    <?php if ($success): ?>
      <div class="alert ok">✓ <?= htmlspecialchars($message) ?> You can now <a href="index.php" style="color:#34d399">sign in →</a></div>
      <div class="delete-note">
        <strong>⚠ Security: Delete this file now!</strong>
        Remove <code>setup.php</code> from your server immediately.<br>
        Via SSH: <code>rm /path/to/aaraji/setup.php</code>
      </div>

    <?php else: ?>

      <h2>Create Admin Account</h2>
      <p class="sub">This will be your login for the registry system.</p>

      <?php if ($error): ?>
      <div class="alert err">⚠ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="hidden" name="step" value="create_admin">
        <div class="field">
          <label>Username</label>
          <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>"
                 placeholder="admin" required autocomplete="off">
        </div>
        <div class="field">
          <label>Full Name (optional)</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                 placeholder="e.g. Ramesh Patil">
        </div>
        <div class="field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Minimum 6 characters" required>
        </div>
        <div class="field">
          <label>Confirm Password</label>
          <input type="password" name="confirm" placeholder="Repeat password" required>
        </div>
        <button type="submit" class="btn" <?= !$tablesOk ? 'disabled' : '' ?>>
          Create Admin & Go to Login →
        </button>
      </form>

      <?php if ($existingUsers > 0): ?>
      <div class="check warn" style="margin-top:1rem">
        ⚠ <?= $existingUsers ?> user(s) already exist. You can add another admin or
        <a href="index.php" style="color:#fcd34d">go to login directly →</a>
      </div>
      <?php endif; ?>

    <?php endif; ?>

    <?php endif; ?>
  </div>
</div>
</body>
</html>
