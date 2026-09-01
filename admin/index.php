<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/builder.php';
require_auth();

$products = get_products_data();
$settings = get_site_settings();
$quotesFile = __DIR__ . '/data/quotes.json';
$quotes = file_exists($quotesFile) ? (json_decode(file_get_contents($quotesFile), true) ?: []) : [];
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

  <link rel="stylesheet" href="css/admin.css?v=2">
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
        <small class="version-text">GlobalMarket GM v2.0</small>
      </div>
    </aside>

    <!-- CONTENT AREA -->
    <main class="admin-content">

      <!-- TAB 1: PRODUCTOS & GALERÍAS -->
      <section class="tab-pane active" id="tab-products">
        <div class="pane-header">
          <div>
            <h2>Gestión de Frutas y Galerías</h2>
            <p>Selecciona una fruta para subir fotos (con recorte automático 1:1), organizar su galería o editar su ficha técnica.</p>
          </div>
        </div>

        <!-- SELECTOR DE FRUTA -->
        <div class="fruit-selector-grid">
          <?php foreach ($products as $idx => $p): 
            $photoCount = count($p['gallery'] ?? []);
          ?>
            <div class="fruit-card <?= $idx === 0 ? 'active' : '' ?>" data-fruit-id="<?= $p['id'] ?>">
              <div class="fruit-card-icon">
                <img src="../<?= strtok($p['img'], '?') ?>" alt="<?= htmlspecialchars($p['name_es']) ?>">
              </div>
              <div class="fruit-card-info">
                <h4><?= htmlspecialchars($p['name_es']) ?></h4>
                <span class="fruit-photos-count"><i class="fa-solid fa-images"></i> <?= $photoCount ?> fotos</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- DETALLE Y EDITOR DE LA FRUTA ACTIVA -->
        <div class="fruit-editor-card" id="fruitEditorCard">
          
          <div class="editor-header">
            <div class="editor-title-group">
              <h3 id="currentFruitTitle">Cargando...</h3>
              <p id="currentFruitScientific" class="scientific-sub"></p>
            </div>
            <div class="editor-actions">
              <a href="#" target="_blank" class="btn btn-outline-light btn-sm" id="btnPreviewFruit">
                <i class="fa-solid fa-eye"></i> Ver Página
              </a>
            </div>
          </div>

          <!-- SUB-TABS: GALERÍA / TEXTOS -->
          <div class="subtabs-bar">
            <button type="button" class="subtab-btn active" data-subtab="gallery">
              <i class="fa-solid fa-images"></i> Galería de Fotos (<span id="activePhotoCount">0</span>)
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
                <p>O haz clic para seleccionar fotos desde tu computadora o teléfono (JPG, PNG, WebP)</p>
                <div class="dropzone-tip">
                  <i class="fa-solid fa-wand-magic-sparkles text-gold"></i>
                  El sistema redimensiona y recorta automáticamente a <strong>formato cuadrado 1:1 (800x800 px)</strong> para mantener la concordancia visual.
                </div>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click();">
                  <i class="fa-solid fa-plus"></i> Seleccionar Foto
                </button>
              </div>
              <div class="upload-progress" id="uploadProgress" style="display: none;">
                <div class="spinner"></div>
                <span>Subiendo y recortando imagen en el servidor...</span>
              </div>
            </div>

            <!-- CUADRÍCULA DE FOTOS EXISTENTES -->
            <div class="admin-gallery-wrapper">
              <div class="gallery-toolbar">
                <h4>Fotografías en la Galería Cuadrada</h4>
                <small class="text-muted">Pasa el cursor sobre una foto para ver las opciones de eliminar o fijar como foto principal.</small>
              </div>

              <div class="admin-gallery-grid" id="adminGalleryGrid">
                <!-- Se llena dinámicamente con JS -->
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

      <!-- TAB 2: BANDEJA DE COTIZACIONES -->
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

      <!-- TAB 3: CONTACTO Y AJUSTES GLOBALES -->
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
              <label for="setAddress"><i class="fa-solid fa-location-dot"></i> Dirección / Puerto Principal</label>
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

      <!-- TAB 4: SEGURIDAD -->
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

  <script src="js/admin.js?v=2"></script>
</body>
</html>
