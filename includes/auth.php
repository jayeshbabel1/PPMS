<?php
require_once __DIR__.'/config.php';

function session_start_safe(): void {
    if (session_status()===PHP_SESSION_NONE) {
        session_set_cookie_params(['lifetime'=>SESSION_LIFETIME,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        session_start();
    }
}
function is_logged_in(): bool {
    session_start_safe();
    if (empty($_SESSION['user_id'])) return false;
    if (isset($_SESSION['last_active'])&&(time()-$_SESSION['last_active'])>SESSION_LIFETIME) {
        session_unset(); session_destroy(); return false;
    }
    $_SESSION['last_active']=time();
    return true;
}
function require_login(): void {
    if (!is_logged_in()) { header('Location: index.php?page=login'); exit; }
}
function current_user(): array {
    session_start_safe();
    return ['id'=>$_SESSION['user_id']??0,'username'=>$_SESSION['username']??'',
            'full_name'=>$_SESSION['full_name']??'','role'=>$_SESSION['role']??'viewer'];
}
function is_admin(): bool { return current_user()['role']==='admin'; }
function is_developer(): bool { return current_user()['role']==='developer'; }
function is_viewer(): bool { return current_user()['role']==='viewer'; }
function login(string $u, string $p): bool {
    $s=db()->prepare('SELECT id,username,password,full_name,role,is_active FROM users WHERE username=? LIMIT 1');
    $s->execute([$u]); $user=$s->fetch();
    if (!$user||!$user['is_active']||!password_verify($p,$user['password'])) return false;
    session_start_safe(); session_regenerate_id(true);
    $_SESSION['user_id']=$user['id']; $_SESSION['username']=$user['username'];
    $_SESSION['full_name']=$user['full_name']??$user['username']; $_SESSION['role']=$user['role'];
    $_SESSION['last_active']=time();
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
function audit(string $action,string $table,?int $rid,string $detail=''): void {
    try {
        $u=current_user();
        db()->prepare('INSERT INTO audit_log(user_id,action,table_name,record_id,detail,ip_address)VALUES(?,?,?,?,?,?)')->execute([$u['id']?:null,$action,$table,$rid,$detail,$_SERVER['REMOTE_ADDR']??null]);
    } catch(Throwable){}
}
function csrf_token(): string {
    session_start_safe();
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token']=bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_verify(): void {
    session_start_safe();
    if (!hash_equals($_SESSION['csrf_token']??'',$_POST['csrf_token']??'')) {
        http_response_code(403); die('Invalid CSRF token.');
    }
}
function get_active_subscription(): ?array {
    static $cache=false;
    if ($cache!==false) return $cache?:null;
    $uid=current_user()['id']; if(!$uid){$cache=null;return null;}
    try {
        $s=db()->prepare('SELECT * FROM subscriptions WHERE user_id=? AND is_active=1 AND end_date>=CURDATE() ORDER BY end_date DESC LIMIT 1');
        $s->execute([$uid]); $cache=$s->fetch()?:null;
    } catch(Throwable){$cache=null;}
    return $cache;
}
function can_view_basic(): bool  { return is_admin()||get_active_subscription()!==null; }
function can_view_advance(): bool {
    if (is_admin()) return true;
    $s=get_active_subscription();
    return $s&&$s['plan_type']==='advance';
}
function get_plan_label(): string {
    if (is_admin()) return 'Administrator';
    $s=get_active_subscription();
    if (!$s) return 'No Active Plan';
    return ucfirst($s['plan_type']).' &middot; '.ucfirst($s['billing_cycle']);
}
function get_setting(string $key, string $default=''): string {
    try {
        $s=db()->prepare('SELECT val FROM app_settings WHERE `key`=? LIMIT 1');
        $s->execute([$key]); $r=$s->fetch();
        return $r ? $r['val'] : $default;
    } catch(Throwable){ return $default; }
}
function set_setting(string $key, string $val): void {
    db()->prepare('INSERT INTO app_settings(`key`,val) VALUES(?,?) ON DUPLICATE KEY UPDATE val=VALUES(val)')->execute([$key,$val]);
}

/**
 * Check if current user has a specific permission feature.
 * Falls back to role-based defaults if permissions table not populated.
 */
function can_do(string $feature): bool {
    $role=current_user()['role'];
    if($role==='admin') return true; // admin always can
    try {
        $s=db()->prepare('SELECT `admin`,`developer`,`adv_viewer`,`bas_viewer` FROM permissions WHERE feature=? LIMIT 1');
        $s->execute([$feature]);$row=$s->fetch();
        if(!$row) return false;
        if($role==='developer') return (bool)$row['developer'];
        // viewer — check subscription
        $sub=get_active_subscription();
        if(!$sub) return false;
        return $sub['plan_type']==='advance'?(bool)$row['adv_viewer']:(bool)$row['bas_viewer'];
    } catch(Throwable){ return false; }
}

/** Get all theme CSS variable values from settings */
function get_theme_vars(): array {
    $defaults=['theme_primary'=>'#81A6C6','theme_bg'=>'#F3E3D0','theme_surface'=>'#FFFFFF',
               'theme_border'=>'#D2C4B4','theme_btn_text'=>'#FFFFFF','theme_heading'=>'#2C3A4A',
               'theme_text'=>'#4A5E70','theme_sidebar_bg'=>'#FFFFFF','theme_topbar_bg'=>'#FFFFFF',
               'theme_c1'=>'#81A6C6','theme_c2'=>'#AACDDC','theme_c3'=>'#F3E3D0','theme_c4'=>'#D2C4B4'];
    $out=[];
    foreach($defaults as $k=>$def){ $out[$k]=get_setting($k,$def); }
    return $out;
}