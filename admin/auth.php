<?php
// GlobalMarket GM - Módulo de Autenticación y Seguridad
define('GM_ADMIN_INIT', true);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('GM_ADMIN_SESS');
    session_start();
}

function get_admin_config() {
    $configFile = __DIR__ . '/data/config.php';
    if (file_exists($configFile)) {
        return require $configFile;
    }
    return [
        'admin_user' => 'admin',
        'password_plain_fallback' => 'GlobalMarket2026!'
    ];
}

function save_admin_config($config) {
    $configFile = __DIR__ . '/data/config.php';
    $export = var_export($config, true);
    $content = "<?php\nif (!defined('GM_ADMIN_INIT')) exit('Acceso directo no permitido');\nreturn " . $export . ";\n";
    return file_put_contents($configFile, $content);
}

function is_logged_in() {
    return !empty($_SESSION['gm_admin_logged']) && $_SESSION['gm_admin_logged'] === true;
}

function require_auth() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function verify_admin_login($username, $password) {
    $config = get_admin_config();
    if ($username !== $config['admin_user']) {
        return false;
    }
    
    // Verificar con password_verify si existe hash válido
    if (!empty($config['password_hash']) && password_verify($password, $config['password_hash'])) {
        return true;
    }
    
    // Fallback de contraseña inicial si aún no se ha hasheado
    if (!empty($config['password_plain_fallback']) && $password === $config['password_plain_fallback']) {
        // Actualizar a hash seguro automáticamente
        $config['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        unset($config['password_plain_fallback']);
        save_admin_config($config);
        return true;
    }
    
    return false;
}

function get_csrf_token() {
    if (empty($_SESSION['gm_csrf_token'])) {
        $_SESSION['gm_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['gm_csrf_token'];
}

function verify_csrf_token($token) {
    return !empty($_SESSION['gm_csrf_token']) && hash_equals($_SESSION['gm_csrf_token'], $token);
}
