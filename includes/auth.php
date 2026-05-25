<?php
// ============================================================
//  includes/auth.php — PMS v4.0
// ============================================================
require_once __DIR__.'/config.php';

function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['lifetime'=>SESSION_LIFETIME,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        session_start();
    }
}

function is_logged_in(): bool {
    session_start_safe();
    if (empty($_SESSION['user_id'])) return false;
    if (isset($_SESSION['last_active']) && (time()-$_SESSION['last_active']) > SESSION_LIFETIME) {
        session_unset(); session_destroy(); return false;
    }
    $_SESSION['last_active'] = time();
    return true;
}

function require_login(): void {
    if (!is_logged_in()) { header('Location: index.php?page=login'); exit; }
}

function current_user(): array {
    session_start_safe();
    return [
        'id'        => $_SESSION['user_id']   ?? 0,
        'username'  => $_SESSION['username']  ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'role'      => $_SESSION['role']      ?? 'viewer',
        'email'     => $_SESSION['email']     ?? '',
    ];
}

function is_admin():     bool { return current_user()['role'] === 'admin'; }
function is_developer(): bool { return current_user()['role'] === 'developer'; }
function is_viewer():    bool { return current_user()['role'] === 'viewer'; }

function login(string $u, string $p): bool {
    $s = db()->prepare('SELECT id,username,password,full_name,role,email,is_active FROM users WHERE username=? LIMIT 1');
    $s->execute([$u]);
    $user = $s->fetch();
    if (!$user || !$user['is_active'] || !password_verify($p, $user['password'])) return false;
    session_start_safe(); session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['username']   = $user['username'];
    $_SESSION['full_name']  = $user['full_name'] ?? $user['username'];
    $_SESSION['role']       = $user['role'];
    $_SESSION['email']      = $user['email'] ?? '';
    $_SESSION['last_active']= time();
    db()->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$user['id']]);
    audit('login','users',$user['id'],'Logged in');
    return true;
}

function logout(): void {
    session_start_safe();
    audit('logout','users',$_SESSION['user_id']??null,'Logged out');
    session_unset(); session_destroy();
    header('Location: index.php?page=login'); exit;
}

function audit(string $action, string $table, ?int $rid, string $detail = ''): void {
    try {
        $u = current_user();
        db()->prepare('INSERT INTO audit_log(user_id,action,table_name,record_id,detail,ip_address)VALUES(?,?,?,?,?,?)')->execute([$u['id']?:null,$action,$table,$rid,$detail,$_SERVER['REMOTE_ADDR']??null]);
    } catch (Throwable) {}
}

function csrf_token(): string {
    session_start_safe();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void {
    session_start_safe();
    if (!hash_equals($_SESSION['csrf_token']??'', $_POST['csrf_token']??'')) {
        http_response_code(403); die('Invalid CSRF token.');
    }
}

// ── Subscription ───────────────────────────────────────────────
function get_active_subscription(): ?array {
    static $cache = false;
    if ($cache !== false) return $cache ?: null;
    $uid = current_user()['id'];
    if (!$uid) { $cache = null; return null; }
    try {
        $s = db()->prepare('SELECT * FROM subscriptions WHERE user_id=? AND is_active=1 AND end_date>=CURDATE() ORDER BY end_date DESC LIMIT 1');
        $s->execute([$uid]);
        $cache = $s->fetch() ?: null;
    } catch (Throwable) { $cache = null; }
    return $cache;
}

function can_view_basic():   bool { return is_admin()||is_developer()||get_active_subscription()!==null; }
function can_view_advance(): bool {
    if (is_admin()||is_developer()) return true;
    $s = get_active_subscription();
    return $s && $s['plan_type'] === 'advance';
}

function get_plan_label(): string {
    if (is_admin())     return 'Administrator';
    if (is_developer()) return 'Developer';
    $s = get_active_subscription();
    if (!$s) return 'No Active Plan';
    return ucfirst($s['plan_type']).' &middot; '.ucfirst($s['billing_cycle']);
}

// ── Forgot password ────────────────────────────────────────────
function send_password_reset(string $email): bool {
    require_once __DIR__.'/mailer.php';
    $s = db()->prepare('SELECT id,username,full_name FROM users WHERE email=? AND is_active=1 LIMIT 1');
    $s->execute([strtolower(trim($email))]);
    $user = $s->fetch();
    if (!$user) return true; // silent — don't reveal if email exists

    // Invalidate old tokens
    db()->prepare('UPDATE password_resets SET used=1 WHERE user_id=?')->execute([$user['id']]);

    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
    db()->prepare('INSERT INTO password_resets(user_id,email,token,expires_at)VALUES(?,?,?,?)')->execute([$user['id'],$email,$token,$expires]);

    $base  = (isset($_SERVER['HTTPS'])?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').strtok($_SERVER['REQUEST_URI']??'/', '?');
    $link  = rtrim(dirname($base),'/').'/index.php?page=reset_password&token='.$token;
    $name  = $user['full_name'] ?: $user['username'];

    $html = email_template('Reset Your Password',
        "<p>Hello <strong>$name</strong>,</p>
        <p>We received a request to reset your PMS password. Click the button below to set a new password. This link is valid for <strong>1 hour</strong>.</p>
        <p style='text-align:center;margin:30px 0'><a href='$link' style='background:".get_setting('theme_primary','#81A6C6').";color:#fff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700'>Reset Password</a></p>
        <p>Or copy this link:<br><small style='color:#888;word-break:break-all'>$link</small></p>
        <p>If you did not request this, ignore this email. Your password will remain unchanged.</p>"
    );

    try {
        pms_mail($email, 'Reset Your PMS Password', $html);
    } catch (Throwable $e) {
        try_error_mail('Password Reset Mail Failed', $e->getMessage());
        return false;
    }
    return true;
}

function validate_reset_token(string $token): ?array {
    $s = db()->prepare('SELECT r.*,u.username,u.full_name FROM password_resets r JOIN users u ON u.id=r.user_id WHERE r.token=? AND r.used=0 AND r.expires_at>NOW() LIMIT 1');
    $s->execute([$token]);
    return $s->fetch() ?: null;
}

function do_password_reset(string $token, string $newPass): bool {
    $reset = validate_reset_token($token);
    if (!$reset) return false;
    $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost'=>12]);
    db()->prepare('UPDATE users SET password=? WHERE id=?')->execute([$hash, $reset['user_id']]);
    db()->prepare('UPDATE password_resets SET used=1 WHERE token=?')->execute([$token]);
    audit('password_reset','users',(int)$reset['user_id'],'Password reset via email token');
    return true;
}