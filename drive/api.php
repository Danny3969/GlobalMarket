<?php
require_once __DIR__ . '/auth.php';

// Ensure user is logged in
if (!is_drive_logged_in()) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Sesión expirada o no autenticada']);
    exit;
}

$currentUser = get_logged_drive_user();
$isAdmin = is_drive_admin();
$isSuperAdmin = is_drive_superadmin();
$isCollab = is_drive_collab();
$action = $_GET['action'] ?? '';

$baseStorageDir = __DIR__ . '/data/storage';
$trashStorageDir = $baseStorageDir . '/.trash';
$trashIndexFile = __DIR__ . '/data/trash_index.json';
$favoritesFile = __DIR__ . '/data/favorites.json';

// Ensure directories exist
if (!is_dir($baseStorageDir)) {
    @mkdir($baseStorageDir, 0755, true);
}
if (!is_dir($baseStorageDir . '/GlobalMarket')) {
    @mkdir($baseStorageDir . '/GlobalMarket', 0755, true);
}
if (!is_dir($trashStorageDir)) {
    @mkdir($trashStorageDir, 0755, true);
}

// Global Recursive Directory Deletion Helper
function delete_dir_recursive($dir) {
    if (!is_dir($dir)) return false;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $dir . '/' . $file;
        (is_dir($filePath)) ? delete_dir_recursive($filePath) : @unlink($filePath);
    }
    return @rmdir($dir);
}

// Trash Index Helpers
function get_trash_index() {
    global $trashIndexFile;
    if (!file_exists($trashIndexFile)) {
        return [];
    }
    $content = file_get_contents($trashIndexFile);
    return json_decode($content, true) ?: [];
}

