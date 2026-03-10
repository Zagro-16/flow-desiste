<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'FormaFlow By Desiste');
define('BASE_URL', '/');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('STAMP_PATH', ROOT_PATH . '/assets/stamps');
define('TIMEZONE', 'Europe/Rome');

date_default_timezone_set(TIMEZONE);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
