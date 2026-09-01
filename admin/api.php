<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/builder.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

// Public endpoint for quote form submissions from the frontend
if ($action === 'submit_quote') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;
    
    $quotesFile = __DIR__ . '/data/quotes.json';
    $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];

    $newQuote = [
        'id' => uniqid('q_'),
        'date' => date('Y-m-d H:i'),
        'client_name' => htmlspecialchars($data['client_name'] ?? 'Cliente Anónimo'),
        'email' => htmlspecialchars($data['email'] ?? ''),
        'phone' => htmlspecialchars($data['phone'] ?? ''),
        'product' => htmlspecialchars($data['product'] ?? 'General'),
        'destination' => htmlspecialchars($data['destination'] ?? 'N/A'),
        'volume' => htmlspecialchars($data['volume'] ?? '1 Contenedor FCL'),
        'incoterm' => htmlspecialchars($data['incoterm'] ?? 'CIF'),
        'message' => htmlspecialchars($data['message'] ?? ''),
        'status' => 'nuevo'
    ];

    array_unshift($quotes, $newQuote);
    file_put_contents($quotesFile, json_encode($quotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['success' => true, 'quote' => $newQuote]);
    exit;
}

// All remaining endpoints require authentication
require_auth();

$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'get_data':
        echo json_encode([
            'success' => true,
            'products' => get_products_data(),
            'settings' => get_site_settings(),
            'home' => get_home_content(),
            'menu' => get_menu_items()
        ]);
        break;

    case 'save_product_text':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['product_id'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        $products = get_products_data();
        $targetIndex = -1;
        foreach ($products as $idx => $p) {
            if ($p['id'] === $data['product_id']) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
            exit;
        }

        // Update fields
        $fields = [
            'name_es', 'scientific', 'badge_es', 'origin_es', 'tagline_es',
            'grade_es', 'calibers_es', 'length_es', 'brix_es', 'pack_es',
            'temp_es', 'vent_es', 'shelf_es', 'pallet_es', 'certs_es'
        ];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $products[$targetIndex][$f] = trim($data[$f]);
            }
        }

        save_products_data($products);
        $settings = get_site_settings();
        rebuild_product_page($products[$targetIndex], $settings);
        rebuild_home_page(get_home_content(), $products, $settings);

        echo json_encode(['success' => true, 'product' => $products[$targetIndex]]);
        break;

    case 'save_home_content':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            exit;
        }

        save_home_content($data);
        $products = get_products_data();
        $settings = get_site_settings();
        rebuild_home_page($data, $products, $settings);

        echo json_encode(['success' => true, 'message' => 'Página de Inicio actualizada y publicada']);
        break;

    case 'save_menu':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !is_array($data)) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos del menú']);
            exit;
        }

        save_menu_items($data);
        rebuild_all_pages();

        echo json_encode(['success' => true, 'message' => 'Menú de navegación actualizado y publicado']);
        break;

    case 'upload_image':
        if (empty($_FILES['image']) || empty($_POST['product_id'])) {
            echo json_encode(['success' => false, 'error' => 'Archivo o ID de fruta no especificado']);
            exit;
        }

        $productId = $_POST['product_id'];
        $products = get_products_data();
        $targetIndex = -1;
        foreach ($products as $idx => $p) {
            if ($p['id'] === $productId) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['success' => false, 'error' => 'Fruta no encontrada']);
            exit;
        }

        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            echo json_encode(['success' => false, 'error' => 'Formato no soportado (use JPG, PNG o WebP)']);
            exit;
        }

        $rootDir = dirname(__DIR__);
        $slug = $productId;
        $targetSubdir = 'assets/images/' . $slug;
        $targetDir = $rootDir . '/' . $targetSubdir;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $uniqueName = $slug . '-' . time() . '-' . rand(100, 999);
        $originalFilename = $uniqueName . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
        $squareFilename = $uniqueName . '-sq.jpg';

        $destOriginal = $targetDir . '/' . $originalFilename;
        $destSquare = $targetDir . '/' . $squareFilename;

        if (!move_uploaded_file($file['tmp_name'], $destOriginal)) {
            echo json_encode(['success' => false, 'error' => 'Error al guardar el archivo en el servidor']);
            exit;
        }

        // Automatic 1:1 square crop with GD
        $imgResource = null;
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $imgResource = @imagecreatefromjpeg($destOriginal);
        } elseif ($ext === 'png') {
            $imgResource = @imagecreatefrompng($destOriginal);
        } elseif ($ext === 'webp') {
            $imgResource = @imagecreatefromwebp($destOriginal);
        }

        if ($imgResource) {
            $origW = imagesx($imgResource);
            $origH = imagesy($imgResource);
            $minDim = min($origW, $origH);
            $cropX = (int)(($origW - $minDim) / 2);
            $cropY = (int)(($origH - $minDim) / 2);

            $sqThumb = imagecreatetruecolor(800, 800);
            imagecopyresampled($sqThumb, $imgResource, 0, 0, $cropX, $cropY, 800, 800, $minDim, $minDim);
            imagejpeg($sqThumb, $destSquare, 92);
            imagedestroy($sqThumb);
            imagedestroy($imgResource);
        } else {
            copy($destOriginal, $destSquare);
        }

        $webPath = $targetSubdir . '/' . $originalFilename;

        // Add to product gallery
        if (!isset($products[$targetIndex]['gallery']) || !is_array($products[$targetIndex]['gallery'])) {
            $products[$targetIndex]['gallery'] = [];
        }
        $products[$targetIndex]['gallery'][] = $webPath;

        save_products_data($products);
        $settings = get_site_settings();
        rebuild_product_page($products[$targetIndex], $settings);
        rebuild_home_page(get_home_content(), $products, $settings);

        echo json_encode([
            'success' => true,
            'gallery' => $products[$targetIndex]['gallery'],
            'new_image' => $webPath
        ]);
        break;

    case 'delete_image':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['product_id']) || !isset($data['index'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        $productId = $data['product_id'];
        $index = (int)$data['index'];
        $imageUrl = $data['image_url'] ?? '';

        $products = get_products_data();
        $targetIndex = -1;
        foreach ($products as $idx => $p) {
            if ($p['id'] === $productId) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
            exit;
        }

        $gallery = $products[$targetIndex]['gallery'] ?? [];
        if (isset($gallery[$index])) {
            $removedUrl = $gallery[$index];
            array_splice($gallery, $index, 1);
            $products[$targetIndex]['gallery'] = array_values($gallery);

            // If this was the main image, change main to first available
            if (!empty($products[$targetIndex]['gallery'])) {
                if (strtok($products[$targetIndex]['img'], '?') === strtok($removedUrl, '?')) {
                    $products[$targetIndex]['img'] = $products[$targetIndex]['gallery'][0];
                }
            }

            save_products_data($products);
            $settings = get_site_settings();
            rebuild_product_page($products[$targetIndex], $settings);
            rebuild_home_page(get_home_content(), $products, $settings);

            // Try to delete physical file and thumb
            $cleanUrl = strtok($removedUrl, '?');
            $fullOriginal = dirname(__DIR__) . '/' . $cleanUrl;
            $pathInfo = pathinfo($fullOriginal);
            $fullSquare = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-sq.jpg';
            @unlink($fullOriginal);
            @unlink($fullSquare);

            echo json_encode(['success' => true, 'gallery' => $products[$targetIndex]['gallery']]);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Índice de foto no encontrado']);
        break;

    case 'set_main_image':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['product_id']) || empty($data['image_url'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            exit;
        }

        $products = get_products_data();
        $targetIndex = -1;
        foreach ($products as $idx => $p) {
            if ($p['id'] === $data['product_id']) {
                $targetIndex = $idx;
                break;
            }
        }

        if ($targetIndex === -1) {
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
            exit;
        }

        $products[$targetIndex]['img'] = strtok($data['image_url'], '?');
        save_products_data($products);
        $settings = get_site_settings();
        rebuild_product_page($products[$targetIndex], $settings);
        rebuild_home_page(get_home_content(), $products, $settings);

        echo json_encode(['success' => true, 'main_image' => $products[$targetIndex]['img']]);
        break;

    case 'save_site_settings':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
            exit;
        }

        $settings = get_site_settings();
        $settings = array_merge($settings, $data);
        save_site_settings($settings);
        rebuild_all_pages();

        echo json_encode(['success' => true, 'settings' => $settings]);
        break;

    case 'get_quotes':
        $quotesFile = __DIR__ . '/data/quotes.json';
        $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
        echo json_encode(['success' => true, 'quotes' => $quotes]);
        break;

    case 'delete_quote':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID no especificado']);
            exit;
        }

        $quotesFile = __DIR__ . '/data/quotes.json';
        $quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
        $quotes = array_values(array_filter($quotes, function($q) use ($data) {
            return $q['id'] !== $data['id'];
        }));

        file_put_contents($quotesFile, json_encode($quotes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true, 'quotes' => $quotes]);
        break;

    case 'change_password':
        $data = json_decode(file_get_contents('php://input'), true);
        $current = $data['current_password'] ?? '';
        $new = $data['new_password'] ?? '';

        if (empty($current) || empty($new) || strlen($new) < 6) {
            echo json_encode(['success' => false, 'error' => 'La nueva contraseña debe tener al menos 6 caracteres']);
            exit;
        }

        $user = $_SESSION['gm_admin_user'] ?? 'admin';
        if (!verify_credentials($user, $current)) {
            echo json_encode(['success' => false, 'error' => 'La contraseña actual es incorrecta']);
            exit;
        }

        if (update_password($user, $new)) {
            echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo guardar la nueva contraseña']);
        }
        break;

    case 'rebuild_all':
        $count = rebuild_all_pages();
        echo json_encode(['success' => true, 'message' => "Sitio web completo recompilado ({$count} páginas actualizadas)"]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
        break;
}