function save_trash_index($items) {
    global $trashIndexFile;
    $dir = dirname($trashIndexFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($trashIndexFile, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Favorites Helpers
function get_favorites_list() {
    global $favoritesFile;
    if (!file_exists($favoritesFile)) {
        return [];
    }
    $content = file_get_contents($favoritesFile);
    return json_decode($content, true) ?: [];
}

function save_favorites_list($favs) {
    global $favoritesFile;
    $dir = dirname($favoritesFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($favoritesFile, json_encode($favs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Path Sanitizer & Security Validator
function get_safe_full_path($baseDir, $relativePath) {
    $cleanRel = trim($relativePath, "/\\ \t\n\r\0\x0B");
    $cleanRel = str_replace(['../', '..\\', '..'], '', $cleanRel);
    
    // Disallow accessing .trash directly via standard tree
    if (strpos($cleanRel, '.trash') !== false) {
        return ['valid' => false, 'path' => null, 'exists' => false];
    }
    
    $targetPath = $baseDir . ($cleanRel !== '' ? '/' . $cleanRel : '/GlobalMarket');
    
    if (!file_exists($targetPath)) {
        if ($cleanRel === 'GlobalMarket') {
            @mkdir($targetPath, 0755, true);
        } else {
            return ['valid' => false, 'path' => null, 'exists' => false];
        }
    }
    
    $realTarget = realpath($targetPath);
    $realBase = realpath($baseDir);
    
    if ($realTarget === false || $realBase === false || strpos($realTarget, $realBase) !== 0) {
        return ['valid' => false, 'path' => null, 'exists' => false];
    }
    
    return ['valid' => true, 'path' => $realTarget, 'exists' => true];
}

function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return $bytes . ' byte';
    } else {
        return '0 bytes';
    }
}

function get_file_icon_and_type($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $map = [
        'pdf' => ['icon' => 'fa-file-pdf', 'color' => '#ef4444', 'type' => 'PDF', 'preview' => true],
        'doc' => ['icon' => 'fa-file-word', 'color' => '#3b82f6', 'type' => 'Documento Word', 'preview' => false],
        'docx' => ['icon' => 'fa-file-word', 'color' => '#3b82f6', 'type' => 'Documento Word', 'preview' => false],
        'xls' => ['icon' => 'fa-file-excel', 'color' => '#10b981', 'type' => 'Hoja de Cálculo', 'preview' => false],
        'xlsx' => ['icon' => 'fa-file-excel', 'color' => '#10b981', 'type' => 'Hoja de Cálculo', 'preview' => false],
        'csv' => ['icon' => 'fa-file-csv', 'color' => '#10b981', 'type' => 'CSV', 'preview' => false],
        'ppt' => ['icon' => 'fa-file-powerpoint', 'color' => '#f97316', 'type' => 'Presentación', 'preview' => false],
        'pptx' => ['icon' => 'fa-file-powerpoint', 'color' => '#f97316', 'type' => 'Presentación', 'preview' => false],
        'jpg' => ['icon' => 'fa-file-image', 'color' => '#fbbf24', 'type' => 'Imagen JPG', 'preview' => true],
        'jpeg' => ['icon' => 'fa-file-image', 'color' => '#fbbf24', 'type' => 'Imagen JPG', 'preview' => true],
        'png' => ['icon' => 'fa-file-image', 'color' => '#fbbf24', 'type' => 'Imagen PNG', 'preview' => true],
        'webp' => ['icon' => 'fa-file-image', 'color' => '#fbbf24', 'type' => 'Imagen WebP', 'preview' => true],
        'mp4' => ['icon' => 'fa-file-video', 'color' => '#8b5cf6', 'type' => 'Video MP4', 'preview' => true],
        'mov' => ['icon' => 'fa-file-video', 'color' => '#8b5cf6', 'type' => 'Video QuickTime', 'preview' => true],
        'zip' => ['icon' => 'fa-file-zipper', 'color' => '#eab308', 'type' => 'Archivo Comprimido', 'preview' => false],
        'rar' => ['icon' => 'fa-file-zipper', 'color' => '#eab308', 'type' => 'Archivo RAR', 'preview' => false],
        'txt' => ['icon' => 'fa-file-lines', 'color' => '#9ca3af', 'type' => 'Texto Plano', 'preview' => true],
    ];

    if (isset($map[$ext])) {
        return $map[$ext];
    }

    return ['icon' => 'fa-file', 'color' => '#9ca3af', 'type' => strtoupper($ext) . ' Archivo', 'preview' => false];
}

// ROUTING
switch ($action) {

    // =========================================================================
    // 1. LIST FOLDERS & FILES IN DIRECTORY
    // =========================================================================
    case 'get_tree':
        header('Content-Type: application/json');
        $reqPath = !empty($_GET['path']) ? $_GET['path'] : 'GlobalMarket';
        
        $check = get_safe_full_path($baseStorageDir, $reqPath);
        if (!$check['valid'] || !$check['exists'] || !is_dir($check['path'])) {
            $reqPath = 'GlobalMarket';
            $check = get_safe_full_path($baseStorageDir, $reqPath);
        }

        $fullPath = $check['path'];
        $items = @scandir($fullPath) ?: [];
        $realBase = realpath($baseStorageDir);
        $favList = get_favorites_list();
        $favPaths = array_column($favList, 'path');
        
        $folders = [];
        $files = [];
        $totalSize = 0;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || $item === '.htaccess' || $item === '.trash') continue;
            
            $itemFullPath = $fullPath . '/' . $item;
            $relItemPath = trim(str_replace($realBase, '', $itemFullPath), "/\\");
            $relItemPath = str_replace('\\', '/', $relItemPath);
            
            if (is_dir($itemFullPath)) {
                $subItems = @scandir($itemFullPath) ?: [];
                $childCount = 0;
                foreach ($subItems as $si) {
                    if ($si !== '.' && $si !== '..' && $si !== '.htaccess' && $si !== '.trash') $childCount++;
                }
                $folders[] = [
                    'name' => $item,
                    'path' => $relItemPath,
                    'mtime' => date('d/m/Y H:i', filemtime($itemFullPath)),
                    'items_count' => $childCount,
                    'is_favorite' => in_array($relItemPath, $favPaths)
                ];
            } elseif (is_file($itemFullPath)) {
                $size = filesize($itemFullPath);
                $totalSize += $size;
                $meta = get_file_icon_and_type($item);
                
                $files[] = [
                    'name' => $item,
                    'path' => $relItemPath,
                    'size' => $size,
                    'size_formatted' => format_file_size($size),
                    'mtime' => date('d/m/Y H:i', filemtime($itemFullPath)),
                    'ext' => strtolower(pathinfo($item, PATHINFO_EXTENSION)),
                    'icon' => $meta['icon'],
                    'color' => $meta['color'],
                    'type_name' => $meta['type'],
                    'previewable' => $meta['preview']
                ];
            }
        }

        // Sort alphabetically
        usort($folders, function($a, $b) { return strnatcasecmp($a['name'], $b['name']); });
        usort($files, function($a, $b) { return strnatcasecmp($a['name'], $b['name']); });

        // Breadcrumbs
        $breadcrumbs = [];
        $pathParts = array_filter(explode('/', str_replace('\\', '/', $reqPath)));
        $accum = '';
        foreach ($pathParts as $p) {
            $accum = $accum === '' ? $p : $accum . '/' . $p;
            $breadcrumbs[] = [
                'name' => $p,
                'path' => $accum
            ];
        }

        echo json_encode([
            'success' => true,
            'current_path' => $reqPath,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $folders,
            'files' => $files,
            'favorites' => $favList,
            'stats' => [
                'folders_count' => count($folders),
                'files_count' => count($files),
                'total_size' => format_file_size($totalSize)
            ],
            'user' => [
                'name' => $currentUser['name'],
                'role' => $currentUser['role'],
                'is_superadmin' => $isSuperAdmin,
                'is_admin' => $isAdmin
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // 2. CREATE FOLDER (Admin or Collab)
    // =========================================================================
    case 'create_folder':
        header('Content-Type: application/json');
        if (!$isAdmin && !$isCollab) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $folderName = trim($data['name'] ?? '');
        $parentPath = trim($data['parent_path'] ?? 'GlobalMarket');

        if (empty($folderName)) {
            echo json_encode(['success' => false, 'error' => 'El nombre de la carpeta no puede estar vacío']);
            exit;
        }

        $cleanName = preg_replace('/[^a-zA-Z0-9_\-\. áéíóúÁÉÍÓÚñÑ]/u', '_', $folderName);
        $checkParent = get_safe_full_path($baseStorageDir, $parentPath);

        if (!$checkParent['valid'] || !$checkParent['exists']) {
            echo json_encode(['success' => false, 'error' => 'Carpeta destino no encontrada']);
            exit;
        }

        $newFolderPath = $checkParent['path'] . '/' . $cleanName;
        if (file_exists($newFolderPath)) {
            echo json_encode(['success' => false, 'error' => 'Ya existe una carpeta o archivo con ese nombre']);
            exit;
        }

        if (mkdir($newFolderPath, 0755, true)) {
            echo json_encode(['success' => true, 'message' => 'Carpeta creada con éxito']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al crear la carpeta en el servidor']);
        }
        break;

    // =========================================================================
    // 3. UPLOAD FILE (Supports Drag & Drop)
    // =========================================================================
    case 'upload_file':
        header('Content-Type: application/json');
        if (!$isAdmin && !$isCollab) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado para subir archivos']);
            exit;
        }

        if (empty($_FILES['file']) || !isset($_POST['target_path'])) {
            echo json_encode(['success' => false, 'error' => 'Datos de subida incompletos']);
            exit;
        }

        $targetRel = trim($_POST['target_path']);
        $check = get_safe_full_path($baseStorageDir, $targetRel);
        if (!$check['valid'] || !$check['exists'] || !is_dir($check['path'])) {
            echo json_encode(['success' => false, 'error' => 'Carpeta destino inválida']);
            exit;
        }

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'Error en la subida del archivo (código ' . $file['error'] . ')']);
            exit;
        }

        $origName = basename($file['name']);
        $cleanFileName = preg_replace('/[^a-zA-Z0-9_\-\. áéíóúÁÉÍÓÚñÑ]/u', '_', $origName);
        
        $destPath = $check['path'] . '/' . $cleanFileName;
        
        // If file exists, append timestamp
        if (file_exists($destPath)) {
            $info = pathinfo($cleanFileName);
            $cleanFileName = $info['filename'] . '_' . time() . '.' . ($info['extension'] ?? '');
            $destPath = $check['path'] . '/' . $cleanFileName;
        }

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            echo json_encode([
                'success' => true,
                'message' => 'Archivo subido con éxito',
                'filename' => $cleanFileName
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar archivo en el servidor']);
        }
        break;

    // =========================================================================
    // 4. DOWNLOAD FILE (Secure Authenticated Stream)
    // =========================================================================
    case 'download':
        $reqPath = $_GET['path'] ?? '';
        $check = get_safe_full_path($baseStorageDir, $reqPath);

        if (!$check['valid'] || !$check['exists'] || !is_file($check['path'])) {
            http_response_code(404);
            die('Archivo no encontrado o acceso no permitido');
        }

        $filePath = $check['path'];
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);
        readfile($filePath);
        exit;

    // =========================================================================
    // 5. VIEW FILE INLINE (PDF, Images, Video Preview)
    // =========================================================================
    case 'view':
        $reqPath = $_GET['path'] ?? '';
        $check = get_safe_full_path($baseStorageDir, $reqPath);

        if (!$check['valid'] || !$check['exists'] || !is_file($check['path'])) {
            http_response_code(404);
            die('Archivo no encontrado');
        }

        $filePath = $check['path'];
        $fileName = basename($filePath);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'txt' => 'text/plain; charset=utf-8'
        ];

        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . addslashes($fileName) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;

    // =========================================================================
    // 6. RENAME ITEM (Folders & Files)
    // =========================================================================
    case 'rename_item':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Solo administradores pueden renombrar']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $reqPath = trim($data['path'] ?? '');
        $newName = trim($data['new_name'] ?? '');

        if ($reqPath === 'GlobalMarket' || empty($reqPath) || empty($newName)) {
            echo json_encode(['success' => false, 'error' => 'Nombre o ruta inválida']);
            exit;
        }

        $check = get_safe_full_path($baseStorageDir, $reqPath);
        if (!$check['valid'] || !$check['exists']) {
            echo json_encode(['success' => false, 'error' => 'Elemento no encontrado']);
            exit;
        }

        $cleanNewName = preg_replace('/[^a-zA-Z0-9_\-\. áéíóúÁÉÍÓÚñÑ]/u', '_', $newName);
        $parentDir = dirname($check['path']);
        $newFullPath = $parentDir . '/' . $cleanNewName;

        if (file_exists($newFullPath)) {
            echo json_encode(['success' => false, 'error' => 'Ya existe un elemento con ese nombre']);
            exit;
        }

        if (rename($check['path'], $newFullPath)) {
            // Update favorites if folder was renamed
            $realBase = realpath($baseStorageDir);
            $newRelPath = trim(str_replace($realBase, '', $newFullPath), "/\\");
            $newRelPath = str_replace('\\', '/', $newRelPath);

            $favs = get_favorites_list();
            $favChanged = false;
            foreach ($favs as $idx => $f) {
                if ($f['path'] === $reqPath) {
                    $favs[$idx]['path'] = $newRelPath;
                    $favs[$idx]['name'] = $cleanNewName;
                    $favChanged = true;
                }
            }
            if ($favChanged) {
                save_favorites_list($favs);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Renombrado con éxito',
                'new_path' => $newRelPath,
                'new_name' => $cleanNewName
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al renombrar']);
        }
        break;

    // =========================================================================
    // 7. MOVE TO TRASH (Soft Delete)
    // =========================================================================
    case 'move_to_trash':
        header('Content-Type: application/json');
        if (!$isAdmin && !$isCollab) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado para enviar a la papelera']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $reqPath = trim($data['path'] ?? '');

        if ($reqPath === 'GlobalMarket' || empty($reqPath)) {
            echo json_encode(['success' => false, 'error' => 'No se puede eliminar la carpeta raíz']);
            exit;
        }

        $check = get_safe_full_path($baseStorageDir, $reqPath);
        if (!$check['valid'] || !$check['exists']) {
            echo json_encode(['success' => false, 'error' => 'Elemento no encontrado']);
            exit;
        }

        $target = $check['path'];
        $isFolder = is_dir($target);
        $baseName = basename($target);
        $size = $isFolder ? 0 : filesize($target);
        $trashId = 'trash_' . time() . '_' . uniqid();
        $trashFileName = $trashId . '_' . $baseName;
        $destTrashPath = $trashStorageDir . '/' . $trashFileName;

        if (rename($target, $destTrashPath)) {
            $trashIndex = get_trash_index();
            $trashIndex[] = [
                'id' => $trashId,
                'name' => $baseName,
                'original_path' => $reqPath,
                'trash_filename' => $trashFileName,
                'is_folder' => $isFolder,
                'size' => $size,
                'size_formatted' => $isFolder ? 'Carpeta' : format_file_size($size),
                'deleted_by' => $currentUser['name'],
                'deleted_at' => date('d/m/Y H:i:s')
            ];
            save_trash_index($trashIndex);

            // Remove from favorites if it was a favorite folder
            $favs = get_favorites_list();
            $newFavs = array_values(array_filter($favs, function($f) use ($reqPath) {
                return $f['path'] !== $reqPath;
            }));
            if (count($favs) !== count($newFavs)) {
                save_favorites_list($newFavs);
            }

            echo json_encode([
                'success' => true,
                'message' => ($isFolder ? 'Carpeta movida' : 'Archivo movido') . ' a la Papelera de Reciclaje'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al mover a la papelera']);
        }
        break;

    // =========================================================================
    // 8. GET TRASH LIST
    // =========================================================================
    case 'get_trash':
        header('Content-Type: application/json');
        $trashItems = get_trash_index();
        // Sort newest deleted first
        usort($trashItems, function($a, $b) {
            return strcmp($b['id'], $a['id']);
        });

        echo json_encode([
            'success' => true,
            'items' => $trashItems,
            'count' => count($trashItems),
            'user' => [
                'name' => $currentUser['name'],
                'role' => $currentUser['role'],
                'is_admin' => $isAdmin
            ]
        ], JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================================
    // 9. RESTORE FROM TRASH
    // =========================================================================
    case 'restore_item':
        header('Content-Type: application/json');
        if (!$isAdmin && !$isCollab) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $trashId = trim($data['id'] ?? '');

        $trashIndex = get_trash_index();
        $targetItem = null;
        $targetIdx = -1;

        foreach ($trashIndex as $idx => $t) {
            if ($t['id'] === $trashId) {
                $targetItem = $t;
                $targetIdx = $idx;
                break;
            }
        }

        if (!$targetItem) {
            echo json_encode(['success' => false, 'error' => 'Elemento no encontrado en la papelera']);
            exit;
        }

        $sourceTrashPath = $trashStorageDir . '/' . $targetItem['trash_filename'];
        if (!file_exists($sourceTrashPath)) {
            // Cleanup index if physical file is missing
            array_splice($trashIndex, $targetIdx, 1);
            save_trash_index($trashIndex);
            echo json_encode(['success' => false, 'error' => 'El archivo físico no existe en la papelera']);
            exit;
        }

        $origRel = $targetItem['original_path'];
        $destPath = $baseStorageDir . '/' . $origRel;
        $destParent = dirname($destPath);

        if (!is_dir($destParent)) {
            @mkdir($destParent, 0755, true);
        }

        // Avoid collision
        if (file_exists($destPath)) {
            $info = pathinfo($targetItem['name']);
            if ($targetItem['is_folder']) {
                $destPath = $destParent . '/' . $targetItem['name'] . '_restaurado_' . time();
            } else {
                $destPath = $destParent . '/' . $info['filename'] . '_restaurado_' . time() . '.' . ($info['extension'] ?? '');
            }
        }

        if (rename($sourceTrashPath, $destPath)) {
            array_splice($trashIndex, $targetIdx, 1);
            save_trash_index($trashIndex);

            echo json_encode([
                'success' => true,
                'message' => 'Elemento restaurado con éxito a su ubicación original'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al restaurar elemento']);
        }
        break;

    // =========================================================================
    // 10. PURGE ITEM (Permanent Delete from Trash)
    // =========================================================================
    case 'purge_item':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Solo administradores pueden eliminar definitivamente']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $trashId = trim($data['id'] ?? '');

        $trashIndex = get_trash_index();
        $targetItem = null;
        $targetIdx = -1;

        foreach ($trashIndex as $idx => $t) {
            if ($t['id'] === $trashId) {
                $targetItem = $t;
                $targetIdx = $idx;
                break;
            }
        }

        if (!$targetItem) {
            echo json_encode(['success' => false, 'error' => 'Elemento no encontrado en la papelera']);
            exit;
        }

        $sourceTrashPath = $trashStorageDir . '/' . $targetItem['trash_filename'];
        if (file_exists($sourceTrashPath)) {
            if ($targetItem['is_folder']) {
                delete_dir_recursive($sourceTrashPath);
            } else {
                @unlink($sourceTrashPath);
            }
        }

        array_splice($trashIndex, $targetIdx, 1);
        save_trash_index($trashIndex);

        echo json_encode(['success' => true, 'message' => 'Elemento eliminado permanentemente']);
        break;

    // =========================================================================
    // 11. EMPTY TRASH
    // =========================================================================
    case 'empty_trash':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Solo administradores pueden vaciar la papelera']);
            exit;
        }

        $trashIndex = get_trash_index();
        foreach ($trashIndex as $t) {
            $path = $trashStorageDir . '/' . $t['trash_filename'];
            if (file_exists($path)) {
                $t['is_folder'] ? delete_dir_recursive($path) : @unlink($path);
            }
        }

        save_trash_index([]);
        echo json_encode(['success' => true, 'message' => 'Papelera vaciada completamente']);
        break;

    // =========================================================================
    // 12. FAVORITES MANAGEMENT
    // =========================================================================
    case 'get_favorites':
        header('Content-Type: application/json');
        $favs = get_favorites_list();
        echo json_encode(['success' => true, 'favorites' => $favs]);
        break;

    case 'toggle_favorite':
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $folderPath = trim($data['path'] ?? '');
        $folderName = trim($data['name'] ?? '');

        if (empty($folderPath)) {
            echo json_encode(['success' => false, 'error' => 'Ruta inválida']);
            exit;
        }

        $favs = get_favorites_list();
        $found = false;
        $isFavNow = false;

        foreach ($favs as $idx => $f) {
            if ($f['path'] === $folderPath) {
                array_splice($favs, $idx, 1);
                $found = true;
                $isFavNow = false;
                break;
            }
        }

        if (!$found) {
            $favs[] = [
                'path' => $folderPath,
                'name' => !empty($folderName) ? $folderName : basename($folderPath),
                'added_at' => date('d/m/Y H:i')
            ];
            $isFavNow = true;
        }

        save_favorites_list($favs);
        echo json_encode([
            'success' => true,
            'is_favorite' => $isFavNow,
            'favorites' => $favs,
            'message' => $isFavNow ? 'Añadido a Favoritos' : 'Removido de Favoritos'
        ]);
        break;

    // =========================================================================
    // 13. USER MANAGEMENT (Admin / Superadmin only)
    // =========================================================================
    case 'get_users':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
            exit;
        }

        $users = get_drive_users();
        $safeUsers = array_map(function($u) {
            return [
                'id' => $u['id'],
                'username' => $u['username'],
                'name' => $u['name'],
                'email' => $u['email'] ?? '',
                'role' => $u['role'],
                'status' => $u['status'] ?? 'active',
                'created_at' => $u['created_at'] ?? ''
            ];
        }, $users);

        echo json_encode(['success' => true, 'users' => $safeUsers]);
        break;

    case 'create_user':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? 'client');

        if (empty($username) || empty($password) || empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Todos los campos obligatorios deben completarse']);
            exit;
        }

        // Only superadmin can create superadmin/admin roles
        if (($role === 'superadmin' || $role === 'admin') && !$isSuperAdmin) {
            echo json_encode(['success' => false, 'error' => 'Solo el Super Administrador puede crear administradores']);
            exit;
        }

        $users = get_drive_users();
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                echo json_encode(['success' => false, 'error' => 'El nombre de usuario ya existe']);
                exit;
            }
        }

        $newUser = [
            'id' => 'usr_' . time() . '_' . rand(100, 999),
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'role' => in_array($role, ['superadmin', 'admin', 'client', 'collab']) ? $role : 'client',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'allowed_folders' => ['*'],
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $users[] = $newUser;
        save_drive_users($users);

        echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito']);
        break;

    case 'delete_user':
        header('Content-Type: application/json');
        if (!$isAdmin) {
            echo json_encode(['success' => false, 'error' => 'Permiso denegado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $data['id'] ?? '';

        if ($userId === $currentUser['id']) {
            echo json_encode(['success' => false, 'error' => 'No puedes eliminar tu propia cuenta']);
            exit;
        }

        $users = get_drive_users();
        $newUsers = [];
        $found = false;

        foreach ($users as $u) {
            if ($u['id'] === $userId) {
                if ($u['role'] === 'superadmin' && !$isSuperAdmin) {
                    echo json_encode(['success' => false, 'error' => 'No puedes eliminar a un Super Administrador']);
                    exit;
                }
                $found = true;
                continue;
            }
            $newUsers[] = $u;
        }

        if ($found) {
            save_drive_users($newUsers);
            echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        break;
}
