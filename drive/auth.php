<?php
// GlobalMarket GM - Cloud Drive Authentication & Role Manager
define('GM_DRIVE_INIT', true);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_name('GM_DRIVE_SESS');
    session_start();
}

function get_drive_users_file() {
    return __DIR__ . '/data/users.json';
}

function get_drive_users() {
    $file = get_drive_users_file();
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

function save_drive_users($users) {
    $file = get_drive_users_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_logged_drive_user() {
    if (!empty($_SESSION['gm_drive_user'])) {
        return $_SESSION['gm_drive_user'];
    }
    return null;
}

function is_drive_logged_in() {
    return !empty($_SESSION['gm_drive_logged']) && $_SESSION['gm_drive_logged'] === true;
}

function is_drive_admin() {
    $user = get_logged_drive_user();
    return $user && ($user['role'] === 'admin');
}

function require_drive_auth() {
    if (!is_drive_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_drive_admin() {
    require_drive_auth();
    if (!is_drive_admin()) {
        http_response_code(403);
        die('Acceso denegado: Se requieren permisos de Administrador.');
    }
}

function verify_drive_login($username, $password) {
    $users = get_drive_users();
    $username = trim($username);

    foreach ($users as $index => $u) {
        if ($u['username'] === $username) {
            if (isset($u['status']) && $u['status'] === 'inactive') {
                return ['success' => false, 'error' => 'Usuario desactivado. Contacte al administrador.'];
            }

            $passwordMatches = false;

            if (!empty($u['password_hash']) && password_verify($password, $u['password_hash'])) {
                $passwordMatches = true;
            } elseif (!empty($u['password_plain_fallback']) && $password === $u['password_plain_fallback']) {
                $passwordMatches = true;
                // Auto-upgrade to secure hash
                $users[$index]['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                unset($users[$index]['password_plain_fallback']);
                save_drive_users($users);
            }

            if ($passwordMatches) {
                $_SESSION['gm_drive_logged'] = true;
                $_SESSION['gm_drive_user'] = [
                    'id' => $u['id'],
                    'username' => $u['username'],
                    'name' => $u['name'],
                    'email' => $u['email'] ?? '',
                    'role' => $u['role'] ?? 'client',
                    'allowed_folders' => $u['allowed_folders'] ?? ['*']
                ];
                return ['success' => true, 'user' => $_SESSION['gm_drive_user']];
            } else {
                return ['success' => false, 'error' => 'Contraseña incorrecta'];
            }
        }
    }

    return ['success' => false, 'error' => 'Usuario no encontrado'];
}

function logout_drive_user() {
    $_SESSION['gm_drive_logged'] = false;
    unset($_SESSION['gm_drive_user']);
    session_destroy();
}
