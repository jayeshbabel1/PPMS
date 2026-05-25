<?php
// ============================================================
//  includes/config.php
// ============================================================
define('DB_HOST',     'localhost');
define('DB_NAME',     'mingos_pms');
define('DB_USER',     'mingos_pmsu');        // DB username
define('DB_PASS',     'TixxHOTTXn9A');   // DB password
define('DB_CHARSET',  'utf8mb4');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', 'uploads/');
define('MAX_FILE_MB', 10);
define('APP_NAME',   'PMS');
define('APP_BRAND',  'PMS By Mingosoft Technologies');
define('APP_VER',    '3.0');
define('SESSION_LIFETIME', 3600 * 8);
define('PLAN_BASIC_MONTHLY',    500);
define('PLAN_BASIC_YEARLY',    5000);
define('PLAN_ADVANCE_MONTHLY', 1200);
define('PLAN_ADVANCE_YEARLY', 12000);
 
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET,
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES=>false]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die('DB connection failed: '.$e->getMessage());
        }
    }
    return $pdo;
}

function get_setting(string $key, string $default = ''): string {
    static $cache = [];
    if (array_key_exists($key, $cache)) return $cache[$key];
    try {
        $s = db()->prepare('SELECT val FROM app_settings WHERE `key`=? LIMIT 1');
        $s->execute([$key]);
        $r = $s->fetch();
        $cache[$key] = $r ? $r['val'] : $default;
        return $cache[$key];
    } catch (Throwable) { return $default; }
}

function set_setting(string $key, string $val): void {
    db()->prepare('INSERT INTO app_settings(`key`,val) VALUES(?,?) ON DUPLICATE KEY UPDATE val=VALUES(val)')->execute([$key,$val]);
}

// ── Permission helpers ─────────────────────────────────────────
function get_permission(string $feature): array {
    static $all = null;
    if ($all === null) {
        $all = [];
        try { foreach (db()->query('SELECT * FROM permissions') as $row) $all[$row['feature']] = $row; } catch(Throwable){}
    }
    return $all[$feature] ?? ['admin'=>1,'developer'=>0,'adv_viewer'=>0,'bas_viewer'=>0];
}

function has_permission(string $feature): bool {
    $u = function_exists('current_user') ? current_user() : ['role'=>''];
    $p = get_permission($feature);
    if ($u['role'] === 'admin')     return (bool)$p['admin'];
    if ($u['role'] === 'developer') return (bool)$p['developer'];
    if ($u['role'] === 'viewer') {
        $sub = function_exists('get_active_subscription') ? get_active_subscription() : null;
        if (!$sub) return false;
        return (bool)($sub['plan_type']==='advance' ? $p['adv_viewer'] : $p['bas_viewer']);
    }
    return false;
}

// ── Theme CSS (from DB settings) ───────────────────────────────
function get_theme_css(): string {
    $p   = get_setting('theme_primary',    '#81A6C6');
    $bg  = get_setting('theme_bg',         '#F3E3D0');
    $sur = get_setting('theme_surface',    '#FFFFFF');
    $bor = get_setting('theme_border',     '#D2C4B4');
    $bt  = get_setting('theme_btn_text',   '#FFFFFF');
    $h   = get_setting('theme_heading',    '#2C3A4A');
    $t   = get_setting('theme_text',       '#4A5E70');
    $sdb = get_setting('theme_sidebar_bg', '#FFFFFF');
    $tob = get_setting('theme_topbar_bg',  '#FFFFFF');

    // Derive shades from primary
    [$pr,$pg,$pb] = array_map('hexdec', str_split(ltrim($p,'#'),2));
    $ph  = sprintf('#%02x%02x%02x', max(0,$pr-18), max(0,$pg-18), max(0,$pb-18));
    $pd  = sprintf('#%02x%02x%02x', max(0,$pr-34), max(0,$pg-34), max(0,$pb-34));
    $pbg = sprintf('#%02x%02x%02x', min(255,$pr+58), min(255,$pg+58), min(255,$pb+58));
    // Derive surface2 from surface
    [$sr,$sg,$sb] = array_map('hexdec', str_split(ltrim($sur,'#'),2));
    $sur2= sprintf('#%02x%02x%02x', max(0,$sr-9), max(0,$sg-9), max(0,$sb-9));
    // border2
    [$brr,$brg,$brb]=array_map('hexdec',str_split(ltrim($bor,'#'),2));
    $bor2=sprintf('#%02x%02x%02x',max(0,$brr-14),max(0,$brg-14),max(0,$brb-14));

    return ":root{
  --bg:{$bg};--bg2:#FAF5EF;
  --surface:{$sur};--surface2:{$sur2};--surface3:#EFE8DF;
  --border:{$bor};--border2:{$bor2};
  --primary:{$p};--primary-h:{$ph};--primary-d:{$pd};--primary-bg:{$pbg};
  --sec:#AACDDC;--sec-bg:#E8F4F8;
  --gold:#C8956C;--gold-bg:#FBF0E8;--gold-s:#A87040;
  --green:#5A9E6F;--green-bg:#EAF5EE;
  --red:#C05050;--red-bg:#FAEAEA;
  --t1:{$h};--t2:{$t};--t3:#7A8F9E;--t4:#B0BEC8;
  --btn-text:{$bt};
  --sidebar-bg:{$sdb};--topbar-bg:{$tob};
  --sh:0 2px 8px rgba(44,58,74,.10);--sh-md:0 4px 20px rgba(44,58,74,.13);--sh-lg:0 8px 40px rgba(44,58,74,.16);
  --r:8px;--r-sm:5px;--r-lg:12px;--r-xl:18px;
  --sidebar:240px;--hdr:58px;
}";
}

// ── Error email ────────────────────────────────────────────────
function try_error_mail(string $subj, string $body): void {
    static $busy = false;
    if ($busy) return; $busy = true;
    try {
        if (get_setting('mail_error_notify','0') !== '1') { $busy=false; return; }
        $to = get_setting('mail_admin_email','');
        if (!$to) { $busy=false; return; }
        if (file_exists(__DIR__.'/mailer.php')) {
            require_once __DIR__.'/mailer.php';
            pms_mail($to, '[PMS ERROR] '.$subj,
                "<h3>PMS Application Error</h3><pre>".htmlspecialchars($body)."</pre><p><small>".date('Y-m-d H:i:s')."</small></p>");
        }
    } catch (Throwable) {}
    $busy = false;
}

set_error_handler(function(int $no, string $str, string $file, int $line): bool {
    if (!($no & error_reporting())) return false;
    if (in_array($no, [E_NOTICE, E_USER_NOTICE, E_STRICT, E_DEPRECATED])) return false;
    try_error_mail("PHP Error: $str", "[$no] $str\nFile: $file:$line");
    return false;
});
set_exception_handler(function(Throwable $e): void {
    try_error_mail('Exception: '.$e->getMessage(), $e->getFile().':'.$e->getLine()."\n".$e->getTraceAsString());
    http_response_code(500);
    echo '<div style="font-family:sans-serif;padding:2rem;color:#c05050;border:1px solid #e0a0a0;border-radius:8px;max-width:600px;margin:2rem auto"><h2>Application Error</h2><p>The administrator has been notified. Please try again later.</p></div>';
});