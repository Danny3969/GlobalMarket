<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/builder.php';
require_auth();

$products = get_products_data();
$settings = get_site_settings();
$homeContent = get_home_content();
$menuItems = get_menu_items();

$quotesFile = __DIR__ . '/data/quotes.json';
$quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];

$initialPayload = [
    'products' => $products,
    'settings' => $settings,
    'home' => $homeContent,
    'menu' => $menuItems,
    'quotes' => $quotes
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración | GlobalMarket GM</title>
  <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=3">

  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="css/admin.css?v=4">
</head>
<body class="admin-body">

  <!-- TOP HEADER -->
  <header class="admin-topbar">
    <div class="topbar-left">
      <a href="index.php" class="admin-brand">
        <img src="../assets/images/logo.png?v=3" alt="GlobalMarket GM" class="admin-logo">
        <span class="admin-title">Panel de Control <span class="badge-cms">CMS Pro</span></span>
      </a>
    </div>

    <div class="topbar-right">
      <a href="../index.html" target="_blank" class="btn btn-outline-light btn-sm" title="Abrir sitio web en nueva pestaña">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Sitio Web
      </a>
      <button type="button" class="btn btn-gold btn-sm" id="btnRebuildAll" title="Recompilar todo el sitio">
        <i class="fa-solid fa-rotate"></i> Publicar Cambios
      </button>
      <div class="admin-user-pill">
        <i class="fa-solid fa-circle-user"></i>
        <span><?= htmlspecialchars($_SESSION['gm_admin_user'] ?? 'admin') ?></span>
      </div>
      <a href="logout.php" class="btn btn-danger btn-sm" title="Cerrar sesión">
        <i class="fa-solid fa-power-off"></i>
      </a>
    </div>
  </header>

  <!-- MAIN WRAPPER -->
  <div class="admin-wrapper">
    
    <!-- SIDEBAR NAVIGATION -->
    <aside class="admin-sidebar">
      <nav class="sidebar-nav">
        <button type="button" class="nav-item active" data-tab="products">
          <i class="fa-solid fa-apple-whole"></i>
          <span>Frutas & Galerías</span>
        </button>
        <button type="button" class="nav-item" data-tab="home">
          <i class="fa-solid fa-house"></i>
          <span>Página de Inicio</span>
        </button>
        <button type="button" class="nav-item" data-tab="menu">
          <i class="fa-solid fa-bars-staggered"></i>
          <span>Menú de Navegación</span>
        </button>
        <button type="button" class="nav-item" data-tab="quotes">
          <i class="fa-solid fa-inbox"></i>
          <span>Cotizaciones</span>
          <?php if (count($quotes) > 0): ?>
            <span class="badge-count" id="quotesCountBadge"><?= count($quotes) ?></span>
          <?php endif; ?>
        </button>
        <button type="button" class="nav-item" data-tab="settings">
          <i class="fa-solid fa-sliders"></i>
          <span>Contacto & Ajustes</span>
        </button>
        <button type="button" class="nav-item" data-tab="security">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Seguridad</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <div class="system-status">
          <span class="status-indicator online"></span>
          <span>Servidor Activo (cPanel)</span>
        </div>
        <small class="version-text">GlobalMarket GM v2.6</small>
      </div>
    </aside>

    <!-- CONTENT AREA -->
    <main class="admin-content">

      <!-- TAB 1: PRODUCTOS & GALERÍAS -->
      <section class="tab-pane active" id="tab-products">
        <div class="pane-header">
          <div>
            <h2>Gestión de Frutas, Cabeceras y Galerías Arrastrables</h2>
            <p>Selecciona una fruta para cambiar su foto de cabecera, su foto de referencia, o reordenar y agregar fotos a su galería arrastrándolas.</p>
          </div>
        </div>

        <!-- SELECTOR DE FRUTA -->
        <div class="fruit-selector-grid">
          <?php foreach ($products as $idx => $p): 
            $photoCount = count($p['gallery'] ?? []);
            $cleanImg = strtok($p['img'], '?');
          ?>
            <div class="fruit-card <?= $idx === 0 ? 'active' : '' ?>" data-fruit-id="<?= $p['id'] ?>" id="fruitCard_<?= $p['id'] ?>">
              <div class="fruit-card-icon">
                <img src="../<?= $cleanImg ?>" alt="<?= htmlspecialchars($p['name_es']) ?>">
              </div>
              <div class="fruit-card-info">
                <h4><?= htmlspecialchars($p['name_es']) ?></h4>
                <span class="fruit-photos-count" id="countBadge_<?= $p['id'] ?>"><i class="fa-solid fa-images"></i> <?= $photoCount ?> fotos</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- DETALLE Y EDITOR DE LA FRUTA ACTIVA -->
        <div class="fruit-editor-card" id="fruitEditorCard">
          
          <div class="editor-header">
            <div class="editor-title-group">
              <h3 id="currentFruitTitle">Cargando fruta...</h3>
              <p id="currentFruitScientific" class="scientific-sub"></p>
            </div>
            <div class="editor-actions">
              <a href="#" target="_blank" class="btn btn-outline-light btn-sm" id="btnPreviewFruit">
                <i class="fa-solid fa-eye"></i> Ver Página Pública
              </a>
            </div>
          </div>

          <!-- SECCIÓN DE CABECERA Y FOTO DE REFERENCIA -->
          <div class="fruit-special-images-grid">
            
            <!-- TARJETA: IMAGEN DE CABECERA (HERO BANNER) -->
            <div class="special-image-card">
              <div class="special-image-header">
                <h4><i class="fa-solid fa-panorama text-gold"></i> Imagen de Cabecera (Hero Banner)</h4>
                <span class="badge-cms" style="background: rgba(255,255,255,0.1);">Fondo Superior</span>
              </div>
              <div class="special-image-preview">
                <img id="previewHeroBg" src="" alt="Fondo de Cabecera">
              </div>
              <div class="special-image-actions">
                <input type="file" id="heroBgFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
                <button type="button" class="btn btn-primary btn-sm btn-block" id="btnUploadHeroBg">
                  <i class="fa-solid fa-cloud-arrow-up"></i> Cambiar Imagen de Cabecera
                </button>
              </div>
            </div>

            <!-- TARJETA: IMAGEN PRINCIPAL DE REFERENCIA (FICHA TÉCNICA) -->
            <div class="special-image-card">
              <div class="special-image-header">
                <h4><i class="fa-solid fa-star text-gold"></i> Imagen Principal de Referencia</h4>
                <span class="badge-cms" style="background: rgba(255,255,255,0.1);">Ficha Técnica</span>
              </div>
              <div class="special-image-preview">
                <img id="previewMainImg" src="" alt="Foto Principal">
              </div>
              <div class="special-image-actions">
                <input type="file" id="mainImgFileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
                <button type="button" class="btn btn-gold btn-sm btn-block" id="btnUploadMainImg">
                  <i class="fa-solid fa-cloud-arrow-up"></i> Cambiar Foto de Referencia
                </button>
              </div>
            </div>

          </div>

          <!-- SUB-TABS: GALERÍA / TEXTOS -->
          <div class="subtabs-bar">
            <button type="button" class="subtab-btn active" data-subtab="gallery">
              <i class="fa-solid fa-images"></i> Galería de Fotos (<span id="activePhotoCount">0</span>) - <em>Arrastra para Reordenar</em>
            </button>
            <button type="button" class="subtab-btn" data-subtab="specs">
              <i class="fa-solid fa-file-lines"></i> Ficha Técnica & Textos
            </button>
          </div>

          <!-- SUBTAB CONTENT: GALERÍA -->
          <div class="subtab-content active" id="subtab-gallery">
            
            <!-- UPLOADER DRAG & DROP -->
            <div class="upload-dropzone" id="uploadDropzone">
              <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
              <div class="dropzone-inner">
                <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                <h4>Arrastra y suelta aquí tus fotografías</h4>
                <p>O haz clic en el botón para seleccionar fotos desde tu PC o teléfono (JPG, PNG, WebP)</p>
                <div class="dropzone-tip">
                  <i class="fa-solid fa-wand-magic-sparkles text-gold"></i>
                  El sistema redimensiona y recorta automáticamente a <strong>formato cuadrado 1:1 (800x800 px)</strong> para mantener la armonía visual.
                </div>
                <div>
                  <button type="button" class="btn btn-primary btn-lg" id="btnSelectFiles">
                    <i class="fa-solid fa-folder-open"></i> Subir Nueva Fotografía
                  </button>
                </div>
              </div>
              <div class="upload-progress" id="uploadProgress" style="display: none;">
                <div class="spinner"></div>
                <span id="uploadProgressText">Subiendo y recortando imagen en el servidor...</span>
              </div>
            </div>

            <!-- CUADRÍCULA DE FOTOS ARRASTRABLES Y REORDENABLES -->
            <div class="admin-gallery-wrapper">
              <div class="gallery-toolbar">
                <div>
                  <h4>Galería de Imágenes (Arrastra para reordenar la ubicación)</h4>
                  <small class="text-muted">
                    <i class="fa-solid fa-arrows-up-down-left-right text-gold"></i> 
                    <strong>Arrastra y suelta</strong> cualquier tarjeta para cambiarla de orden, o usa las flechas <i class="fa-solid fa-chevron-left"></i> / <i class="fa-solid fa-chevron-right"></i>.
                  </small>
                </div>
              </div>

              <div class="admin-gallery-grid" id="adminGalleryGrid">
                <!-- Se llena dinámicamente con JS con soporte Drag & Drop -->
              </div>
            </div>

          </div>

          <!-- SUBTAB CONTENT: FICHA TÉCNICA Y TEXTOS -->
          <div class="subtab-content" id="subtab-specs">
            <form id="productSpecsForm" class="specs-form">
              <input type="hidden" id="specFruitId" name="product_id" value="">

              <div class="form-row">
                <div class="form-group col-6">
                  <label for="specNameEs">Nombre Comercial *</label>
                  <input type="text" id="specNameEs" name="name_es" class="form-control" required>
                </div>
                <div class="form-group col-6">
                  <label for="specScientific">Nombre Científico / Variedad *</label>
                  <input type="text" id="specScientific" name="scientific" class="form-control" required>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-6">
                  <label for="specBadge">Insignia / Badge Superior</label>
                  <input type="text" id="specBadge" name="badge_es" class="form-control" placeholder="Ej: Disponibilidad: Todo el Año">
                </div>
                <div class="form-group col-6">
                  <label for="specOrigin">Origen / Zonas de Cultivo</label>
                  <input type="text" id="specOrigin" name="origin_es" class="form-control" placeholder="Ej: Provincias de El Oro, Guayas y Los Ríos">
                </div>
              </div>

              <div class="form-group">
                <label for="specTagline">Descripción / Resumen de Exportación</label>
                <textarea id="specTagline" name="tagline_es" class="form-control" rows="2"></textarea>
              </div>

              <h4 class="form-section-title"><i class="fa-solid fa-clipboard-check text-gold"></i> Parámetros de la Ficha Técnica</h4>

              <div class="form-row">
                <div class="form-group col-4">
                  <label for="specGrade">Grado Comercial</label>
                  <input type="text" id="specGrade" name="grade_es" class="form-control">
                </div>
                <div class="form-group col-4">
                  <label for="specCalibers">Calibres / Tamaños</label>
                  <input type="text" id="specCalibers" name="calibers_es" class="form-control">
                </div>
                <div class="form-group col-4">
                  <label for="specBrix">Dulzura / Grados Brix</label>
                  <input type="text" id="specBrix" name="brix_es" class="form-control">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-6">
                  <label for="specPack">Formato de Empaque</label>
                  <input type="text" id="specPack" name="pack_es" class="form-control">
                </div>
                <div class="form-group col-6">
                  <label for="specTemp">Temperatura de Tránsito (Reefer)</label>
                  <input type="text" id="specTemp" name="temp_es" class="form-control">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-4">
                  <label for="specShelf">Vida Útil en Tránsito</label>
                  <input type="text" id="specShelf" name="shelf_es" class="form-control">
                </div>
                <div class="form-group col-4">
                  <label for="specPallet">Paletizado & Capacidad</label>
                  <input type="text" id="specPallet" name="pallet_es" class="form-control">
                </div>
                <div class="form-group col-4">
                  <label for="specCerts">Certificaciones</label>
                  <input type="text" id="specCerts" name="certs_es" class="form-control">
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fa-solid fa-floppy-disk"></i> Guardar y Publicar en la Web
                </button>
              </div>
            </form>
          </div>

        </div>
      </section>

      <!-- TAB 2: PÁGINA DE INICIO (HOME) -->
      <section class="tab-pane" id="tab-home">
        <div class="pane-header">
          <div>
            <h2>Editor de la Página Principal (Inicio)</h2>
            <p>Personaliza los títulos, descripciones, estadísticas y textos de cada sección de la portada.</p>
          </div>
        </div>

        <div class="settings-card">
          <form id="homeEditorForm">
            
            <!-- SECCIÓN HERO BANNER -->
            <h3 class="form-section-title"><i class="fa-solid fa-flag text-gold"></i> 1. Sección Hero (Banner Principal)</h3>
            
            <div class="form-group">
              <label for="heroBadge">Insignia Superior (Badge)</label>
              <input type="text" id="heroBadge" class="form-control" value="<?= htmlspecialchars($homeContent['hero']['badge'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label for="heroTitle">Título Principal (H1)</label>
              <textarea id="heroTitle" class="form-control" rows="2"><?= htmlspecialchars($homeContent['hero']['title'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label for="heroDesc">Descripción / Subtítulo</label>
              <textarea id="heroDesc" class="form-control" rows="3"><?= htmlspecialchars($homeContent['hero']['desc'] ?? '') ?></textarea>
            </div>

            <h4 style="color: var(--gold-light); font-size: 0.95rem; margin: 1rem 0 0.5rem 0;">Métricas / Contadores de Confianza</h4>
            <div class="form-row">
              <?php foreach ($homeContent['hero']['stats'] ?? [] as $stIdx => $st): ?>
                <div class="form-group col-4">
                  <label>Métrica <?= $stIdx + 1 ?> (Número & Etiqueta)</label>
                  <input type="text" id="statNum_<?= $stIdx ?>" class="form-control" value="<?= htmlspecialchars($st['num']) ?>" style="margin-bottom: 0.35rem;">
                  <input type="text" id="statLbl_<?= $stIdx ?>" class="form-control" value="<?= htmlspecialchars($st['label']) ?>">
                </div>
              <?php endforeach; ?>
            </div>

            <!-- SECCIÓN QUIÉNES SOMOS -->
            <h3 class="form-section-title"><i class="fa-solid fa-users text-gold"></i> 2. Sección Quiénes Somos (Nosotros)</h3>
            
            <div class="form-row">
              <div class="form-group col-4">
                <label for="aboutTag">Etiqueta de Sección</label>
                <input type="text" id="aboutTag" class="form-control" value="<?= htmlspecialchars($homeContent['about']['tag'] ?? '') ?>">
              </div>
              <div class="form-group col-8" style="flex: 2;">
                <label for="aboutTitle">Título de Sección</label>
                <input type="text" id="aboutTitle" class="form-control" value="<?= htmlspecialchars($homeContent['about']['title'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="aboutP1">Párrafo 1</label>
              <textarea id="aboutP1" class="form-control" rows="2"><?= htmlspecialchars($homeContent['about']['p1'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label for="aboutP2">Párrafo 2</label>
              <textarea id="aboutP2" class="form-control" rows="2"><?= htmlspecialchars($homeContent['about']['p2'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
              <div class="form-group col-6">
                <label for="aboutBadgeTitle">Título Tarjeta Flotante</label>
                <input type="text" id="aboutBadgeTitle" class="form-control" value="<?= htmlspecialchars($homeContent['about']['badge_title'] ?? '') ?>">
              </div>
              <div class="form-group col-6">
                <label for="aboutBadgeSub">Subtítulo Tarjeta Flotante</label>
                <input type="text" id="aboutBadgeSub" class="form-control" value="<?= htmlspecialchars($homeContent['about']['badge_sub'] ?? '') ?>">
              </div>
            </div>

            <!-- SECCIÓN CERTIFICACIONES -->
            <h3 class="form-section-title"><i class="fa-solid fa-shield-halved text-gold"></i> 3. Sección Certificaciones de Calidad</h3>

            <div class="form-row">
              <div class="form-group col-4">
                <label for="certsTag">Etiqueta</label>
                <input type="text" id="certsTag" class="form-control" value="<?= htmlspecialchars($homeContent['certs']['tag'] ?? '') ?>">
              </div>
              <div class="form-group col-8" style="flex: 2;">
                <label for="certsTitle">Título</label>
                <input type="text" id="certsTitle" class="form-control" value="<?= htmlspecialchars($homeContent['certs']['title'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="certsDesc">Descripción</label>
              <textarea id="certsDesc" class="form-control" rows="2"><?= htmlspecialchars($homeContent['certs']['desc'] ?? '') ?></textarea>
            </div>

            <!-- SECCIÓN LOGÍSTICA -->
            <h3 class="form-section-title"><i class="fa-solid fa-truck-ramp-box text-gold"></i> 4. Sección Logística & Cadena de Frío</h3>

            <div class="form-row">
              <div class="form-group col-4">
                <label for="logisticsTag">Etiqueta</label>
                <input type="text" id="logisticsTag" class="form-control" value="<?= htmlspecialchars($homeContent['logistics']['tag'] ?? '') ?>">
              </div>
              <div class="form-group col-8" style="flex: 2;">
                <label for="logisticsTitle">Título</label>
                <input type="text" id="logisticsTitle" class="form-control" value="<?= htmlspecialchars($homeContent['logistics']['title'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="logisticsDesc">Descripción</label>
              <textarea id="logisticsDesc" class="form-control" rows="2"><?= htmlspecialchars($homeContent['logistics']['desc'] ?? '') ?></textarea>
            </div>

            <!-- PIE DE PÁGINA (FOOTER) -->
            <h3 class="form-section-title"><i class="fa-solid fa-shoe-prints text-gold"></i> 5. Pie de Página (Footer)</h3>

            <div class="form-group">
              <label for="footerAbout">Texto Resumen de Empresa</label>
              <textarea id="footerAbout" class="form-control" rows="2"><?= htmlspecialchars($homeContent['footer']['about_text'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
              <label for="footerCopyright">Texto de Derechos Reservados (Copyright)</label>
              <input type="text" id="footerCopyright" class="form-control" value="<?= htmlspecialchars($homeContent['footer']['copyright'] ?? '') ?>">
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Guardar y Publicar Portada
              </button>
            </div>

          </form>
        </div>
      </section>

      <!-- TAB 3: MENÚ DE NAVEGACIÓN -->
      <section class="tab-pane" id="tab-menu">
        <div class="pane-header">
          <div>
            <h2>Gestor del Menú de Navegación</h2>
            <p>Oculta, muestra o renombra los enlaces de la barra superior y los submenús de las frutas.</p>
          </div>
        </div>

        <div class="settings-card">
          <form id="menuEditorForm">
            <div class="menu-items-list" id="menuItemsList">
              <!-- Se renderiza con JS dinámicamente -->
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Guardar y Actualizar Menú
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- TAB 4: BANDEJA DE COTIZACIONES -->
      <section class="tab-pane" id="tab-quotes">
        <div class="pane-header">
          <div>
            <h2>Bandeja de Cotizaciones de Clientes</h2>
            <p>Registro de solicitudes enviadas desde los formularios de la web.</p>
          </div>
          <button type="button" class="btn btn-outline-light btn-sm" id="btnRefreshQuotes">
            <i class="fa-solid fa-arrows-rotate"></i> Actualizar
          </button>
        </div>

        <div class="quotes-table-card">
          <div class="table-responsive">
            <table class="admin-table" id="quotesTable">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Cliente / Empresa</th>
                  <th>Producto</th>
                  <th>Destino</th>
                  <th>Volumen</th>
                  <th>Contacto</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="quotesTableBody">
                <!-- Se llena dinámicamente con JS -->
              </tbody>
            </table>
          </div>
          <div class="empty-state" id="quotesEmptyState" style="display: none;">
            <i class="fa-solid fa-inbox"></i>
            <h4>No hay cotizaciones pendientes</h4>
            <p>Las solicitudes que envíen los clientes aparecerán automáticamente aquí.</p>
          </div>
        </div>
      </section>

      <!-- TAB 5: CONTACTO Y AJUSTES GLOBALES -->
      <section class="tab-pane" id="tab-settings">
        <div class="pane-header">
          <div>
            <h2>Ajustes Generales del Sitio</h2>
            <p>Configura los números de WhatsApp, correos de exportación, dirección y redes sociales que se muestran en toda la página.</p>
          </div>
        </div>

        <div class="settings-card">
          <form id="siteSettingsForm">
            <div class="form-row">
              <div class="form-group col-6">
                <label for="setPhone"><i class="fa-solid fa-phone"></i> Teléfono Principal</label>
                <input type="text" id="setPhone" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>">
              </div>
              <div class="form-group col-6">
                <label for="setWhatsapp"><i class="fa-brands fa-whatsapp"></i> Número de WhatsApp (con código de país sin +)</label>
                <input type="text" id="setWhatsapp" name="whatsapp" class="form-control" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>" placeholder="593999999999">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-6">
                <label for="setEmail"><i class="fa-solid fa-envelope"></i> Correo Electrónico de Exportación</label>
                <input type="email" id="setEmail" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? '') ?>">
              </div>
              <div class="form-group col-6">
                <label for="setCertsBadge"><i class="fa-solid fa-shield-halved"></i> Insignia de Certificaciones (Barra Superior)</label>
                <input type="text" id="setCertsBadge" name="certs_badge" class="form-control" value="<?= htmlspecialchars($settings['certs_badge'] ?? '') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="setAddress"><i class="fa-solid fa-location-dot"></i> Dirección / Sede Principal</label>
              <input type="text" id="setAddress" name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? '') ?>">
            </div>

            <h4 class="form-section-title"><i class="fa-solid fa-share-nodes text-gold"></i> Redes Sociales</h4>

            <div class="form-row">
              <div class="form-group col-4">
                <label for="setFb"><i class="fa-brands fa-facebook-f"></i> Facebook URL</label>
                <input type="text" id="setFb" name="social_fb" class="form-control" value="<?= htmlspecialchars($settings['social']['facebook'] ?? '#') ?>">
              </div>
              <div class="form-group col-4">
                <label for="setIg"><i class="fa-brands fa-instagram"></i> Instagram URL</label>
                <input type="text" id="setIg" name="social_ig" class="form-control" value="<?= htmlspecialchars($settings['social']['instagram'] ?? '#') ?>">
              </div>
              <div class="form-group col-4">
                <label for="setLi"><i class="fa-brands fa-linkedin-in"></i> LinkedIn URL</label>
                <input type="text" id="setLi" name="social_li" class="form-control" value="<?= htmlspecialchars($settings['social']['linkedin'] ?? '#') ?>">
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Ajustes Generales
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- TAB 6: SEGURIDAD -->
      <section class="tab-pane" id="tab-security">
        <div class="pane-header">
          <div>
            <h2>Seguridad & Acceso</h2>
            <p>Actualiza la contraseña de acceso a esta consola de administración.</p>
          </div>
        </div>

        <div class="settings-card" style="max-width: 600px;">
          <form id="changePasswordForm">
            <div class="form-group">
              <label for="currentPassword"><i class="fa-solid fa-lock"></i> Contraseña Actual</label>
              <input type="password" id="currentPassword" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
              <label for="newPassword"><i class="fa-solid fa-key"></i> Nueva Contraseña (mínimo 6 caracteres)</label>
              <input type="password" id="newPassword" name="new_password" class="form-control" minlength="6" required>
            </div>

            <div class="form-group">
              <label for="confirmPassword"><i class="fa-solid fa-check-double"></i> Confirmar Nueva Contraseña</label>
              <input type="password" id="confirmPassword" name="confirm_password" class="form-control" minlength="6" required>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-shield"></i> Actualizar Contraseña
              </button>
            </div>
          </form>
        </div>
      </section>

    </main>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div class="admin-toast" id="adminToast">
    <div class="toast-icon" id="toastIcon"><i class="fa-solid fa-circle-check"></i></div>
    <div class="toast-message" id="toastMessage">Operación realizada con éxito</div>
  </div>

  <!-- EMBEDDED INITIAL DATA (INSTANT LOAD) -->
  <script>
    window.INITIAL_DATA = <?= json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>
  <script src="js/admin.js?v=4"></script>
</body>
</html>
