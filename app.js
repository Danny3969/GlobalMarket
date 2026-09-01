/**
 * GLOBAL MARKET GM - JAVASCRIPT LOGIC & BILINGUAL ENGINE
 * Multi-Language Engine, Technical Sheets Modal, RFQ Cotizador & WhatsApp Handler
 */

document.addEventListener('DOMContentLoaded', () => {

  // Current year in footer
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // ==========================================
  // BILINGUAL TRANSLATIONS DICTIONARY (ES / EN)
  // ==========================================
  const translations = {
    es: {
      topbar_location: "Ecuador / Latinoamérica • Exportación Global",
      topbar_quality: "100% Calidad de Exportación Certificada",
      brand_sub: "GLOBAL FRESH FRUIT EXPORTER",
      nav_home: "Inicio",
      nav_about: "Nosotros",
      nav_products: "Productos",
      nav_cert: "Certificaciones",
      nav_logistics: "Logística",
      nav_contact: "Contacto",
      btn_request_quote: "Cotizar Frutas",

      hero_badge: "Frutas y Raíces Tropicales Premium de Exportación",
      hero_title: "Llevamos la Frescura y Sabor del Trópico a los Mercados del Mundo",
      hero_desc: "Productores y exportadores líderes de <strong>Banano Cavendish</strong>, <strong>Plátano Barraganete</strong>, <strong>Pitahaya Roja y Amarilla</strong>, <strong>Malanga</strong>, <strong>Maracuyá</strong>, <strong>Piña MD2</strong> y <strong>Mango</strong>. Estrictos estándares fitosanitarios, trazabilidad completa y cadena de frío ininterrumpida.",
      hero_btn_explore: "Explorar Catálogo",
      hero_btn_quote: "Cotizador B2B",
      hero_btn_whatsapp: "WhatsApp Directo",

      stat_countries: "Países Destino",
      stat_coldchain: "Cadena de Frío Controlada",
      stat_hectares: "Hectáreas de Cultivo",
      stat_cert: "Estándar Internacional",

      about_tag: "Quiénes Somos",
      about_title: "Compromiso con la Excelencia Agrícola y la Frescura Internacional",
      about_p1: "En <strong>Global Market GM</strong> somos apasionados por cultivar, seleccionar y exportar lo mejor del agro tropical hacia compradores mayoristas, supermercados e importadores en Estados Unidos, Europa, Asia y Medio Oriente.",
      about_p2: "Nuestras fincas aliadas y propias en los valles más fértiles de Ecuador garantizan frutos con nutrientes excepcionales, dulzura natural (grados Brix óptimos) y una vida útil prolongada en anaquel gracias a rigurosos procesos de postcosecha y frío.",
      about_badge_title: "Agricultura Sostenible",
      about_badge_sub: "Prácticas orgánicas y eco-amigables",
      about_feat1_title: "Trazabilidad Total",
      about_feat1_desc: "Desde el árbol y lote de cosecha hasta el puerto de desembarque en destino.",
      about_feat2_title: "Monitoreo de Temperatura 24/7",
      about_feat2_desc: "Contenedores Reefer con atmósfera controlada para preservar firmeza y frescura.",
      about_feat3_title: "Trato Justo y Alianzas a Largo Plazo",
      about_feat3_desc: "Relaciones transparentes, contratos de volumen estable y entregas puntuales.",

      prod_tag: "Catálogo de Exportación",
      prod_title: "Nuestras Frutas y Raíces Tropicales de Clase Mundial",
      prod_subtitle: "Seleccionadas a mano bajo los más rigurosos estándares de peso, calibre, color, dulzura y maduración.",
      
      // Badges
      badge_all_year: "Todo el Año",
      badge_season_mango: "Octubre - Febrero",
      badge_high_demand: "Superfruta Exótica",
      badge_sweetest: "La Más Dulce (18° Brix)",
      badge_energy: "Alta Energía & Calibre",
      badge_gourmet: "Aroma & Acidez Única",
      badge_premium_sweet: "Golden Extra Dulce",
      badge_tubers: "Calidad Primera / Lavada",

      // Origins
      origin_ecuador: "Origen: Ecuador",
      origin_ecuador_costa: "Origen: Costa Ecuatoriana",
      origin_palora: "Origen: Palora / Amazonía",
      origin_manabi: "Origen: Manabí / Santo Domingo",
      origin_losrios: "Origen: Los Ríos / Guayas",
      origin_santodomingo: "Origen: Santo Domingo / Costa",

      // Names & Descriptions
      banano_name: "Banano Cavendish Premium",
      banano_desc: "Reconocido mundialmente por su textura suave, sabor equilibrado y alta durabilidad en tránsito. Cultivado en suelos volcánicos de alta fertilidad.",
      
      platano_name: "Plátano Verde / Barraganete",
      platano_desc: "Plátano macho de contextura firme, calibre grueso y excelente consistencia para cocción y snacks. Alta vida útil en contenedor refrigerado.",

      pitared_name: "Pitahaya Roja (Red Dragon Fruit)",
      pitared_desc: "Cáscara fucsia brillante con pulpa púrpura/roja intensa repleta de antioxidantes. Gran impacto visual y demandada en canales gourmet y retail.",

      pitayellow_name: "Pitahaya Amarilla (Yellow Dragon)",
      pitayellow_desc: "La famosa fruta dorada de Palora. Pulpa translúcida crujiente con dulzura insuperable (16°-19° Brix) y reconocidas propiedades digestivas.",

      malanga_name: "Malanga / Taro (Blanca & Lila)",
      malanga_desc: "Tubérculo tropical selecto, libre de impurezas, con carne densa y alto contenido nutricional. Seleccionado y empacado bajo estándares estrictos.",

      maracuya_name: "Maracuyá (Passion Fruit)",
      maracuya_desc: "Fruta de la pasión de piel amarilla dorada y aroma penetrante. Pulpa jugosa con perfecto equilibrio entre acidez y dulzura, ideal para jugos y repostería.",

      pina_name: "Piña Golden MD2",
      pina_desc: "Piña extra dulce de color dorado intenso, baja acidez y aroma exquisito. Pulpa jugosa y consistente, cosechada en el punto óptimo de grados Brix.",

      mango_name: "Mango de Exportación (Tommy & Kent)",
      mango_desc: "Mangos jugosos con pulpa firme sin fibra, coloración viva y aroma tropical embriagador. Tratamiento hidrotérmico certificado USDA-APHIS.",

      // Specs labels
      spec_grade: "Grado:",
      spec_caliber: "Calibre:",
      spec_calibers: "Calibres:",
      spec_packaging: "Empaque:",
      spec_varieties: "Variedades:",
      spec_variety: "Especie:",
      spec_brix: "Dulzura / Brix:",
      spec_transport: "Transporte:",
      spec_finger_length: "Largo de dedo:",
      btn_specs: "Ficha Técnica",
      btn_quote_item: "Cotizar",

      cert_tag: "Garantía Internacional",
      cert_title: "Certificaciones de Calidad y Fitosanidad",
      cert_subtitle: "Cumplimos con las regulaciones y auditorías internacionales más estrictas para asegurar inocuidad alimentaria, bienestar laboral y respeto ambiental.",
      cert_globalgap: "Buenas Prácticas Agrícolas mundiales que garantizan la inocuidad alimentaria y la sostenibilidad del cultivo.",
      cert_grasp: "Evaluación de riesgos en las prácticas sociales de los trabajadores, salud y seguridad laboral en el campo.",
      cert_rainforest: "Conservación de la biodiversidad, protección de fuentes de agua y manejo responsable del ecosistema.",
      cert_basc: "Cadena logística segura y libre de ilícitos, avalada por Agrocalidad y agencias fitosanitarias de destino.",

      logistics_tag: "Logística de Exportación",
      logistics_title: "Cadena de Frío Avanzada y Conectividad Global",
      logistics_p1: "El secreto para que nuestras frutas lleguen con el mismo brillo, aroma y firmeza que tenían en la planta es nuestra gestión milimétrica de la cadena de frío.",
      log_p1_title: "Pre-enfriamiento Rápido en Planta",
      log_p1_desc: "Reducción inmediata del calor de campo tras la cosecha para detener la senescencia.",
      log_p2_title: "Contenedores Reefer con Atmósfera Controlada",
      log_p2_desc: "Regulación precisa de O2 y CO2 para extender la vida en tránsito marítimo hacia Asia y Europa.",
      log_p3_title: "Despachos Marítimos y Aéreos Directos",
      log_p3_desc: "Operamos desde los puertos de Guayaquil / Posorja y aeropuertos internacionales de Quito / Guayaquil.",
      log_shipping_routes: "Rutas Principales",

      quote_tag: "Cotización Directa",
      quote_title: "Solicita tu Cotización de Contenedores o Carga Aérea",
      quote_subtitle: "Ingresa los detalles de tu requerimiento y nuestro equipo comercial te responderá en menos de 24 horas con disponibilidad, especificaciones y precios FOB / CIF.",
      form_name: "Nombre Completo / Contacto",
      form_company: "Empresa / Importadora",
      form_email: "Correo Corporativo",
      form_phone: "Teléfono / WhatsApp",
      form_fruit: "Fruta / Producto de Interés",
      form_fruit_placeholder: "Seleccione una fruta...",
      form_volume: "Volumen / Frecuencia",
      form_dest_country: "País y Puerto de Destino",
      form_incoterm: "Término Incoterm Deseado",
      form_notes: "Especificaciones Adicionales / Requerimientos",
      form_btn_send: "Enviar Solicitud de Cotización",
      form_btn_wa: "Cotizar al Instante por WhatsApp",
      alert_success_title: "¡Solicitud recibida con éxito!",
      alert_success_desc: "Nuestro equipo de comercio exterior se pondrá en contacto contigo de inmediato.",

      contact_tag: "Contacto Global",
      contact_title: "Conectemos para tu Próximo Despacho",
      contact_desc: "Estamos listos para ser tu socio estratégico en el abastecimiento continuo de frutas tropicales de alta calidad.",
      contact_loc_title: "Oficinas y Plantas de Empaque",
      contact_email_title: "Correos de Comercio Exterior",
      contact_phone_title: "Atención Telefónica y WhatsApp 24/7",
      contact_hours_title: "Horario de Operación",
      map_text: "Despachos desde los principales puertos del Pacífico hacia el mundo",

      modal_close: "Cerrar",
      btn_quote_this: "Cotizar este Producto",
      wa_tooltip: "¡Chatea con un asesor de exportación!",
      footer_desc: "Exportación de frutas y raíces tropicales de alta gama hacia América del Norte, Europa, Asia y Medio Oriente. Frescura, trazabilidad y compromiso con la calidad.",
      footer_fruits: "Nuestras Frutas",
      footer_nav: "Navegación",
      footer_certs: "Estándares",
      footer_rights: "Todos los derechos reservados.",
      footer_global: "Exportando frescura de Ecuador para el mundo 🌍"
    },
    en: {
      topbar_location: "Ecuador / Latin America • Global Export",
      topbar_quality: "100% Certified Export Quality",
      brand_sub: "GLOBAL FRESH FRUIT EXPORTER",
      nav_home: "Home",
      nav_about: "About Us",
      nav_products: "Products",
      nav_cert: "Certifications",
      nav_logistics: "Logistics",
      nav_contact: "Contact",
      btn_request_quote: "Request a Quote",

      hero_badge: "Premium Tropical Fruits & Roots for Export",
      hero_title: "Delivering Tropical Freshness & Flavor to Global Markets",
      hero_desc: "Leading growers and exporters of <strong>Cavendish Bananas</strong>, <strong>Green Plantains</strong>, <strong>Red & Yellow Dragon Fruit</strong>, <strong>Malanga (Taro)</strong>, <strong>Passion Fruit</strong>, <strong>MD2 Pineapple</strong>, and <strong>Fresh Mangoes</strong>. Strict phytosanitary compliance, complete farm traceability, and unbroken cold chain.",
      hero_btn_explore: "Explore Catalog",
      hero_btn_quote: "B2B Quote Tool",
      hero_btn_whatsapp: "Direct WhatsApp",

      stat_countries: "Destination Countries",
      stat_coldchain: "Controlled Cold Chain",
      stat_hectares: "Farming Hectares",
      stat_cert: "International Standards",

      about_tag: "About Global Market GM",
      about_title: "Dedicated to Agricultural Excellence and Global Freshness",
      about_p1: "At <strong>Global Market GM</strong> we are passionate about growing, selecting, and exporting the finest tropical produce to wholesalers, supermarket chains, and food distributors across North America, Europe, Asia, and the Middle East.",
      about_p2: "Our dedicated farms across Ecuador's most fertile agricultural valleys deliver exceptional nutrient density, optimal natural Brix sweetness, and extended shelf life through rigorous post-harvest handling.",
      about_badge_title: "Sustainable Farming",
      about_badge_sub: "Eco-friendly and organic practices",
      about_feat1_title: "End-to-End Traceability",
      about_feat1_desc: "From harvest grove and batch code to destination port discharge.",
      about_feat2_title: "24/7 Temperature Monitoring",
      about_feat2_desc: "Controlled Atmosphere Reefer containers ensuring maximum firmness and freshness.",
      about_feat3_title: "Reliable Long-Term Partnerships",
      about_feat3_desc: "Transparent supply agreements, stable annual programs, and timely arrivals.",

      prod_tag: "Export Catalog",
      prod_title: "Our World-Class Tropical Fruits & Produce",
      prod_subtitle: "Hand-harvested and graded under the strictest international weight, caliber, color, sweetness, and maturity standards.",
      
      // Badges
      badge_all_year: "Year-Round Supply",
      badge_season_mango: "October - February",
      badge_high_demand: "Exotic Superfruit",
      badge_sweetest: "Sweetest Fruit (18° Brix)",
      badge_energy: "High Caliber & Energy",
      badge_gourmet: "Unique Aroma & Acidity",
      badge_premium_sweet: "Golden Extra Sweet",
      badge_tubers: "Grade 1 Clean Tubers",

      // Origins
      origin_ecuador: "Origin: Ecuador",
      origin_ecuador_costa: "Origin: Ecuadorian Coast",
      origin_palora: "Origin: Palora / Amazon Basin",
      origin_manabi: "Origin: Manabí / Santo Domingo",
      origin_losrios: "Origin: Los Ríos / Guayas",
      origin_santodomingo: "Origin: Santo Domingo / Coast",

      // Names & Descriptions
      banano_name: "Premium Cavendish Banana",
      banano_desc: "Internationally acclaimed for its smooth texture, rich natural flavor, and exceptional transit resilience. Grown in mineral-rich volcanic soils.",
      
      platano_name: "Green Plantain (Barraganete)",
      platano_desc: "Large-caliber, extra-firm green plantains with excellent starch consistency for cooking, processing, and snack industries. Superior reefer shelf life.",

      pitared_name: "Red Dragon Fruit (Pitahaya Roja)",
      pitared_desc: "Stunning magenta-pink skin with deep purple-red antioxidant-rich pulp. Outstanding shelf life and exceptional appeal for gourmet and retail channels.",

      pitayellow_name: "Yellow Dragon Fruit (Pitahaya Amarilla)",
      pitayellow_desc: "The world-famous golden fruit from Palora. Crunchy translucent flesh, unmatched honey sweetness (16°-19° Brix), and digestive benefits.",

      malanga_name: "Malanga / Taro Root (White & Purple)",
      malanga_desc: "Premium selected tropical tubers, washed and brushed, offering dense white/purple flesh and high nutrient value. Strict export sorting.",

      maracuya_name: "Passion Fruit (Maracuyá)",
      maracuya_desc: "Golden-yellow tropical passion fruit with intense exotic aroma. Juicy pulp with balanced sweetness and tart acidity, ideal for fresh consumption and gourmet juice.",

      pina_name: "MD2 Golden Sweet Pineapple",
      pina_desc: "Vibrant golden pineapple with tender juicy flesh, high vitamin C, and low acidity. Harvested at optimal sugar brix levels for global transit.",

      mango_name: "Export Mango (Tommy Atkins & Kent)",
      mango_desc: "Juicy, vibrantly colored red/yellow mangoes with sweet aroma and fiber-free firm pulp. USDA-APHIS certified hydrothermal treatment available.",

      // Specs labels
      spec_grade: "Grade:",
      spec_caliber: "Caliber:",
      spec_calibers: "Calibers:",
      spec_packaging: "Packaging:",
      spec_varieties: "Varieties:",
      spec_variety: "Species:",
      spec_brix: "Sweetness / Brix:",
      spec_transport: "Transport:",
      spec_finger_length: "Finger length:",
      btn_specs: "Tech Specs",
      btn_quote_item: "Quote",

      cert_tag: "Global Assurance",
      cert_title: "Quality & Phytosanitary Certifications",
      cert_subtitle: "We comply with the world's most demanding agricultural standards and audits to guarantee food safety, ethical labor, and environmental conservation.",
      cert_globalgap: "Global Good Agricultural Practices guaranteeing food safety and sustainable farm operations.",
      cert_grasp: "Social practice risk assessment, occupational health, and field worker safety compliance.",
      cert_rainforest: "Biodiversity protection, water resource conservation, and ecosystem management.",
      cert_basc: "Secure international supply chain certified by BASC and national phytosanitary authorities (Agrocalidad).",

      logistics_tag: "Export Logistics",
      logistics_title: "Advanced Cold Chain & Global Connectivity",
      logistics_p1: "The secret to delivering fruits with the same vibrant aroma and crisp firmness as on the plantation is our millimeter-precision cold chain control.",
      log_p1_title: "Rapid Hydro-Cooling / Pre-cooling",
      log_p1_desc: "Immediate field-heat extraction post-harvest to halt senescence.",
      log_p2_title: "Controlled Atmosphere Reefer Containers",
      log_p2_desc: "Exact O2 and CO2 regulation extending transit life on maritime voyages to Asia and Europe.",
      log_p3_title: "Direct Maritime & Air Freight Shipments",
      log_p3_desc: "Dispatched from deep-water ports in Guayaquil/Posorja and international airports in Quito/Guayaquil.",
      log_shipping_routes: "Main Trade Lanes",

      quote_tag: "Direct RFQ",
      quote_title: "Request Your Container or Air Cargo Quotation",
      quote_subtitle: "Submit your requirement details and our export sales desk will provide availability, specs, and competitive FOB/CIF quotes within 24 hours.",
      form_name: "Full Name / Contact Person",
      form_company: "Company / Importer Name",
      form_email: "Corporate Email",
      form_phone: "Phone / WhatsApp",
      form_fruit: "Product of Interest",
      form_fruit_placeholder: "Select product...",
      form_volume: "Volume / Frequency",
      form_dest_country: "Destination Country & Port",
      form_incoterm: "Incoterm Desired",
      form_notes: "Additional Specifications / Requirements",
      form_btn_send: "Submit RFQ Quote Request",
      form_btn_wa: "Quote Instantly on WhatsApp",
      alert_success_title: "Quote request received successfully!",
      alert_success_desc: "Our international trade team will contact you shortly with formal specs.",

      contact_tag: "Global Inquiries",
      contact_title: "Let's Connect for Your Next Shipment",
      contact_desc: "We are ready to become your strategic supply partner for continuous volumes of premium tropical fruits.",
      contact_loc_title: "Headquarters & Packing Houses",
      contact_email_title: "Export Trade Inquiries",
      contact_phone_title: "24/7 Trade Desk & WhatsApp",
      contact_hours_title: "Office Hours",
      map_text: "Dispatches from premier Pacific Ocean deep-water ports to global destinations",

      modal_close: "Close",
      btn_quote_this: "Quote this Product",
      wa_tooltip: "Chat with an export specialist!",
      footer_desc: "Exporting world-class tropical fruits and produce to North America, Europe, Asia, and the Middle East. Freshness, traceability, and certified quality.",
      footer_fruits: "Our Fruit Portfolio",
      footer_nav: "Navigation",
      footer_certs: "Standards",
      footer_rights: "All rights reserved.",
      footer_global: "Exporting Ecuadorian freshness to the world 🌍"
    }
  };

  // State
  let currentLang = 'es';

  function applyLanguage(lang) {
    currentLang = lang;
    document.documentElement.lang = lang;
    const dict = translations[lang];

    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      if (dict[key]) {
        el.innerHTML = dict[key];
      }
    });

    const langLabel = document.getElementById('currentLangLabel');
    if (langLabel) {
      langLabel.textContent = lang === 'es' ? '🇪🇸 ES' : '🇺🇸 EN';
    }

    document.querySelectorAll('.lang-option').forEach(opt => {
      opt.classList.toggle('active', opt.getAttribute('data-lang') === lang);
    });

    // Update select placeholder
    const fruitSelect = document.getElementById('fruitSelect');
    if (fruitSelect && fruitSelect.options[0]) {
      fruitSelect.options[0].textContent = dict.form_fruit_placeholder;
    }
  }

  // Language Dropdown Toggle
  const langSwitch = document.getElementById('langSwitch');
  const langDropdown = document.getElementById('langDropdown');

  if (langSwitch && langDropdown) {
    langSwitch.addEventListener('click', (e) => {
      e.stopPropagation();
      langDropdown.classList.toggle('show');
    });

    document.addEventListener('click', () => {
      langDropdown.classList.remove('show');
    });

    document.querySelectorAll('.lang-option').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const selectedLang = btn.getAttribute('data-lang');
        applyLanguage(selectedLang);
        langDropdown.classList.remove('show');
      });
    });
  }

  // ==========================================
  // MOBILE NAVIGATION DRAWER
  // ==========================================
  const mobileToggle = document.getElementById('mobileToggle');
  const mobileClose = document.getElementById('mobileClose');
  const mobileDrawer = document.getElementById('mobileDrawer');

  if (mobileToggle && mobileDrawer) {
    mobileToggle.addEventListener('click', () => {
      mobileDrawer.classList.add('open');
    });
  }

  if (mobileClose && mobileDrawer) {
    mobileClose.addEventListener('click', () => {
      mobileDrawer.classList.remove('open');
    });
  }

  document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => {
      if (mobileDrawer) mobileDrawer.classList.remove('open');
    });
  });

  // ==========================================
  // TECHNICAL SHEETS MODAL DATA (ALL 8 FRUITS)
  // ==========================================
  const productSpecs = {
    banano: {
      title_es: "Ficha Técnica: Banano Cavendish Premium",
      title_en: "Tech Sheet: Premium Cavendish Banana",
      badge: "GlobalG.A.P. / 1st Class Export",
      specs_es: [
        { label: "Variedad Botánica", val: "Musa acuminata (Cavendish - Williams / Grand Nain)" },
        { label: "Origen de Cultivo", val: "Ecuador (Guayas, Los Ríos, El Oro)" },
        { label: "Calibración", val: "Grado 39 a 46 mm (39/46)" },
        { label: "Largo de Dedo", val: "Mínimo 7.5 a 8.5 pulgadas (19 - 22 cm)" },
        { label: "Dedos por Clúster", val: "4 a 8 dedos seleccionados sin defectos" },
        { label: "Empaque Primario", val: "Bolsa al vacío Banavac / Polietileno microperforado" },
        { label: "Caja Master", val: "Cartón corrugado 18.14 kg (40 lb) o 19.43 kg neto" },
        { label: "Capacidad por Contenedor", val: "1,080 cajas por contenedor Reefer 40ft (Paletizado)" },
        { label: "Temperatura de Transporte", val: "13.2°C - 13.5°C con ventilación constante" },
        { label: "Vida Útil en Tránsito", val: "Hasta 35 - 40 días en atmósfera controlada" }
      ],
      specs_en: [
        { label: "Botanical Variety", val: "Musa acuminata (Cavendish - Williams / Grand Nain)" },
        { label: "Origin", val: "Ecuador (Guayas, Los Ríos, El Oro provinces)" },
        { label: "Calibration", val: "Grade 39 to 46 mm (39/46)" },
        { label: "Finger Length", val: "Minimum 7.5 to 8.5 inches (19 - 22 cm)" },
        { label: "Fingers per Cluster", val: "4 to 8 selected uniform fingers" },
        { label: "Primary Packaging", val: "Banavac vacuum bag / Microperforated liner" },
        { label: "Master Box", val: "Corrugated carton 18.14 kg (40 lb) or 19.43 kg net" },
        { label: "Container Load", val: "1,080 boxes per 40ft Reefer Container (Palletized)" },
        { label: "Transit Temperature", val: "13.2°C - 13.5°C with controlled ventilation" },
        { label: "Shelf Life in Transit", val: "Up to 35 - 40 days under Controlled Atmosphere" }
      ]
    },
    platano: {
      title_es: "Ficha Técnica: Plátano Verde / Barraganete",
      title_en: "Tech Sheet: Green Plantain (Barraganete)",
      badge: "Extra Large / High Consistency",
      specs_es: [
        { label: "Variedad Botánica", val: "Musa balbisiana x acuminata (Barraganete / Macho)" },
        { label: "Origen de Cultivo", val: "Ecuador (Manabí - El Carmen, Santo Domingo, Los Ríos)" },
        { label: "Calibración", val: "Grado 48 a 56 mm" },
        { label: "Largo de Dedo", val: "9.5 a 12 pulgadas (24 - 30 cm)" },
        { label: "Color / Textura", val: "Verde intenso uniforme, pulpa blanca-crema firme" },
        { label: "Empaque Master", val: "Cajas de cartón corrugado de 50 lb (22.7 kg) o 40 lb (18.14 kg)" },
        { label: "Capacidad Contenedor", val: "960 a 1,080 cajas por contenedor Reefer 40ft" },
        { label: "Temperatura Transporte", val: "12.0°C - 13.0°C con humedad relativa 85-90%" },
        { label: "Usos Principales", val: "Consumo fresco, snacks (chifles), industria congelada" },
        { label: "Disponibilidad", val: "Suministro constante las 52 semanas del año" }
      ],
      specs_en: [
        { label: "Botanical Variety", val: "Musa balbisiana x acuminata (Barraganete / Plantain)" },
        { label: "Origin", val: "Ecuador (Manabí, Santo Domingo, Los Ríos)" },
        { label: "Calibration", val: "Grade 48 to 56 mm" },
        { label: "Finger Length", val: "9.5 to 12.0 inches (24 - 30 cm)" },
        { label: "Color & Texture", val: "Uniform deep green, firm creamy-white pulp" },
        { label: "Master Box", val: "Corrugated boxes of 50 lb (22.7 kg) or 40 lb (18.14 kg)" },
        { label: "Container Load", val: "960 to 1,080 boxes per 40ft Reefer Container" },
        { label: "Transit Temperature", val: "12.0°C - 13.0°C with 85-90% relative humidity" },
        { label: "Key Applications", val: "Fresh culinary, plantain chips / snacks, frozen processing" },
        { label: "Availability", val: "Steady 52-week annual export programs" }
      ]
    },
    pitahaya_roja: {
      title_es: "Ficha Técnica: Pitahaya Roja (Red Dragon Fruit)",
      title_en: "Tech Sheet: Red Dragon Fruit (Pitahaya Roja)",
      badge: "Antioxidant Superfruit / Grade A",
      specs_es: [
        { label: "Especie Botánica", val: "Hylocereus monacanthus / polyrhizus" },
        { label: "Color de Pulpa", val: "Rojo / Púrpura intenso (Magenta antioxidante)" },
        { label: "Calibres Comerciales", val: "6, 7, 8, 9, 10, 12 (350g a 700g por fruto)" },
        { label: "Grados Brix", val: "12.0° a 14.5° Brix" },
        { label: "Presentación", val: "Cajas telescópicas de 4.0 kg o 4.5 kg neto con alveolos protectores" },
        { label: "Modo de Despacho", val: "Aéreo (Pallets rápidos) y Marítimo Reefer" },
        { label: "Temperatura de Transporte", val: "6.0°C - 8.0°C con humedad relativa 85-90%" },
        { label: "Ventilación", val: "15 m³/hora en contenedor marítimo" },
        { label: "Disponibilidad", val: "Picos principales: Diciembre - Mayo y floraciones continuas" }
      ],
      specs_en: [
        { label: "Botanical Species", val: "Hylocereus monacanthus / polyrhizus" },
        { label: "Flesh Color", val: "Vibrant deep magenta red / purple (antioxidant-packed)" },
        { label: "Commercial Calibers", val: "6, 7, 8, 9, 10, 12 (350g to 700g each)" },
        { label: "Brix Sweetness", val: "12.0° to 14.5° Brix" },
        { label: "Packaging", val: "Telescopic master boxes 4.0 kg or 4.5 kg net with protective trays" },
        { label: "Freight Mode", val: "Air cargo (express pallets) and Ocean Reefer" },
        { label: "Transit Temperature", val: "6.0°C - 8.0°C with 85-90% relative humidity" },
        { label: "Ventilation", val: "15 cbm/hour in ocean reefer" },
        { label: "Availability", val: "Peak season: December - May & year-round secondary flushes" }
      ]
    },
    pitahaya_amarilla: {
      title_es: "Ficha Técnica: Pitahaya Amarilla de Palora",
      title_en: "Tech Sheet: Palora Yellow Dragon Fruit",
      badge: "World's Sweetest (16-19° Brix)",
      specs_es: [
        { label: "Especie Botánica", val: "Selenicereus megalanthus (Pitahaya Amarilla)" },
        { label: "Denominación de Origen", val: "Palora, Morona Santiago (Amazonía Ecuatoriana)" },
        { label: "Calibres Disponibles", val: "6, 7, 8, 9, 10, 12 (180g a 500g por unidad)" },
        { label: "Dulzura Excepcional", val: "16.0° a 19.5° Grados Brix (Sabor a miel natural)" },
        { label: "Empaque", val: "Cajas de cartón de 2.5 kg, 4.0 kg y 4.5 kg neto" },
        { label: "Mercados Principales", val: "Estados Unidos, Canadá, Singapur, Hong Kong, Dubái, Europa" },
        { label: "Temperatura de Transporte", val: "10.0°C - 12.0°C en transporte refrigerado" },
        { label: "Beneficios Clave", val: "Alta en fibra soluble (captina), vitamina C y prebióticos naturales" }
      ],
      specs_en: [
        { label: "Botanical Species", val: "Selenicereus megalanthus (Yellow Pitahaya)" },
        { label: "Appellation of Origin", val: "Palora, Morona Santiago (Ecuadorian Amazon)" },
        { label: "Available Calibers", val: "6, 7, 8, 9, 10, 12 (180g to 500g each)" },
        { label: "Exceptional Sweetness", val: "16.0° to 19.5° Brix (Natural honeyed flavor profile)" },
        { label: "Packaging", val: "Corrugated master boxes 2.5 kg, 4.0 kg, and 4.5 kg net" },
        { label: "Key Export Destinations", val: "United States, Canada, Singapore, Hong Kong, Dubai, Europe" },
        { label: "Transit Temperature", val: "10.0°C - 12.0°C in temperature-controlled reefer" },
        { label: "Health Benefits", val: "Rich in soluble fiber (captin), Vitamin C, and natural prebiotics" }
      ]
    },
    malanga: {
      title_es: "Ficha Técnica: Malanga / Taro (Blanca & Lila)",
      title_en: "Tech Sheet: Malanga / Taro Root (White & Purple)",
      badge: "High Grade / Washed & Brushed",
      specs_es: [
        { label: "Especie Botánica", val: "Xanthosoma sagittifolium (Malanga Blanca / Lila) & Colocasia" },
        { label: "Origen de Cultivo", val: "Ecuador (Santo Domingo de los Tsáchilas, Los Ríos, Amazonía)" },
        { label: "Clasificación / Calidad", val: "Primera Categoría, limpia, cepillada, desinfectada y seca" },
        { label: "Calibres Disponibles", val: "Mediana (300g - 600g) y Grande (600g - 1200g)" },
        { label: "Textura y Carne", val: "Firme, blanca brillante o con jaspeado púrpura/lila" },
        { label: "Empaque Master", val: "Cajas de cartón corrugado de 40 lb (18.14 kg) o 50 lb (22.7 kg)" },
        { label: "Capacidad por Contenedor", val: "1,000 a 1,050 cajas por contenedor Reefer 40ft (Paletizado)" },
        { label: "Temperatura de Transporte", val: "10.0°C - 12.0°C con humedad relativa del 85%" },
        { label: "Vida Útil en Tránsito", val: "Hasta 40 - 50 días de excelente conservación" },
        { label: "Disponibilidad", val: "Todo el año (Producción continua)" }
      ],
      specs_en: [
        { label: "Botanical Species", val: "Xanthosoma sagittifolium (White/Purple Taro) & Colocasia" },
        { label: "Origin", val: "Ecuador (Santo Domingo, Los Ríos, Amazon Basin)" },
        { label: "Grade / Quality", val: "Grade 1, thoroughly washed, brushed, treated, and dried" },
        { label: "Available Sizes", val: "Medium (300g - 600g) and Large (600g - 1200g)" },
        { label: "Flesh & Texture", val: "Dense, crisp white or speckled with delicate purple marbling" },
        { label: "Master Box", val: "Heavy-duty corrugated boxes of 40 lb (18.14 kg) or 50 lb (22.7 kg)" },
        { label: "Container Load", val: "1,000 to 1,050 boxes per 40ft Reefer Container (Palletized)" },
        { label: "Transit Temperature", val: "10.0°C - 12.0°C with 85% relative humidity" },
        { label: "Transit Shelf Life", val: "Up to 40 - 50 days of optimal storage" },
        { label: "Availability", val: "Year-round uninterrupted supply" }
      ]
    },
    maracuya: {
      title_es: "Ficha Técnica: Maracuyá (Passion Fruit)",
      title_en: "Tech Sheet: Tropical Passion Fruit (Maracuyá)",
      badge: "High Aroma / Premium Juice Yield",
      specs_es: [
        { label: "Especie Botánica", val: "Passiflora edulis f. flavicarpa (Maracuyá Amarillo)" },
        { label: "Origen de Cultivo", val: "Ecuador (Manabí, Guayas, Esmeraldas, Santo Domingo)" },
        { label: "Calibres Disponibles", val: "Calibres 24, 28, 32, 36 unidades por caja (120g - 200g/fruta)" },
        { label: "Grados Brix", val: "14.0° a 17.5° Brix (Intenso perfil aromático tropical)" },
        { label: "Rendimiento de Jugo", val: "Superior al 35% de pulpa y semillas" },
        { label: "Empaque Primario/Master", val: "Cajas de cartón de 2.0 kg o 4.0 kg neto con alveolos protectores" },
        { label: "Modalidad de Despacho", val: "Aéreo (alta frescura retail) y Marítimo refrigerado" },
        { label: "Temperatura de Transporte", val: "8.0°C - 10.0°C con humedad relativa 85-90%" },
        { label: "Vida Útil en Tránsito", val: "21 a 28 días con cadena de frío" },
        { label: "Disponibilidad", val: "Cosecha continua durante las 52 semanas del año" }
      ],
      specs_en: [
        { label: "Botanical Species", val: "Passiflora edulis f. flavicarpa (Yellow Passion Fruit)" },
        { label: "Origin", val: "Ecuador (Manabí, Guayas, Esmeraldas, Santo Domingo)" },
        { label: "Calibers / Counts", val: "Counts 24, 28, 32, 36 pieces per box (120g - 200g each)" },
        { label: "Brix Sweetness", val: "14.0° to 17.5° Brix (High exotic aroma & acidity balance)" },
        { label: "Juice Yield", val: "Greater than 35% pulp and seeds yield" },
        { label: "Packaging", val: "Carton boxes 2.0 kg or 4.0 kg net with protective honeycomb trays" },
        { label: "Freight Mode", val: "Air Freight (express retail) and Ocean Reefer" },
        { label: "Transit Temperature", val: "8.0°C - 10.0°C with 85-90% relative humidity" },
        { label: "Transit Shelf Life", val: "21 to 28 days with continuous refrigeration" },
        { label: "Availability", val: "Year-round steady harvest" }
      ]
    },
    pina: {
      title_es: "Ficha Técnica: Piña Golden MD2",
      title_en: "Tech Sheet: MD2 Golden Sweet Pineapple",
      badge: "Extra Sweet / Golden Color 2-3",
      specs_es: [
        { label: "Variedad Botánica", val: "Ananas comosus var. MD2 (Golden Sweet)" },
        { label: "Origen de Cultivo", val: "Ecuador (Santo Domingo, Los Ríos, Guayas)" },
        { label: "Calibres Disponibles", val: "Calibre 5, 6, 7, 8, 9, 10 frutas por caja master" },
        { label: "Grados Brix", val: "13.5° a 16.0° Brix al empaque (Dulzura garantizada)" },
        { label: "Color de Cáscara", val: "Grado de color 2 a 3.5 (Dorado brillante con corona verde)" },
        { label: "Empaque Master", val: "Cajas telescópicas de cartón corrugado de 11.5 kg / 12.0 kg neto" },
        { label: "Capacidad por Contenedor", val: "1,600 a 1,680 cajas por contenedor Reefer 40ft (Paletizado)" },
        { label: "Temperatura de Transporte", val: "7.5°C - 8.5°C con ventilación adecuada" },
        { label: "Vida Útil en Tránsito", val: "21 a 28 días en transporte marítimo reefer" },
        { label: "Disponibilidad", val: "Todo el año (Programas semanales estables)" }
      ],
      specs_en: [
        { label: "Botanical Variety", val: "Ananas comosus var. MD2 (Golden Sweet)" },
        { label: "Origin", val: "Ecuador (Santo Domingo, Los Ríos, Guayas)" },
        { label: "Available Calibers", val: "Counts 5, 6, 7, 8, 9, 10 fruits per master box" },
        { label: "Brix Sweetness", val: "13.5° to 16.0° Brix at packaging (Certified high sweetness)" },
        { label: "Skin Color Grade", val: "Color grade 2 to 3.5 (Vibrant golden with crisp green crown)" },
        { label: "Master Box", val: "Telescopic corrugated boxes of 11.5 kg / 12.0 kg net" },
        { label: "Container Load", val: "1,600 to 1,680 boxes per 40ft Reefer Container (Palletized)" },
        { label: "Transit Temperature", val: "7.5°C - 8.5°C with proper ventilation" },
        { label: "Transit Shelf Life", val: "21 to 28 days under ocean reefer" },
        { label: "Availability", val: "Year-round weekly fixed volume programs" }
      ]
    },
    mango: {
      title_es: "Ficha Técnica: Mango de Exportación",
      title_en: "Tech Sheet: Export Quality Mango",
      badge: "USDA-APHIS / Hot Water Treated",
      specs_es: [
        { label: "Variedades", val: "Tommy Atkins (65%), Kent (25%), Keitt, Ataulfo" },
        { label: "Origen de Cultivo", val: "Guayas y Santa Elena, Ecuador" },
        { label: "Calibres Disponibles", val: "6, 7, 8, 9, 10, 12, 14 (300g a 700g/fruta)" },
        { label: "Nivel de Dulzura", val: "12.0° a 15.0° Grados Brix a la cosecha" },
        { label: "Tratamiento Fitosanitario", val: "Tratamiento Hidrotérmico en planta certificada USDA-APHIS" },
        { label: "Empaque", val: "Cajas de cartón corrugado de 4.0 kg neto (8.8 lb)" },
        { label: "Capacidad por Contenedor", val: "5,280 a 5,500 cajas por contenedor Reefer 40ft" },
        { label: "Temperatura de Transporte", val: "10.0°C - 12.0°C (Kent: 12°C, Tommy: 10°C)" },
        { label: "Temporada de Cosecha", val: "Octubre a Febrero (Pico: Noviembre - Diciembre)" }
      ],
      specs_en: [
        { label: "Varieties", val: "Tommy Atkins (65%), Kent (25%), Keitt, Ataulfo" },
        { label: "Origin", val: "Guayas & Santa Elena, Ecuador" },
        { label: "Available Calibers", val: "6, 7, 8, 9, 10, 12, 14 (300g to 700g/piece)" },
        { label: "Sweetness Level", val: "12.0° to 15.0° Brix at packing" },
        { label: "Phytosanitary Treatment", val: "Hot Water Treatment in USDA-approved facility" },
        { label: "Packaging", val: "Corrugated master boxes 4.0 kg net (8.8 lb)" },
        { label: "Container Load", val: "5,280 to 5,500 boxes per 40ft Reefer Container" },
        { label: "Transit Temperature", val: "10.0°C - 12.0°C (Kent: 12°C, Tommy: 10°C)" },
        { label: "Harvest Season", val: "October to February (Peak: Nov - Dec)" }
      ]
    }
  };

  const specsModal = document.getElementById('specsModal');
  const modalClose = document.getElementById('modalClose');
  const modalBtnClose = document.getElementById('modalBtnClose');
  const modalProductTitle = document.getElementById('modalProductTitle');
  const modalProductBadge = document.getElementById('modalProductBadge');
  const modalProductContent = document.getElementById('modalProductContent');
  const modalBtnQuote = document.getElementById('modalBtnQuote');

  function openSpecsModal(productKey) {
    const data = productSpecs[productKey];
    if (!data || !specsModal) return;

    modalProductTitle.textContent = currentLang === 'es' ? data.title_es : data.title_en;
    modalProductBadge.textContent = data.badge;

    const list = currentLang === 'es' ? data.specs_es : data.specs_en;
    let tableHtml = '<table class="modal-specs-table"><tbody>';
    list.forEach(row => {
      tableHtml += `<tr><th>${row.label}</th><td>${row.val}</td></tr>`;
    });
    tableHtml += '</tbody></table>';

    modalProductContent.innerHTML = tableHtml;

    if (modalBtnQuote) {
      modalBtnQuote.onclick = () => {
        specsModal.classList.remove('open');
        const fruitSelect = document.getElementById('fruitSelect');
        if (fruitSelect) {
          const mapping = {
            'banano': 'Banano Cavendish',
            'platano': 'Plátano Verde / Barraganete',
            'pitahaya_roja': 'Pitahaya Roja',
            'pitahaya_amarilla': 'Pitahaya Amarilla',
            'malanga': 'Malanga / Taro',
            'maracuya': 'Maracuyá',
            'pina': 'Piña Golden MD2',
            'mango': 'Mango Tommy/Kent'
          };
          if (mapping[productKey]) {
            fruitSelect.value = mapping[productKey];
          }
        }
      };
    }

    specsModal.classList.add('open');
  }

  function closeSpecsModal() {
    if (specsModal) specsModal.classList.remove('open');
  }

  document.querySelectorAll('.btn-open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const pKey = btn.getAttribute('data-product');
      openSpecsModal(pKey);
    });
  });

  if (modalClose) modalClose.addEventListener('click', closeSpecsModal);
  if (modalBtnClose) modalBtnClose.addEventListener('click', closeSpecsModal);
  if (specsModal) {
    specsModal.addEventListener('click', (e) => {
      if (e.target === specsModal) closeSpecsModal();
    });
  }

  // Pre-fill fruit select on "Cotizar" button click from catalog card
  document.querySelectorAll('.btn-select-product').forEach(btn => {
    btn.addEventListener('click', () => {
      const fruit = btn.getAttribute('data-fruit');
      const fruitSelect = document.getElementById('fruitSelect');
      if (fruitSelect && fruit) {
        fruitSelect.value = fruit;
      }
    });
  });

  // ==========================================
  // RFQ FORM & WHATSAPP GENERATOR
  // ==========================================
  const quoteForm = document.getElementById('quoteForm');
  const btnWaQuote = document.getElementById('btnWaQuote');
  const quoteAlert = document.getElementById('quoteAlert');

  function buildWhatsAppMessage() {
    const name = document.getElementById('quoteName')?.value.trim() || 'Cliente';
    const company = document.getElementById('quoteCompany')?.value.trim() || 'No especificada';
    const fruit = document.getElementById('fruitSelect')?.value || 'Frutas de Exportación';
    const volume = document.getElementById('quoteVolume')?.value.trim() || '1 Contenedor FCL';
    const dest = document.getElementById('quoteDest')?.value.trim() || 'Puerto Internacional';
    const incoterm = document.getElementById('quoteIncoterm')?.value || 'FOB';
    const notes = document.getElementById('quoteNotes')?.value.trim() || 'Ninguna';

    const text = `👋 *SOLICITUD DE COTIZACIÓN - GLOBAL MARKET GM*\n\n` +
      `👤 *Contacto:* ${name}\n` +
      `🏢 *Empresa:* ${company}\n` +
      `🍎 *Producto:* ${fruit}\n` +
      `📦 *Volumen/Frecuencia:* ${volume}\n` +
      `🌍 *Destino:* ${dest}\n` +
      `📋 *Incoterm:* ${incoterm}\n` +
      `📝 *Notas:* ${notes}\n\n` +
      `_Enviado desde el portal globalmarket-gm.com_`;

    return encodeURIComponent(text);
  }

  if (btnWaQuote) {
    btnWaQuote.addEventListener('click', () => {
      const waNumber = '593999999999'; // Número comercial oficial
      const msg = buildWhatsAppMessage();
      window.open(`https://wa.me/${waNumber}?text=${msg}`, '_blank');
    });
  }

  if (quoteForm) {
    quoteForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const waNumber = '593999999999';
      const msg = buildWhatsAppMessage();

      if (quoteAlert) {
        quoteAlert.classList.add('show');
        setTimeout(() => {
          quoteAlert.classList.remove('show');
        }, 6000);
      }

      window.open(`https://wa.me/${waNumber}?text=${msg}`, '_blank');
      quoteForm.reset();
    });
  }

  // Smooth Header Scroll shadow
  const mainHeader = document.getElementById('mainHeader');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      mainHeader?.classList.add('scrolled');
    } else {
      mainHeader?.classList.remove('scrolled');
    }
  });

});
