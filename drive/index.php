<?php
require_once __DIR__ . '/auth.php';
require_drive_auth();

$currentUser = get_logged_drive_user();
$isAdmin = is_drive_admin();

// Pre-load initial tree data for instant rendering
$baseStorageDir = __DIR__ . '/data/storage';
if (!is_dir($baseStorageDir . '/GlobalMarket')) {
    @mkdir($baseStorageDir . '/GlobalMarket', 0755, true);
}

$initialReqPath = 'GlobalMarket';
$targetDir = $baseStorageDir . '/' . $initialReqPath;
$items = @scandir($targetDir) ?: [];
$folders = [];
$files = [];
$totalSize = 0;

function initial_file_icon($filename) {
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
        'zip' => ['icon' => 'fa-file-zipper', 'color' => '#eab308', 'type' => 'Archivo ZIP', 'preview' => false],
    ];
    return $map[$ext] ?? ['icon' => 'fa-file', 'color' => '#9ca3af', 'type' => strtoupper($ext) . ' Archivo', 'preview' => false];
}

function initial_format_size($bytes) {
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
    return $bytes . ' bytes';
}

foreach ($items as $item) {
    if ($item === '.' || $item === '..' || $item === '.htaccess') continue;
    $itemFull = $targetDir . '/' . $item;
    if (is_dir($itemFull)) {
        $sub = @scandir($itemFull) ?: [];
        $cnt = 0;
        foreach ($sub as $s) { if ($s !== '.' && $s !== '..' && $s !== '.htaccess') $cnt++; }
        $folders[] = [
            'name' => $item,
            'path' => 'GlobalMarket/' . $item,
            'mtime' => date('d/m/Y H:i', filemtime($itemFull)),
            'items_count' => $cnt
        ];
    } elseif (is_file($itemFull)) {
        $sz = filesize($itemFull);
        $totalSize += $sz;
        $m = initial_file_icon($item);
        $files[] = [
            'name' => $item,
            'path' => 'GlobalMarket/' . $item,
            'size' => $sz,
            'size_formatted' => initial_format_size($sz),
            'mtime' => date('d/m/Y H:i', filemtime($itemFull)),
            'ext' => strtolower(pathinfo($item, PATHINFO_EXTENSION)),
            'icon' => $m['icon'],
            'color' => $m['color'],
            'type_name' => $m['type'],
            'previewable' => $m['preview']
        ];
    }
}

