<?php
define('DB_HOST',    'localhost');
define('DB_NAME',    'pms_db');
define('DB_USER',    'pms_user');
define('DB_PASS',    'YOUR_DB_PASSWORD');
define('DB_CHARSET', 'utf8mb4');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', 'uploads/');
define('MAX_FILE_MB', 10);
define('APP_NAME',   'PMS');
define('APP_BRAND',  'PMS By Mingosoft Technologies');
define('APP_VER',    '4.0');
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
