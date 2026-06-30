<?php
// Beauty Shop project configuration
// Put this folder inside xampp/htdocs/beauty_shop and import database/beauty_shop.sql

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'beauty_shop');

// Change this only if your folder name is different in htdocs.
define('BASE_URL', '/beauty_shop/');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Database connection failed. Please import database/beauty_shop.sql and check config/config.php. Error: ' . htmlspecialchars($e->getMessage()));
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function money($amount) {
    return '৳' . number_format((float)$amount, 2);
}

function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash() {
    if (!empty($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($type) . '">' . e($message) . '</div>';
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Please login first.');
        redirect('login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        set_flash('danger', 'Admin access required.');
        redirect('login.php');
    }
}

function cart_count() {
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}

function get_cart_total() {
    $total = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $total += (float)$item['price'] * (int)$item['quantity'];
    }
    return $total;
}

function ensure_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    $token = ensure_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

function verify_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die('Invalid request token. Please go back and try again.');
        }
    }
}
?>
