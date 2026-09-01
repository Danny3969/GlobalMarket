<?php
// Configuración de Seguridad - GlobalMarket GM Admin Console
if (!defined('GM_ADMIN_INIT')) {
    exit('Acceso directo no permitido');
}

return [
    'admin_user' => 'admin',
    // Hash por defecto de: GlobalMarket2026!
    'password_hash' => 'y.b8HjJ3o.c5e7bWjR3M9xZ8v7p9q1z3w5e7r9t1u',
    'password_plain_fallback' => 'GlobalMarket2026!', // Se actualizará al primer cambio
    'session_name' => 'GM_ADMIN_SESS',
    'session_lifetime' => 86400 * 7 // 7 días
];
