<?php
// GlobalMarket GM - Admin API Endpoint
define('GM_ADMIN_INIT', true);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/builder.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Public endpoint: Submit Quote from frontend website
if ($action === 'submit_quote') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $quote = [
        'id' => uniqid('quote_'),
        'date' => date('Y-m-d H:i:s'),
        'product' => trim($input['product'] ?? 'General'),
        'client_name' => trim($input['client_name'] ?? $input['name'] ?? ''),
        'email' => trim($input['email'] ?? ''),
        'phone' => trim($input['phone'] ?? ''),
        'destination' => trim($input['destination'] ?? ''),
        'volume' => trim($input['volume'] ?? ''),
        'incoterm' => trim($input['incoterm'] ?? 'CIF'),
        'message' => trim($input['message'] ?? ''),
        'status' => 'new' // new, reviewed, answered
    ];

    if (empty($quote['client_name']) || empty($quote['email'])) {
        echo json_encode(['success' => false, 'error' => 'Nombre y correo son requeridos']);
        exit;
    }

    $quotesFile = __DIR__ . '/data/quotes.json';
    $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
    array_unshift($quotes, $quote);
    file_put_contents($quotesFile, json_encode($quotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['success' => true, 'message' => 'Cotización guardada exitosamente']);
    exit;
}

// All following actions require active admin session
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado. Inicie sesión.']);
    exit;
}

// 1. OBTENER DATOS DE PRODUCTOS Y AJUSTES
if ($action === 'get_data') {
    $products = get_products_data();
    $settings = get_site_settings();
    $quotesFile = __DIR__ . '/data/quotes.json';
    $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'settings' => $settings,
        'quotes_count' => count($quotes)
    ]);
    exit;
}

// 2. SUBIR FOTO A GALERÍA DE FRUTA CON RECORTE 1:1 AUTOMÁTICO
if ($action === 'upload_image') {
    $productId = $_POST['product_id'] ?? '';
    if (empty($productId) || empty($_FILES['image']['tmp_name'])) {
        echo json_encode(['success' => false, 'error' => 'Falta fruta o archivo de imagen']);
        exit;
    }

    $products = get_products_data();
    $productKey = null;
    foreach ($products as $k => $p) {
        if ($p['id'] === $productId) {
            $productKey = $k;
            break;
        }
    }

    if ($productKey === null) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        exit;
    }

    $uploadDir = dirname(__DIR__) . '/assets/images/' . $productId;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $tmpFile = $_FILES['image']['tmp_name'];
    $origName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($_FILES['image']['name']));
    $baseName = pathinfo($origName, PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        echo json_encode(['success' => false, 'error' => 'Formato no soportado. Usa JPG, PNG o WebP.']);
        exit;
    }

    $slug = $productId . '-' . time() . '-' . rand(100, 999);
    $destFileName = $slug . '.jpg';
    $destSqFileName = $slug . '-sq.jpg';
    $destPath = $uploadDir . '/' . $destFileName;
    $destSqPath = $uploadDir . '/' . $destSqFileName;

    // Cargar imagen con GD
    $srcImg = null;
    if ($ext === 'png') {
        $srcImg = imagecreatefrompng($tmpFile);
    } else if ($ext === 'webp') {
        $srcImg = imagecreatefromwebp($tmpFile);
    } else {
        $srcImg = imagecreatefromjpeg($tmpFile);
    }

    if (!$srcImg) {
        echo json_encode(['success' => false, 'error' => 'Error al procesar la imagen subida']);
        exit;
    }

    $w = imagesx($srcImg);
    $h = imagesy($srcImg);

    // Guardar original en JPG alta calidad
    $bgImg = imagecreatetruecolor($w, $h);
    $white = imagecolorallocate($bgImg, 255, 255, 255);
    imagefill($bgImg, 0, 0, $white);
    imagecopy($bgImg, $srcImg, 0, 0, 0, 0, $w, $h);
    imagejpeg($bgImg, $destPath, 94);
    imagedestroy($bgImg);

    // Generar versión cuadrada uniforme 1:1 (800x800 px)
    $minDim = min($w, $h);
    $cropX = ($w - $minDim) / 2;
    $cropY = ($h - $minDim) / 2;

    $sqImg = imagecreatetruecolor(800, 800);
    $whiteSq = imagecolorallocate($sqImg, 255, 255, 255);
    imagefill($sqImg, 0, 0, $whiteSq);
    imagecopyresampled($sqImg, $srcImg, 0, 0, $cropX, $cropY, 800, 800, $minDim, $minDim);
    imagejpeg($sqImg, $destSqPath, 94);
    imagedestroy($sqImg);
    imagedestroy($srcImg);

    // Actualizar JSON y compilar página HTML
    $relPath = 'assets/images/' . $productId . '/' . $destFileName;
    if (!isset($products[$productKey]['gallery'])) {
        $products[$productKey]['gallery'] = [];
    }
    $products[$productKey]['gallery'][] = $relPath;

    save_products_data($products);
    $settings = get_site_settings();
    rebuild_product_page($products[$productKey], $settings);

    echo json_encode([
        'success' => true,
        'message' => 'Imagen subida, redimensionada a 1:1 y publicada con éxito',
        'image' => $relPath . '?v=' . time(),
        'gallery' => $products[$productKey]['gallery']
    ]);
    exit;
}