$initialPayload = [
    'current_path' => 'GlobalMarket',
    'breadcrumbs' => [['name' => 'GlobalMarket', 'path' => 'GlobalMarket']],
    'folders' => $folders,
    'files' => $files,
    'stats' => [
        'folders_count' => count($folders),
        'files_count' => count($files),
        'total_size' => initial_format_size($totalSize)
    ],
    'user' => [
        'name' => $currentUser['name'],
        'role' => $currentUser['role'],
        'is_admin' => $isAdmin
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drive GlobalMarket | Portal de Archivos & Intranet</title>
  <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=3">

  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="css/drive.css?v=3">
</head>
<body>

  <!-- TOPBAR -->
  <header class="drive-topbar">
    <div class="topbar-left">
      <a href="index.php" class="drive-brand">
        <img src="../assets/images/logo.png?v=3" alt="GlobalMarket GM" class="drive-logo">
        <span class="drive-title">Drive GlobalMarket <span class="badge-vault">Pro</span></span>
      </a>
    </div>

    <div class="topbar-center">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="driveSearchInput" class="search-input" placeholder="Buscar documentos, carpetas o PDFs...">
      </div>
    </div>

    <div class="topbar-right">
      <a href="https://globalmarket-gm.com/webmail" target="_blank" class="btn btn-outline-light btn-sm" title="Abrir Webmail de Correos Corporativos">
        <i class="fa-solid fa-envelope"></i> Webmail
      </a>

      <?php if ($isAdmin): ?>
        <button type="button" class="btn btn-outline-light btn-sm" id="btnManageUsers" title="Gestor de Usuarios y Roles">
          <i class="fa-solid fa-users-gear"></i> Usuarios
        </button>
      <?php endif; ?>

      <div class="user-pill">
        <i class="fa-solid fa-circle-user"></i>
        <span><?= htmlspecialchars($currentUser['name'] ?? 'Usuario') ?></span>
        <span class="user-role-badge"><?= $isAdmin ? 'Admin' : 'Cliente' ?></span>
      </div>

      <a href="logout.php" class="btn btn-danger btn-sm" title="Cerrar sesión">
        <i class="fa-solid fa-power-off"></i>
      </a>
    </div>
  </header>

  <!-- MAIN WRAPPER -->
  <div class="drive-wrapper">
    
    <!-- SIDEBAR -->
    <aside class="drive-sidebar">
      <nav class="sidebar-menu">
        <span class="sidebar-category">Navegación</span>
        <a href="#" class="sidebar-link sidebar-shortcut active" data-path="GlobalMarket">
          <i class="fa-solid fa-hard-drive"></i>
          <span>Mi Unidad (GlobalMarket)</span>
        </a>

        <span class="sidebar-category">Aplicativos Corporativos</span>
        <a href="https://globalmarket-gm.com/webmail" target="_blank" class="sidebar-link" style="color: #60a5fa;">
          <i class="fa-solid fa-envelope-open-text"></i>
          <span>Correos Webmail</span>
        </a>
        <a href="../admin/index.php" target="_blank" class="sidebar-link">
          <i class="fa-solid fa-sliders"></i>
          <span>Admin de la Web</span>
        </a>
        <a href="../index.html" class="sidebar-link">
          <i class="fa-solid fa-globe"></i>
          <span>Ver Sitio Web</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div class="storage-meter">
          <div class="meter-header">
            <span><i class="fa-solid fa-server"></i> Espacio Servidor</span>
            <span style="color: var(--success);">~4 TB Libres</span>
          </div>
          <div class="meter-bar">
            <div class="meter-fill"></div>
          </div>
        </div>
        <small style="color: rgba(255,255,255,0.3); font-size: 0.72rem; text-align: center;">Drive GlobalMarket v2.0</small>
      </div>
    </aside>

    <!-- CONTENT EXPLORER -->
    <main class="drive-content">
      
      <!-- TOOLBAR -->
      <div class="drive-toolbar">
        <div class="toolbar-actions">
          <?php if ($isAdmin): ?>
            <input type="file" id="driveFileInput" style="display: none;">
            <button type="button" class="btn btn-primary" id="btnUploadFile">
              <i class="fa-solid fa-cloud-arrow-up"></i> Subir Archivo
            </button>
            <button type="button" class="btn btn-outline-light" id="btnNewFolder">
              <i class="fa-solid fa-folder-plus"></i> Nueva Carpeta
            </button>
          <?php endif; ?>
        </div>

        <div class="view-toggle-group">
          <button type="button" class="btn-view-toggle active" data-view="grid" title="Vista en Cuadrícula">
            <i class="fa-solid fa-grip"></i>
          </button>
          <button type="button" class="btn-view-toggle" data-view="list" title="Vista en Lista">
            <i class="fa-solid fa-list"></i>
          </button>
        </div>
      </div>

      <!-- BREADCRUMBS -->
      <div class="drive-breadcrumbs" id="driveBreadcrumbs">
        <!-- Generado dinámicamente -->
      </div>

      <!-- MAIN EXPLORER AREA -->
      <div id="driveExplorerContent">
        <!-- Renderizado dinámico de carpetas y archivos -->
      </div>

    </main>
  </div>

  <!-- MODAL: PREVIEW DOCUMENT (PDF, IMAGES, VIDEOS) -->
  <div class="drive-modal" id="previewModal">
    <div class="modal-content preview-modal-content">
      <div class="modal-header">
        <h3 id="previewModalTitle">Visualizador de Documento</h3>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <a href="#" class="btn btn-gold btn-sm" id="btnPreviewDownload" title="Descargar este archivo">
            <i class="fa-solid fa-download"></i> Descargar
          </a>
          <button type="button" class="btn-modal-close" id="btnClosePreview">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>
      <div class="preview-body" id="previewModalBody">
        <!-- Iframe, imagen o video -->
      </div>
    </div>
  </div>

  <!-- MODAL: NUEVA CARPETA (Admin) -->
  <div class="drive-modal" id="folderModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fa-solid fa-folder-plus text-gold"></i> Crear Nueva Carpeta</h3>
        <button type="button" class="btn-modal-close" id="btnCloseFolderModal">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <form id="formNewFolder">
        <div class="form-group">
          <label for="newFolderName">Nombre de la Carpeta</label>
          <input type="text" id="newFolderName" class="form-control" placeholder="Ej: Certificados o Fichas Tecnicas" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fa-solid fa-check"></i> Crear Carpeta
        </button>
      </form>
    </div>
  </div>

  <?php if ($isAdmin): ?>
    <!-- MODAL: GESTOR DE USUARIOS Y ROLES (Admin) -->
    <div class="drive-modal" id="usersModal">
      <div class="modal-content" style="max-width: 750px;">
        <div class="modal-header">
          <h3><i class="fa-solid fa-users-gear text-gold"></i> Gestión de Usuarios & Roles del Drive</h3>
          <button type="button" class="btn-modal-close" id="btnCloseUsersModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- FORMULARIO CREAR USUARIO -->
        <form id="formCreateUser" style="background: rgba(0,0,0,0.3); padding: 1.25rem; border-radius: var(--radius-sm); border: 1px solid var(--drive-card-border); margin-bottom: 1.5rem;">
          <h4 style="font-size: 0.95rem; color: #ffffff; margin-bottom: 0.85rem;"><i class="fa-solid fa-user-plus text-gold"></i> Agregar Nuevo Usuario</h4>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
            <input type="text" id="newUserName" class="form-control" placeholder="Nombre Completo / Empresa *" required>
            <input type="text" id="newUserUsername" class="form-control" placeholder="Nombre de Usuario *" required>
            <input type="email" id="newUserEmail" class="form-control" placeholder="Correo Electrónico">
            <input type="password" id="newUserPassword" class="form-control" placeholder="Contraseña *" required minlength="6">
          </div>
          <div style="display: flex; gap: 0.75rem; align-items: center;">
            <select id="newUserRole" class="form-select" style="max-width: 220px;">
              <option value="client">Rol: Cliente (Solo ver y descargar)</option>
              <option value="collab">Rol: Colaborador (Ver y subir)</option>
              <option value="admin">Rol: Administrador (Control total)</option>
            </select>
            <button type="submit" class="btn btn-primary" style="flex: 1;">
              <i class="fa-solid fa-plus"></i> Registrar Usuario
            </button>
          </div>
        </form>

        <!-- TABLA DE USUARIOS -->
        <div style="max-height: 260px; overflow-y: auto;">
          <table class="drive-list-table">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Email</th>
                <th>Creado</th>
                <th style="text-align: right;">Acciones</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <!-- Se llena dinámicamente con JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- TOAST -->
  <div class="drive-toast" id="driveToast">
    <div class="toast-icon" id="toastIcon"><i class="fa-solid fa-circle-check"></i></div>
    <div class="toast-message" id="toastMessage">Operación exitosa</div>
  </div>

  <script>
    window.INITIAL_DRIVE_DATA = <?= json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="js/drive.js?v=3"></script>
</body>
</html>
