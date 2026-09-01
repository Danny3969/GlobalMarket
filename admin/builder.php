<?php
// GlobalMarket GM - Complete Site Compiler & HTML Rebuild Engine
if (!defined('GM_ADMIN_INIT')) {
    define('GM_ADMIN_INIT', true);
}

function get_products_data() {
    $file = __DIR__ . '/data/products.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function get_site_settings() {
    $file = __DIR__ . '/data/site_settings.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function get_home_content() {
    $file = __DIR__ . '/data/home_content.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function get_menu_items() {
    $file = __DIR__ . '/data/menu.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}

function save_products_data($data) {
    $file = __DIR__ . '/data/products.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function save_site_settings($data) {
    $file = __DIR__ . '/data/site_settings.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function save_home_content($data) {
    $file = __DIR__ . '/data/home_content.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function save_menu_items($data) {
    $file = __DIR__ . '/data/menu.json';
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function render_menu_nav_html($menuItems, $isProductPage = false) {
    $html = '';
    foreach ($menuItems as $item) {
        if (isset($item['visible']) && $item['visible'] === false) continue;
        if (!empty($item['is_cta'])) continue;

        $url = $item['url'];
        if ($isProductPage && strpos($url, '#') === 0) {
            $url = 'index.html' . $url;
        }

        if (!empty($item['has_submenu']) && !empty($item['submenu'])) {
            $html .= "        <div class=\"nav-dropdown-wrapper\">\n";
            $html .= "          <a href=\"{$url}\" class=\"nav-link\">{$item['label']} <i class=\"fa-solid fa-chevron-down\" style=\"font-size: 0.7rem; margin-left: 0.25rem;\"></i></a>\n";
            $html .= "          <div class=\"nav-dropdown-menu\">\n";
            foreach ($item['submenu'] as $sub) {
                if (isset($sub['visible']) && $sub['visible'] === false) continue;
                $html .= "            <a href=\"{$sub['url']}\" class=\"dropdown-item\">{$sub['label']}</a>\n";
            }
            $html .= "          </div>\n";
            $html .= "        </div>\n";
        } else {
            $html .= "        <a href=\"{$url}\" class=\"nav-link\">{$item['label']}</a>\n";
        }
    }
    return $html;
}

function render_gallery_html($gallery, $fruitName) {
    if (empty($gallery)) return '';
    $html = '';
    $i = 1;
    foreach ($gallery as $imgUrl) {
        $cleanUrl = strtok($imgUrl, '?');
        if (strpos($cleanUrl, '-sq.') === false) {
            $pathInfo = pathinfo($cleanUrl);
            $sqUrl = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-sq.jpg';
        } else {
            $sqUrl = $cleanUrl;
        }
        $v = time();
        $html .= "        <!-- FOTO {$i} -->\n";
        $html .= "        <div class=\"gallery-card\" data-src=\"{$cleanUrl}?v={$v}\">\n";
        $html .= "          <img src=\"{$sqUrl}?v={$v}\" alt=\"{$fruitName}\" class=\"gallery-thumb\" loading=\"lazy\">\n";
        $html .= "          <div class=\"gallery-overlay\">\n";
        $html .= "            <div class=\"gallery-overlay-icon\"><i class=\"fa-solid fa-magnifying-glass-plus\"></i></div>\n";
        $html .= "          </div>\n";
        $html .= "        </div>\n\n";
        $i++;
    }
    return $html;
}

function rebuild_product_page($p, $settings, $menuItems = null) {
    $rootDir = dirname(__DIR__);
    $filePath = $rootDir . '/' . $p['file'];
    if ($menuItems === null) $menuItems = get_menu_items();

    $galleryHtml = render_gallery_html($p['gallery'] ?? [], $p['name_es']);
    $menuHtml = render_menu_nav_html($menuItems, true);
    
    $nutriHtml = '';
    if (!empty($p['nutri'])) {
        foreach ($p['nutri'] as $n) {
            $nutriHtml .= "            <div class=\"nutrition-card\">\n";
            $nutriHtml .= "              <div class=\"nutri-icon\"><i class=\"fa-solid {$n['icon']}\"></i></div>\n";
            $nutriHtml .= "              <h4>{$n['title_es']}</h4>\n";
            $nutriHtml .= "              <p>{$n['desc_es']}</p>\n";
            $nutriHtml .= "            </div>\n";
        }
    }
    
    $cleanMainImg = strtok($p['img'], '?') . '?v=' . time();
    $cleanHeroBg = strtok(!empty($p['hero_bg']) ? $p['hero_bg'] : $p['img'], '?') . '?v=' . time();
    $companyDesc = !empty($settings['company_desc']) ? $settings['company_desc'] : 'Empresa exportadora ecuatoriana líder en frutas exóticas y tradicionales de calidad premium.';
    $fbUrl = !empty($settings['social']['facebook']) ? $settings['social']['facebook'] : '#';
    $igUrl = !empty($settings['social']['instagram']) ? $settings['social']['instagram'] : '#';
    $liUrl = !empty($settings['social']['linkedin']) ? $settings['social']['linkedin'] : '#';
    $phone = !empty($settings['phone']) ? $settings['phone'] : '';
    $email = !empty($settings['email']) ? $settings['email'] : '';
    $address = !empty($settings['address']) ? $settings['address'] : '';
    $whatsapp = !empty($settings['whatsapp']) ? $settings['whatsapp'] : '';
    $certsBadge = !empty($settings['certs_badge']) ? $settings['certs_badge'] : '';

    $pName = $p['name_es'];
    $pScientific = $p['scientific'];
    $pBadge = $p['badge_es'];
    $pTagline = $p['tagline_es'];
    $pHeroClass = $p['hero_class'];
    $pOrigin = $p['origin_es'];
    $pGrade = $p['grade_es'];
    $pCalibers = $p['calibers_es'];
    $pLength = $p['length_es'];
    $pBrix = $p['brix_es'];
    $pPack = $p['pack_es'];
    $pTemp = $p['temp_es'];
    $pVent = $p['vent_es'];
    $pShelf = $p['shelf_es'];
    $pPallet = $p['pallet_es'];
    $pCerts = $p['certs_es'];

    $content = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$pName} | Exportación Global Market GM</title>
  <meta name="description" content="{$pTagline} Ficha técnica, calibres, empaque y cotización B2B.">
  <link rel="icon" type="image/png" href="assets/images/favicon.png?v=3">

  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Stylesheet -->
  <link rel="stylesheet" href="styles.css?v=6">
</head>
<body>

  <!-- TOP BAR -->
  <div class="top-bar">
    <div class="container top-bar-inner">
      <div class="top-bar-contact">
        <a href="tel:{$phone}"><i class="fa-solid fa-phone"></i> {$phone}</a>
        <a href="mailto:{$email}"><i class="fa-solid fa-envelope"></i> {$email}</a>
        <span class="top-bar-badge"><i class="fa-solid fa-shield-halved"></i> {$certsBadge}</span>
      </div>
      <div class="top-bar-lang">
        <a href="drive/index.php" class="top-bar-portal-btn" title="Portal Corporativo / Drive Privado" style="color: #fbbf24; text-decoration: none; font-size: 0.82rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; margin-right: 0.75rem;"><i class="fa-solid fa-lock"></i> <span>Intranet</span></a>
        <span class="lang-divider">|</span>
        <button type="button" class="lang-btn active" data-lang="es">🇪🇸 ES</button>
        <span class="lang-divider">|</span>
        <button type="button" class="lang-btn" data-lang="en">🇺🇸 EN</button>
      </div>
    </div>
  </div>

  <!-- NAVBAR -->
  <header class="main-header" id="navbar">
    <div class="container nav-container">
      <a href="index.html" class="brand-logo" aria-label="Global Market Inicio">
        <img src="assets/images/logo.png?v=3" alt="Global Market Logo" class="brand-img">
      </a>

      <nav class="nav-links" id="navLinks">
{$menuHtml}      </nav>

      <button class="nav-mobile-toggle" id="mobileToggle" aria-label="Abrir Menú">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- PRODUCT HERO HEADER CON IMAGEN -->
  <section class="product-hero {$pHeroClass}" style="background-image: linear-gradient(180deg, rgba(6, 21, 15, 0.72) 0%, rgba(6, 21, 15, 0.92) 100%), url('{$cleanHeroBg}'); background-size: cover; background-position: center;">
    <div class="container">
      <div class="breadcrumbs">
        <a href="index.html">Inicio</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
        <a href="index.html#productos">Nuestros Productos</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
        <span>{$pName}</span>
      </div>

      <div class="product-hero-content" style="max-width: 800px;">
        <span class="badge badge-gold" style="display: inline-block; margin-bottom: 0.75rem; padding: 0.35rem 0.85rem; border-radius: 99px;">
          <i class="fa-solid fa-award"></i> {$pBadge}
        </span>
        <h1 class="hero-title" style="font-size: 2.75rem; margin-bottom: 0.75rem;">{$pName}</h1>
        <p style="font-size: 1.15rem; font-style: italic; color: var(--accent-gold); margin-bottom: 1rem;">{$pScientific}</p>
        <p class="hero-desc" style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">{$pTagline}</p>

        <div class="hero-cta-group">
          <a href="#cotizador-producto" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-file-invoice-dollar"></i> Cotizar {$pName}
          </a>
          <a href="https://wa.me/{$whatsapp}?text=Hola%20Global%20Market,%20deseo%20cotizar%20{$pName}%20para%20exportación." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
            <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- PRODUCT DETAIL & SPECS -->
  <section class="section-padding bg-light">
    <div class="container">
      <div class="product-detail-grid">
        
        <!-- LEFT: IMAGEN HD & NUTRICIÓN -->
        <div>
          <div class="detail-gallery-box">
            <img src="{$cleanMainImg}" alt="{$pName}" class="detail-main-img">
            <div style="padding: 1.25rem; background: #FFFFFF; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="color: var(--primary-dark); font-size: 1.05rem;"><i class="fa-solid fa-location-dot text-primary"></i> Origen:</strong>
                <span style="font-size: 0.95rem; color: var(--text-muted); margin-left: 0.35rem;">{$pOrigin}</span>
              </div>
              <span class="product-badge badge-green" style="position: static;">100% Export Quality</span>
            </div>
          </div>

          <div style="margin-top: 2.5rem;">
            <div class="section-tag">Propiedades & Beneficios</div>
            <h3 style="font-size: 1.6rem; color: var(--text-main); margin-bottom: 0.5rem;">Valor Nutricional y Cualidades</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Cosechado en el punto óptimo de madurez para preservar todas sus propiedades organolépticas.</p>
            
            <div class="nutrition-grid">
              {$nutriHtml}
            </div>
          </div>
        </div>

        <!-- RIGHT: TABLA TÉCNICA DETALLADA -->
        <div>
          <div class="specs-table-card">
            <div class="specs-table-header">
              <h3><i class="fa-solid fa-clipboard-check text-gold"></i> Ficha Técnica de Exportación</h3>
              <span style="font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 0.25rem 0.6rem; border-radius: 4px;">Specs B2B</span>
            </div>
            <table class="specs-full-table">
              <tbody>
                <tr>
                  <th>Especie / Variedad:</th>
                  <td><strong>{$pScientific}</strong></td>
                </tr>
                <tr>
                  <th>Grado Comercial:</th>
                  <td>{$pGrade}</td>
                </tr>
                <tr>
                  <th>Calibres / Tamaños:</th>
                  <td>{$pCalibers}</td>
                </tr>
                <tr>
                  <th>Longitud / Dimensiones:</th>
                  <td>{$pLength}</td>
                </tr>
                <tr>
                  <th>Dulzura / Sólidos Solubles:</th>
                  <td>{$pBrix}</td>
                </tr>
                <tr>
                  <th>Formato de Empaque:</th>
                  <td>{$pPack}</td>
                </tr>
                <tr>
                  <th>Temperatura en Reefer:</th>
                  <td>{$pTemp}</td>
                </tr>
                <tr>
                  <th>Ventilación & Humedad:</th>
                  <td>{$pVent}</td>
                </tr>
                <tr>
                  <th>Vida Útil en Tránsito:</th>
                  <td>{$pShelf}</td>
                </tr>
                <tr>
                  <th>Paletizado & Capacidad:</th>
                  <td>{$pPallet}</td>
                </tr>
                <tr>
                  <th>Certificaciones:</th>
                  <td><span style="color: var(--primary); font-weight: 600;">{$pCerts}</span></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- HIGHLIGHT BOX -->
          <div style="background: #FFFFFF; border-left: 4px solid var(--primary); padding: 1.5rem; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm);">
            <h4 style="font-size: 1.1rem; color: var(--primary-dark); margin-bottom: 0.5rem;"><i class="fa-solid fa-truck-ramp-box"></i> Trazabilidad y Cadena de Frío Garantizada</h4>
            <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
              Monitoreamos cada lote desde el corte en finca hasta la entrega en puerto con termógrafos digitales de alta precisión, garantizando que el producto llegue en perfecto estado a su destino final.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- SECCIÓN DE GALERÍA DE FOTOS -->
  <section class="gallery-section" id="galeria">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-tag">Galería de Imágenes</div>
        <h2 class="section-title">{$pName}</h2>
        <p class="section-subtitle">
          Catálogo visual de exportación. Haz clic sobre cualquier fotografía para verla en alta definición.
        </p>
      </div>

      <div class="gallery-grid" id="galleryGrid">
{$galleryHtml}      </div>
    </div>
  </section>

  <!-- LIGHTBOX MODAL -->
  <div class="lightbox-modal" id="lightboxModal">
    <button type="button" class="lightbox-close" id="lightboxClose" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
    <button type="button" class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
    <button type="button" class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Siguiente"><i class="fa-solid fa-chevron-right"></i></button>
    
    <div class="lightbox-dialog">
      <div class="lightbox-img-wrapper">
        <img src="" alt="Vista ampliada" class="lightbox-img" id="lightboxImg">
      </div>
      <div class="lightbox-footer">
        <p class="lightbox-caption" id="lightboxCaption"></p>
        <span class="lightbox-counter" id="lightboxCounter">1 / 1</span>
      </div>
    </div>
  </div>

  <!-- COTIZADOR DEDICADO -->
  <section class="section-padding" id="cotizador-producto" style="background: var(--bg-dark); color: #FFFFFF;">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-tag">Cotización Directa</div>
        <h2 class="section-title text-white">Solicitar Cotización de {$pName}</h2>
        <p class="section-subtitle text-white-muted">
          Completa el formulario y recibe una propuesta FOB / CIF formal en menos de 24 horas.
        </p>
      </div>

      <div class="quote-form-card" style="max-width: 800px; margin: 0 auto;">
        <form id="productQuoteForm" class="quote-form">
          <input type="hidden" id="selectedProduct" value="{$pName}">
          <div class="form-grid">
            <div class="form-group">
              <label for="pClientName" class="form-label"><i class="fa-solid fa-user"></i> Nombre Completo / Empresa *</label>
              <input type="text" id="pClientName" class="form-input" placeholder="Ej: John Doe / Fresh Imports LLC" required>
            </div>
            <div class="form-group">
              <label for="pClientEmail" class="form-label"><i class="fa-solid fa-envelope"></i> Correo Electrónico *</label>
              <input type="email" id="pClientEmail" class="form-input" placeholder="procurement@company.com" required>
            </div>
            <div class="form-group">
              <label for="pClientPhone" class="form-label"><i class="fa-brands fa-whatsapp"></i> Teléfono / WhatsApp *</label>
              <input type="tel" id="pClientPhone" class="form-input" placeholder="+1 (555) 000-0000" required>
            </div>
            <div class="form-group">
              <label for="pDestCountry" class="form-label"><i class="fa-solid fa-globe"></i> País y Puerto de Destino *</label>
              <input type="text" id="pDestCountry" class="form-input" placeholder="Ej: Port of Miami / Rotterdam / Hamburgo" required>
            </div>
            <div class="form-group">
              <label for="pVolume" class="form-label"><i class="fa-solid fa-box-open"></i> Volumen Estimado *</label>
              <select id="pVolume" class="form-select" required>
                <option value="1 Contenedor 40ft Reefer (Spot)">1 Contenedor 40ft Reefer (Spot)</option>
                <option value="2 a 4 Contenedores / Mes">2 a 4 Contenedores / Mes</option>
                <option value="+5 Contenedores / Mes (Programa Anual)">+5 Contenedores / Mes (Programa Anual)</option>
                <option value="Carga Aérea (Pallets)">Carga Aérea (Pallets Semanales)</option>
              </select>
            </div>
            <div class="form-group">
              <label for="pIncoterm" class="form-label"><i class="fa-solid fa-handshake"></i> Incoterm Preferido</label>
              <select id="pIncoterm" class="form-select">
                <option value="FOB Guayaquil">FOB (Guayaquil, Ecuador)</option>
                <option value="CIF Puerto de Destino" selected>CIF (Costo, Seguro y Flete)</option>
                <option value="CFR Puerto de Destino">CFR (Costo y Flete)</option>
                <option value="FCA Aeropuerto UIO/GYE">FCA (Carga Aérea)</option>
              </select>
            </div>
          </div>
          <div class="form-group" style="margin-top: 1rem;">
            <label for="pMessage" class="form-label"><i class="fa-solid fa-comment-dots"></i> Requerimientos Especiales / Calibres Específicos</label>
            <textarea id="pMessage" class="form-textarea" rows="3" placeholder="Indica calibres de preferencia, formato de empaque o semanas de despacho requeridas..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
            <i class="fa-solid fa-paper-plane"></i> Enviar Solicitud de Cotización B2B
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="main-footer">
    <div class="container footer-grid">
      <div class="footer-col">
        <img src="assets/images/logo-dark.jpg?v=3" alt="Global Market GM" class="footer-logo">
        <p class="footer-desc">
          {$companyDesc}
        </p>
        <div class="footer-social">
          <a href="{$fbUrl}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="{$igUrl}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="{$liUrl}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://wa.me/{$whatsapp}" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Nuestras Frutas</h4>
        <ul class="footer-links">
          <li><a href="banano.html">Banano Cavendish</a></li>
          <li><a href="platano.html">Plátano Verde / Barraganete</a></li>
          <li><a href="pitahaya-roja.html">Pitahaya Roja (Red Dragon)</a></li>
          <li><a href="pitahaya-amarilla.html">Pitahaya Amarilla de Palora</a></li>
          <li><a href="malanga.html">Malanga / Taro Root</a></li>
          <li><a href="maracuya.html">Maracuyá (Passion Fruit)</a></li>
          <li><a href="pina.html">Piña Golden MD2</a></li>
          <li><a href="mango.html">Mango de Exportación</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Enlaces Rápidos</h4>
        <ul class="footer-links">
          <li><a href="index.html#inicio">Inicio</a></li>
          <li><a href="index.html#nosotros">Quiénes Somos</a></li>
          <li><a href="index.html#certificaciones">Garantía de Calidad</a></li>
          <li><a href="index.html#logistica">Operaciones de Embarque</a></li>
          <li><a href="index.html#cotizador">Solicitar Cotización</a></li>
          <li><a href="drive/index.php" style="color: #fbbf24;"><i class="fa-solid fa-lock"></i> Portal Clientes & Intranet</a></li>
          <li><a href="https://globalmarket-gm.com/webmail" target="_blank" style="color: #60a5fa;"><i class="fa-solid fa-envelope"></i> Webmail Corporativo</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Contacto Directo</h4>
        <div class="footer-contact-item">
          <i class="fa-solid fa-location-dot"></i>
          <span>{$address}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-phone"></i>
          <span>{$phone}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-envelope"></i>
          <span>{$email}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-clock"></i>
          <span>Lunes a Sábado: 08:00 - 18:00 (GMT-5)</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>&copy; 2026 GlobalMarket GM Cía. Ltda. Todos los derechos reservados.</p>
        <div class="footer-legal">
          <span>Ecuador Export Quality</span>
          <span>•</span>
          <span>BASC & GlobalG.A.P.</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- FLOATING WHATSAPP BUTTON -->
  <a href="https://wa.me/{$whatsapp}?text=Hola%20Global%20Market,%20deseo%20información%20sobre%20la%20exportación%20de%20{$pName}." class="floating-whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
    <span class="whatsapp-tooltip">¿Deseas cotizar {$pName}? Chatea con nosotros</span>
  </a>

  <!-- SCRIPTS -->
  <script src="app.js?v=6"></script>
</body>
</html>
HTML;

    return file_put_contents($filePath, $content);
}

function rebuild_home_page($home, $products, $settings, $menuItems = null) {
    $rootDir = dirname(__DIR__);
    $filePath = $rootDir . '/index.html';
    if ($menuItems === null) $menuItems = get_menu_items();

    $menuHtml = render_menu_nav_html($menuItems, false);
    
    // Products Grid
    $productsGridHtml = '';
    foreach ($products as $p) {
        $cleanImg = strtok($p['img'], '?') . '?v=' . time();
        $pName = $p['name_es'];
        $pScientific = $p['scientific'];
        $pBadge = $p['badge_es'];
        $pTagline = $p['tagline_es'];
        $pCalibers = $p['calibers_es'];
        $pPack = $p['pack_es'];
        $pFile = $p['file'];

        $productsGridHtml .= <<<HTML
        <div class="product-card">
          <div class="product-img-wrapper">
            <img src="{$cleanImg}" alt="{$pName}" class="product-img" loading="lazy">
            <span class="product-badge badge-green">{$pBadge}</span>
          </div>
          <div class="product-content">
            <span class="product-tag">{$pScientific}</span>
            <h3 class="product-title">{$pName}</h3>
            <p class="product-desc">{$pTagline}</p>
            <div class="product-meta">
              <span><i class="fa-solid fa-ruler-combined"></i> {$pCalibers}</span>
              <span><i class="fa-solid fa-box"></i> {$pPack}</span>
            </div>
            <div class="product-card-actions">
              <a href="{$pFile}" class="btn btn-outline-primary btn-sm btn-block">
                <i class="fa-solid fa-circle-info"></i> Ver Ficha y Galería
              </a>
              <a href="{$pFile}#cotizador-producto" class="btn btn-primary btn-sm btn-block">
                <i class="fa-solid fa-file-invoice-dollar"></i> Cotizar
              </a>
            </div>
          </div>
        </div>
HTML;
    }

    // Certifications Items
    $certsHtml = '';
    if (!empty($home['certs']['items'])) {
        foreach ($home['certs']['items'] as $c) {
            $cIcon = $c['icon'];
            $cTitle = $c['title'];
            $cDesc = $c['desc'];
            $certsHtml .= <<<HTML
          <div class="cert-card">
            <div class="cert-icon"><i class="fa-solid {$cIcon}"></i></div>
            <h3 class="cert-title">{$cTitle}</h3>
            <p class="cert-desc">{$cDesc}</p>
          </div>
HTML;
        }
    }

    // Logistics Steps
    $logisticsHtml = '';
    if (!empty($home['logistics']['steps'])) {
        foreach ($home['logistics']['steps'] as $s) {
            $sNum = $s['num'];
            $sTitle = $s['title'];
            $sDesc = $s['desc'];
            $logisticsHtml .= <<<HTML
            <div class="logistics-step">
              <span class="step-num">{$sNum}</span>
              <div>
                <h4 class="step-title">{$sTitle}</h4>
                <p class="step-desc">{$sDesc}</p>
              </div>
            </div>
HTML;
        }
    }

    // Hero Stats
    $heroStatsHtml = '';
    if (!empty($home['hero']['stats'])) {
        foreach ($home['hero']['stats'] as $st) {
            $stNum = $st['num'];
            $stLabel = $st['label'];
            $heroStatsHtml .= <<<HTML
            <div class="stat-card">
              <span class="stat-num">{$stNum}</span>
              <span class="stat-lbl">{$stLabel}</span>
            </div>
HTML;
        }
    }

    $cleanHeroBg = strtok(!empty($home['hero']['bg_image']) ? $home['hero']['bg_image'] : 'assets/images/hero-banner.jpg', '?') . '?v=' . time();
    $cleanAboutImg = strtok(!empty($home['about']['image']) ? $home['about']['image'] : 'assets/images/hero-banner.jpg', '?') . '?v=' . time();
    $cleanLogisticsImg = strtok(!empty($home['logistics']['image']) ? $home['logistics']['image'] : 'assets/images/logistica.jpg', '?') . '?v=' . time();

    $heroBadge = !empty($home['hero']['badge']) ? $home['hero']['badge'] : '';
    $heroTitle = !empty($home['hero']['title']) ? $home['hero']['title'] : '';
    $heroDesc = !empty($home['hero']['desc']) ? $home['hero']['desc'] : '';
    $heroBtnExplore = !empty($home['hero']['btn_explore']) ? $home['hero']['btn_explore'] : 'Explorar Catálogo';
    $heroBtnQuote = !empty($home['hero']['btn_quote']) ? $home['hero']['btn_quote'] : 'Cotizador B2B';
    $heroBtnWa = !empty($home['hero']['btn_whatsapp']) ? $home['hero']['btn_whatsapp'] : 'WhatsApp Directo';

    $aboutTag = !empty($home['about']['tag']) ? $home['about']['tag'] : 'Quiénes Somos';
    $aboutTitle = !empty($home['about']['title']) ? $home['about']['title'] : '';
    $aboutP1 = !empty($home['about']['p1']) ? $home['about']['p1'] : '';
    $aboutP2 = !empty($home['about']['p2']) ? $home['about']['p2'] : '';
    $aboutBadgeTitle = !empty($home['about']['badge_title']) ? $home['about']['badge_title'] : '';
    $aboutBadgeSub = !empty($home['about']['badge_sub']) ? $home['about']['badge_sub'] : '';

    $certsTag = !empty($home['certs']['tag']) ? $home['certs']['tag'] : 'Garantía de Calidad';
    $certsTitle = !empty($home['certs']['title']) ? $home['certs']['title'] : '';
    $certsDesc = !empty($home['certs']['desc']) ? $home['certs']['desc'] : '';

    $logisticsTag = !empty($home['logistics']['tag']) ? $home['logistics']['tag'] : 'Operaciones';
    $logisticsTitle = !empty($home['logistics']['title']) ? $home['logistics']['title'] : '';
    $logisticsDesc = !empty($home['logistics']['desc']) ? $home['logistics']['desc'] : '';

    $footerAbout = !empty($home['footer']['about_text']) ? $home['footer']['about_text'] : '';
    $footerCopyright = !empty($home['footer']['copyright']) ? $home['footer']['copyright'] : '© 2026 GlobalMarket GM Cía. Ltda.';

    $fbUrl = !empty($settings['social']['facebook']) ? $settings['social']['facebook'] : '#';
    $igUrl = !empty($settings['social']['instagram']) ? $settings['social']['instagram'] : '#';
    $liUrl = !empty($settings['social']['linkedin']) ? $settings['social']['linkedin'] : '#';
    $phone = !empty($settings['phone']) ? $settings['phone'] : '';
    $email = !empty($settings['email']) ? $settings['email'] : '';
    $address = !empty($settings['address']) ? $settings['address'] : '';
    $whatsapp = !empty($settings['whatsapp']) ? $settings['whatsapp'] : '';
    $certsBadge = !empty($settings['certs_badge']) ? $settings['certs_badge'] : '';

    $content = <<<HTML
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GLOBAL MARKET GM | Exportador Global de Frutas Tropicales Frescas</title>
  <meta name="description" content="Global Market GM es exportador líder de frutas tropicales de alta calidad: Banano Cavendish, Plátano, Pitahaya Roja, Pitahaya Amarilla, Malanga, Maracuyá, Piña y Mango de Ecuador al mundo.">
  <meta name="keywords" content="Global Market GM, exportacion de frutas, banano ecuador, pitahaya amarilla palora, red dragon fruit, mango exportacion, fresh fruit exporter ecuador">
  <meta name="author" content="Global Market GM">
  <meta property="og:title" content="GLOBAL MARKET GM | Global Fresh Fruit Exporter">
  <meta property="og:description" content="Frutas tropicales premium de exportación: Banano, Mango, Pitahayas, Piña, Plátano, Malanga y Maracuyá con cadena de frío y certificaciones internacionales.">
  <meta property="og:image" content="{$cleanHeroBg}">
  <meta property="og:type" content="website">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/images/favicon.png?v=3">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- CSS Stylesheet -->
  <link rel="stylesheet" href="styles.css?v=6">
</head>
<body>

  <!-- Top Announcement Bar -->
  <aside class="top-bar" aria-label="Información de contacto rápido">
    <div class="container top-bar-inner">
      <div class="top-bar-left">
        <span><i class="fa-solid fa-location-dot"></i> <span>{$address}</span></span>
        <span><i class="fa-solid fa-shield-halved"></i> <span>{$certsBadge}</span></span>
      </div>
      <div class="top-bar-right">
        <a href="drive/index.php" class="top-link" title="Portal Corporativo / Drive Privado" style="display: inline-flex; align-items: center; gap: 0.35rem; color: #fbbf24; font-weight: 600;"><i class="fa-solid fa-lock"></i> <span>Intranet</span></a>
        <a href="mailto:{$email}" class="top-link"><i class="fa-regular fa-envelope"></i> {$email}</a>
        <div class="lang-switch-btn" id="langSwitch" title="Cambiar idioma">
          <span id="currentLangLabel">🇪🇸 ES</span> <i class="fa-solid fa-chevron-down"></i>
          <div class="lang-dropdown" id="langDropdown">
            <button type="button" class="lang-option active" data-lang="es">🇪🇸 Español</button>
            <button type="button" class="lang-option" data-lang="en">🇺🇸 English</button>
          </div>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Navigation Header -->
  <header class="main-header" id="mainHeader">
    <div class="container nav-container">
      <a href="#inicio" class="brand-logo" aria-label="Global Market Inicio">
        <img src="assets/images/logo.png?v=3" alt="Global Market - Premium Fruits Experts" class="brand-img">
      </a>

      <!-- Desktop Navigation -->
      <nav class="nav-menu" id="navMenu">
{$menuHtml}      </nav>

      <!-- Header CTAs -->
      <div class="header-actions">
        <a href="#cotizador" class="btn btn-primary btn-sm btn-quote">
          <i class="fa-solid fa-file-invoice-dollar"></i>
          <span>Cotizar Frutas</span>
        </a>
        <a href="https://wa.me/{$whatsapp}?text=Hola%20Global%20Market,%20deseo%20solicitar%20información%20y%20cotización%20de%20frutas%20de%20exportación." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-header" aria-label="WhatsApp">
          <i class="fa-brands fa-whatsapp"></i>
        </a>
        <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="Abrir menú">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer Menu -->
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-head">
      <img src="assets/images/logo.png?v=3" alt="Global Market Logo" style="height: 44px; width: auto; object-fit: contain;">
      <button type="button" class="mobile-close" id="mobileClose" aria-label="Cerrar menú">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="mobile-drawer-links">
      <a href="#inicio" class="mobile-link">Inicio</a>
      <a href="#nosotros" class="mobile-link">Nosotros</a>
      <a href="#productos" class="mobile-link">Productos</a>
      <a href="#certificaciones" class="mobile-link">Certificaciones</a>
      <a href="#logistica" class="mobile-link">Logística</a>
      <a href="#contacto" class="mobile-link">Contacto</a>
      <a href="#cotizador" class="btn btn-primary" style="margin-top: 1.5rem; justify-content: center;">Cotizar Frutas</a>
    </div>
  </div>

  <main>
    <!-- HERO SECTION -->
    <section class="hero-section" id="inicio" style="background-image: linear-gradient(180deg, rgba(6, 21, 15, 0.72) 0%, rgba(6, 21, 15, 0.92) 100%), url('{$cleanHeroBg}');">
      <div class="hero-bg-overlay"></div>
      <div class="container hero-container">
        <div class="hero-content">
          <div class="hero-badge animate-fade-in">
            <i class="fa-solid fa-seedling"></i>
            <span>{$heroBadge}</span>
          </div>
          <h1 class="hero-title animate-fade-in">
            {$heroTitle}
          </h1>
          <p class="hero-subtitle animate-fade-in">
            {$heroDesc}
          </p>
          <div class="hero-buttons animate-fade-in">
            <a href="#productos" class="btn btn-primary btn-lg">
              <span>{$heroBtnExplore}</span>
              <i class="fa-solid fa-arrow-right"></i>
            </a>
            <a href="#cotizador" class="btn btn-glass btn-lg">
              <i class="fa-solid fa-calculator"></i>
              <span>{$heroBtnQuote}</span>
            </a>
            <a href="https://wa.me/{$whatsapp}?text=Hola%20Global%20Market%20GM,%20deseo%20cotizar%20un%20contenedor%20de%20frutas." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
              <i class="fa-brands fa-whatsapp"></i>
              <span>{$heroBtnWa}</span>
            </a>
          </div>

          <!-- Hero Metrics / Trust Counters -->
          <div class="hero-stats-grid animate-fade-in">
{$heroStatsHtml}          </div>
        </div>
      </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="section-padding about-section" id="nosotros">
      <div class="container">
        <div class="grid-2-cols align-center">
          <div class="about-visuals">
            <div class="image-wrapper-card">
              <img src="{$cleanAboutImg}" alt="Plantación y Cosecha Global Market GM" class="about-img-main">
              <div class="about-floating-badge">
                <div class="badge-icon"><i class="fa-solid fa-leaf"></i></div>
                <div>
                  <h4>{$aboutBadgeTitle}</h4>
                  <p>{$aboutBadgeSub}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="about-text-content">
            <div class="section-tag">{$aboutTag}</div>
            <h2 class="section-title">{$aboutTitle}</h2>
            <p class="section-desc">
              {$aboutP1}
            </p>
            <p class="section-desc">
              {$aboutP2}
            </p>
            <div class="about-pillars">
              <div class="pillar-item">
                <div class="pillar-icon"><i class="fa-solid fa-temperature-arrow-down"></i></div>
                <div>
                  <h4 class="pillar-title">Cadena de Frío Ininterrumpida</h4>
                  <p class="pillar-desc">Monitoreo térmico digital con termógrafos en tiempo real desde la finca hasta puerto.</p>
                </div>
              </div>
              <div class="pillar-item">
                <div class="pillar-icon"><i class="fa-solid fa-certificate"></i></div>
                <div>
                  <h4 class="pillar-title">Certificaciones Globales</h4>
                  <p class="pillar-desc">Cultivos respaldados por GlobalG.A.P., BASC, Rainforest Alliance y AGROCALIDAD.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- PRODUCTS CATALOG SECTION -->
    <section class="section-padding bg-light" id="productos">
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag">Portafolio de Exportación</div>
          <h2 class="section-title">Frutas Premium Seleccionadas</h2>
          <p class="section-subtitle">
            Calibres uniformes, dulzura garantizada y empaques acondicionados para tránsitos marítimos y aéreos de larga distancia.
          </p>
        </div>

        <!-- Products Grid (8 Frutas) -->
        <div class="products-grid">
{$productsGridHtml}        </div>
      </div>
    </section>

    <!-- CERTIFICATIONS SECTION -->
    <section class="section-padding cert-section" id="certificaciones">
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag">{$certsTag}</div>
          <h2 class="section-title text-white">{$certsTitle}</h2>
          <p class="section-subtitle text-white-muted">
            {$certsDesc}
          </p>
        </div>

        <div class="cert-grid">
{$certsHtml}        </div>
      </div>
    </section>

    <!-- LOGISTICS SECTION -->
    <section class="section-padding logistics-section" id="logistica">
      <div class="container">
        <div class="grid-2-cols align-center">
          <div class="logistics-content">
            <div class="section-tag">{$logisticsTag}</div>
            <h2 class="section-title">{$logisticsTitle}</h2>
            <p class="section-desc">
              {$logisticsDesc}
            </p>
            <div class="logistics-steps">
{$logisticsHtml}            </div>
          </div>
          <div class="logistics-visual">
            <div class="logistics-card-img">
              <img src="{$cleanLogisticsImg}" alt="Logística y Embarque Internacional Global Market GM" class="logistics-img">
              <div class="logistics-floating-card">
                <i class="fa-solid fa-anchor"></i>
                <div>
                  <strong>Puerto de Guayaquil (GYE)</strong>
                  <span>Salidas semanales hacia Norteamérica, Europa y Asia</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- QUOTATION & CONTACT SECTION -->
    <section class="section-padding quote-section" id="cotizador">
      <div class="container">
        <div class="grid-2-cols align-center">
          <div class="quote-intro text-white">
            <div class="section-tag text-gold">Cotizaciones B2B</div>
            <h2 class="section-title text-white">Solicite su Cotización de Frutas Frescas de Exportación</h2>
            <p class="section-desc text-white-muted">
              Atendemos requerimientos mayoristas, programas anuales y compras spot con cotizaciones personalizadas en términos FOB, CIF, CFR y FCA.
            </p>
            <div class="contact-quick-list">
              <div class="contact-quick-item">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Respuesta en menos de 24 horas hábiles</span>
              </div>
              <div class="contact-quick-item">
                <i class="fa-solid fa-file-shield"></i>
                <span>Documentación de exportación completa y fitosanitarios</span>
              </div>
              <div class="contact-quick-item">
                <i class="fa-solid fa-truck-ramp-box"></i>
                <span>Inspección de calidad previa al cierre de contenedor</span>
              </div>
            </div>
          </div>

          <div class="quote-form-card">
            <div class="form-header">
              <h3>Formulario de Cotización Internacional</h3>
              <p>Complete los datos para generar una propuesta adaptada a su destino.</p>
            </div>
            <form class="quote-form" id="quoteForm">
              <div class="form-grid">
                <div class="form-group">
                  <label for="clientName" class="form-label"><i class="fa-solid fa-user"></i> Nombre / Empresa *</label>
                  <input type="text" id="clientName" name="clientName" class="form-input" placeholder="Ej: John Doe / Fresh Imports Inc." required>
                </div>
                <div class="form-group">
                  <label for="clientEmail" class="form-label"><i class="fa-solid fa-envelope"></i> Correo Electrónico *</label>
                  <input type="email" id="clientEmail" name="clientEmail" class="form-input" placeholder="procurement@empresa.com" required>
                </div>
                <div class="form-group">
                  <label for="clientPhone" class="form-label"><i class="fa-brands fa-whatsapp"></i> Teléfono / WhatsApp *</label>
                  <input type="tel" id="clientPhone" name="clientPhone" class="form-input" placeholder="+1 (555) 000-0000" required>
                </div>
                <div class="form-group">
                  <label for="destinationCountry" class="form-label"><i class="fa-solid fa-globe"></i> País y Puerto Destino *</label>
                  <input type="text" id="destinationCountry" name="destinationCountry" class="form-input" placeholder="Ej: Puerto de Miami, EE.UU." required>
                </div>
                <div class="form-group">
                  <label for="quoteProduct" class="form-label"><i class="fa-solid fa-apple-whole"></i> Fruta de Interés *</label>
                  <select id="quoteProduct" name="quoteProduct" class="form-select" required>
                    <option value="" disabled selected>Seleccione una fruta</option>
                    <option value="banano">Banano Cavendish Premium</option>
                    <option value="platano">Plátano Verde / Barraganete</option>
                    <option value="pitahaya_roja">Pitahaya Roja (Red Dragon)</option>
                    <option value="pitahaya_amarilla">Pitahaya Amarilla de Palora</option>
                    <option value="malanga">Malanga / Taro Root</option>
                    <option value="maracuya">Maracuyá (Passion Fruit)</option>
                    <option value="pina">Piña Golden MD2</option>
                    <option value="mango">Mango de Exportación</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="shippingVolume" class="form-label"><i class="fa-solid fa-box-open"></i> Volumen Estimado *</label>
                  <select id="shippingVolume" name="shippingVolume" class="form-select" required>
                    <option value="1 Contenedor 40ft (Spot)">1 Contenedor 40ft Reefer (Spot)</option>
                    <option value="2 a 4 Contenedores / Mes">2 a 4 Contenedores / Mes</option>
                    <option value="+5 Contenedores / Mes">+5 Contenedores / Mes (Programa)</option>
                    <option value="Carga Aérea (Pallets)">Carga Aérea (Pallets)</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="incoterm" class="form-label"><i class="fa-solid fa-handshake"></i> Incoterm Preferido</label>
                  <select id="incoterm" name="incoterm" class="form-select">
                    <option value="FOB">FOB (Guayaquil, Ecuador)</option>
                    <option value="CIF" selected>CIF (Costo, Seguro y Flete)</option>
                    <option value="CFR">CFR (Costo y Flete)</option>
                    <option value="FCA">FCA (Carga Aérea)</option>
                  </select>
                </div>
              </div>
              <div class="form-group" style="margin-top: 1rem;">
                <label for="clientMessage" class="form-label"><i class="fa-solid fa-comment-dots"></i> Especificaciones o Requerimientos</label>
                <textarea id="clientMessage" name="clientMessage" rows="3" class="form-textarea" placeholder="Indique calibres, fechas estimadas de arribo o empaque especial..."></textarea>
              </div>
              <div class="form-actions-group" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnSubmitQuote">
                  <i class="fa-solid fa-paper-plane"></i>
                  <span>Enviar Cotización por Correo & WhatsApp</span>
                </button>
              </div>
              <div class="quote-alert" id="quoteAlert" style="display: none;">
                <i class="fa-solid fa-circle-check"></i>
                <span>¡Gracias! Su cotización ha sido recibida y redirigida a nuestro equipo comercial.</span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- CONTACT SECTION -->
    <section class="section-padding bg-light" id="contacto">
      <div class="container">
        <div class="section-header text-center">
          <div class="section-tag">Canales Oficiales</div>
          <h2 class="section-title">Comuníquese con Nuestro Equipo</h2>
          <p class="section-subtitle">
            Estamos listos para atender consultas comerciales, alianzas de distribución y despachos inmediatos.
          </p>
        </div>

        <div class="contact-cards-grid">
          <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
            <h3 class="contact-card-title">Llamadas Directas</h3>
            <p class="contact-card-desc">Atención comercial B2B</p>
            <a href="tel:{$phone}" class="contact-link">{$phone}</a>
          </div>
          <div class="contact-card highlight-card">
            <div class="contact-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <h3 class="contact-card-title">WhatsApp Exportaciones</h3>
            <p class="contact-card-desc">Respuesta inmediata 24/7</p>
            <a href="https://wa.me/{$whatsapp}" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-sm">
              <i class="fa-brands fa-whatsapp"></i> Chatear con Asesor
            </a>
          </div>
          <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
            <h3 class="contact-card-title">Correo Electrónico</h3>
            <p class="contact-card-desc">Envío de RFQ y propuestas</p>
            <a href="mailto:{$email}" class="contact-link">{$email}</a>
          </div>
          <div class="contact-card">
            <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
            <h3 class="contact-card-title">Sede de Operaciones</h3>
            <p class="contact-card-desc">{$address}</p>
            <span class="contact-link" style="color: var(--primary); font-weight: 600;">Ecuador</span>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="main-footer">
    <div class="container footer-grid">
      <div class="footer-col">
        <img src="assets/images/logo-dark.jpg?v=3" alt="Global Market GM" class="footer-logo">
        <p class="footer-desc">
          {$footerAbout}
        </p>
        <div class="footer-social">
          <a href="{$fbUrl}" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="{$igUrl}" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="{$liUrl}" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://wa.me/{$whatsapp}" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Nuestras Frutas</h4>
        <ul class="footer-links">
          <li><a href="banano.html">Banano Cavendish</a></li>
          <li><a href="platano.html">Plátano Verde / Barraganete</a></li>
          <li><a href="pitahaya-roja.html">Pitahaya Roja (Red Dragon)</a></li>
          <li><a href="pitahaya-amarilla.html">Pitahaya Amarilla de Palora</a></li>
          <li><a href="malanga.html">Malanga / Taro Root</a></li>
          <li><a href="maracuya.html">Maracuyá (Passion Fruit)</a></li>
          <li><a href="pina.html">Piña Golden MD2</a></li>
          <li><a href="mango.html">Mango de Exportación</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Enlaces Rápidos</h4>
        <ul class="footer-links">
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#nosotros">Quiénes Somos</a></li>
          <li><a href="#certificaciones">Garantía de Calidad</a></li>
          <li><a href="#logistica">Operaciones de Embarque</a></li>
          <li><a href="#cotizador">Solicitar Cotización</a></li>
          <li><a href="drive/index.php" style="color: #fbbf24;"><i class="fa-solid fa-lock"></i> Portal Clientes & Intranet</a></li>
          <li><a href="https://globalmarket-gm.com/webmail" target="_blank" style="color: #60a5fa;"><i class="fa-solid fa-envelope"></i> Webmail Corporativo</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-title">Contacto Directo</h4>
        <div class="footer-contact-item">
          <i class="fa-solid fa-location-dot"></i>
          <span>{$address}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-phone"></i>
          <span>{$phone}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-envelope"></i>
          <span>{$email}</span>
        </div>
        <div class="footer-contact-item">
          <i class="fa-solid fa-clock"></i>
          <span>Lunes a Sábado: 08:00 - 18:00 (GMT-5)</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>{$footerCopyright}</p>
        <div class="footer-legal">
          <span>Ecuador Export Quality</span>
          <span>•</span>
          <span>BASC & GlobalG.A.P.</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- FLOATING WHATSAPP BUTTON -->
  <a href="https://wa.me/{$whatsapp}?text=Hola%20Global%20Market%20GM,%20deseo%20información%20sobre%20frutas%20de%20exportación." class="floating-whatsapp" target="_blank" rel="noopener noreferrer" aria-label="Contactar por WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
    <span class="whatsapp-tooltip">¿Deseas cotizar frutas de exportación? Chatea con nosotros</span>
  </a>

  <!-- SCRIPTS -->
  <script src="app.js?v=6"></script>
</body>
</html>
HTML;

    return file_put_contents($filePath, $content);
}

function rebuild_all_pages() {
    $products = get_products_data();
    $settings = get_site_settings();
    $home = get_home_content();
    $menu = get_menu_items();

    $count = 0;
    if (rebuild_home_page($home, $products, $settings, $menu)) {
        $count++;
    }
    foreach ($products as $p) {
        if (rebuild_product_page($p, $settings, $menu)) {
            $count++;
        }
    }
    return $count;
}