// 3. ELIMINAR FOTO DE GALERÍA
if ($action === 'delete_image') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $productId = $input['product_id'] ?? '';
    $imgIndex = isset($input['index']) ? (int)$input['index'] : -1;
    $imgUrl = $input['image_url'] ?? '';

    $products = get_products_data();
    $productKey = null;
    foreach ($products as $k => $p) {
        if ($p['id'] === $productId) {
            $productKey = $k;
            break;
        }
    }

    if ($productKey === null) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        exit;
    }

    $gallery = $products[$productKey]['gallery'] ?? [];

    if ($imgIndex >= 0 && isset($gallery[$imgIndex])) {
        array_splice($gallery, $imgIndex, 1);
    } else if (!empty($imgUrl)) {
        $cleanTarget = strtok($imgUrl, '?');
        $gallery = array_values(array_filter($gallery, function($item) use ($cleanTarget) {
            return strtok($item, '?') !== $cleanTarget;
        }));
    }

    $products[$productKey]['gallery'] = $gallery;
    save_products_data($products);
    $settings = get_site_settings();
    rebuild_product_page($products[$productKey], $settings);

    echo json_encode([
        'success' => true,
        'message' => 'Imagen eliminada correctamente de la galería',
        'gallery' => $gallery
    ]);
    exit;
}

// 4. ESTABLECER FOTO PRINCIPAL (FICHA TÉCNICA)
if ($action === 'set_main_image') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $productId = $input['product_id'] ?? '';
    $imgUrl = $input['image_url'] ?? '';

    $products = get_products_data();
    $productKey = null;
    foreach ($products as $k => $p) {
        if ($p['id'] === $productId) {
            $productKey = $k;
            break;
        }
    }

    if ($productKey === null || empty($imgUrl)) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }

    $products[$productKey]['img'] = strtok($imgUrl, '?');
    save_products_data($products);
    $settings = get_site_settings();
    rebuild_product_page($products[$productKey], $settings);

    echo json_encode([
        'success' => true,
        'message' => 'Foto principal de la ficha técnica actualizada',
        'img' => $products[$productKey]['img']
    ]);
    exit;
}

// 5. GUARDAR TEXTOS Y ESPECIFICACIONES DE PRODUCTO
if ($action === 'save_product_text') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $productId = $input['product_id'] ?? '';

    $products = get_products_data();
    $productKey = null;
    foreach ($products as $k => $p) {
        if ($p['id'] === $productId) {
            $productKey = $k;
            break;
        }
    }

    if ($productKey === null) {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
        exit;
    }

    // Actualizar campos
    $fields = ['name_es', 'scientific', 'tagline_es', 'origin_es', 'grade_es', 'calibers_es', 'length_es', 'pack_es', 'temp_es', 'vent_es', 'shelf_es', 'pallet_es', 'brix_es', 'certs_es', 'badge_es'];
    foreach ($fields as $f) {
        if (isset($input[$f])) {
            $products[$productKey][$f] = trim($input[$f]);
        }
    }

    save_products_data($products);
    $settings = get_site_settings();
    rebuild_product_page($products[$productKey], $settings);

    echo json_encode([
        'success' => true,
        'message' => 'Información de la fruta guardada y publicada en la web'
    ]);
    exit;
}

// 6. GUARDAR AJUSTES GENERALES DEL SITIO
if ($action === 'save_site_settings') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $settings = get_site_settings();

    $settings['phone'] = trim($input['phone'] ?? $settings['phone']);
    $settings['whatsapp'] = preg_replace('/[^0-9]/', '', $input['whatsapp'] ?? $settings['whatsapp']);
    $settings['email'] = trim($input['email'] ?? $settings['email']);
    $settings['address'] = trim($input['address'] ?? $settings['address']);
    $settings['certs_badge'] = trim($input['certs_badge'] ?? $settings['certs_badge']);

    if (isset($input['social'])) {
        $settings['social']['facebook'] = trim($input['social']['facebook'] ?? '#');
        $settings['social']['instagram'] = trim($input['social']['instagram'] ?? '#');
        $settings['social']['linkedin'] = trim($input['social']['linkedin'] ?? '#');
    }

    save_site_settings($settings);
    rebuild_all_pages();

    echo json_encode([
        'success' => true,
        'message' => 'Ajustes del sitio actualizados y páginas recompiladas'
    ]);
    exit;
}

// 7. GESTOR DE COTIZACIONES
if ($action === 'get_quotes') {
    $quotesFile = __DIR__ . '/data/quotes.json';
    $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
    echo json_encode(['success' => true, 'quotes' => $quotes]);
    exit;
}

if ($action === 'delete_quote') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $quoteId = $input['id'] ?? '';
    
    $quotesFile = __DIR__ . '/data/quotes.json';
    $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
    $quotes = array_values(array_filter($quotes, function($q) use ($quoteId) {
        return $q['id'] !== $quoteId;
    }));
    file_put_contents($quotesFile, json_encode($quotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['success' => true, 'message' => 'Cotización eliminada']);
    exit;
}

// 8. CAMBIAR CONTRASEÑA DE ADMINISTRADOR
if ($action === 'change_password') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $currPass = $input['current_password'] ?? '';
    $newPass = $input['new_password'] ?? '';

    if (strlen($newPass) < 6) {
        echo json_encode(['success' => false, 'error' => 'La nueva contraseña debe tener al menos 6 caracteres']);
        exit;
    }

    if (!verify_admin_login($_SESSION['gm_admin_user'] ?? 'admin', $currPass)) {
        echo json_encode(['success' => false, 'error' => 'La contraseña actual no es correcta']);
        exit;
    }

    $config = get_admin_config();
    $config['password_hash'] = password_hash($newPass, PASSWORD_DEFAULT);
    unset($config['password_plain_fallback']);
    save_admin_config($config);

    echo json_encode(['success' => true, 'message' => 'Contraseña actualizada con éxito']);
    exit;
}

// 9. RECOMPILAR TODO EL SITIO WEB MANUALMENTE
if ($action === 'rebuild_all') {
    $count = rebuild_all_pages();
    echo json_encode(['success' => true, 'message' => "Se recompilaron {$count} páginas con éxito"]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Acción no válida']);
