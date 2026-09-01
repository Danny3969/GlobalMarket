import os

products = [
    {
        "id": "banano",
        "file": "banano.html",
        "name_es": "Banano Cavendish Premium",
        "name_en": "Premium Cavendish Banana",
        "scientific": "Musa acuminata (Williams / Grand Nain)",
        "hero_class": "product-hero-banano",
        "img": "assets/images/banano.jpg?v=4",
        "badge_es": "Disponibilidad: Todo el Año",
        "badge_en": "Availability: All Year Round",
        "tagline_es": "El banano ecuatoriano más codiciado a nivel mundial por su dulzura, calibre uniforme y excelente vida verde en anaquel.",
        "tagline_en": "The most sought-after Ecuadorian banana globally for its sweetness, uniform caliber, and outstanding green life.",
        "origin_es": "Provincias de El Oro, Guayas y Los Ríos, Ecuador (Suelos volcánicos)",
        "origin_en": "Provinces of El Oro, Guayas and Los Ríos, Ecuador (Volcanic soils)",
        "grade_es": "Premium Calidad de Exportación Clase 1",
        "grade_en": "Premium Export Quality Class 1",
        "calibers_es": "39 a 46 mm (Calibre estándar internacional)",
        "calibers_en": "39 to 46 mm (International standard)",
        "length_es": "Mínimo 7.5 a 8.5+ pulgadas (19 - 22 cm)",
        "length_en": "Minimum 7.5 to 8.5+ inches (19 - 22 cm)",
        "pack_es": "Cajas de cartón corrugado tipo 22XU de 18.14 kg (40 lb) o 19.43 kg neto",
        "pack_en": "Corrugated carton boxes type 22XU 18.14 kg (40 lb) or 19.43 kg net",
        "temp_es": "+13.2°C a +13.5°C (56°F) en contenedor refrigerado Reefer",
        "temp_en": "+13.2°C to +13.5°C (56°F) in Reefer refrigerated container",
        "vent_es": "Ventilación 25 cbm/h • Humedad relativa 85-90%",
        "vent_en": "Ventilation 25 cbm/h • Relative humidity 85-90%",
        "shelf_es": "28 a 35 días en tránsito con atmósfera controlada / bolsa Banavac",
        "shelf_en": "28 to 35 days in transit with controlled atmosphere / Banavac bag",
        "pallet_es": "48 a 54 cajas por pallet • 960 a 1,080 cajas por contenedor 40ft High Cube Reefer",
        "pallet_en": "48 to 54 boxes per pallet • 960 to 1,080 boxes per 40ft High Cube Reefer container",
        "brix_es": "Maduro 20° - 23° Brix • Almidón óptimo para despacho verde",
        "brix_en": "Ripe 20° - 23° Brix • Optimal starch for green shipping",
        "certs_es": "GLOBALG.A.P., GRASP, Rainforest Alliance, BASC, Fitosanitario AGROCALIDAD",
        "certs_en": "GLOBALG.A.P., GRASP, Rainforest Alliance, BASC, Phytosanitary AGROCALIDAD",
        "nutri": [
            {"title_es": "Potasio Activo", "title_en": "Active Potassium", "desc_es": "Regula la presión arterial y aporta energía duradera.", "desc_en": "Regulates blood pressure and provides long-lasting energy.", "icon": "fa-heart-pulse"},
            {"title_es": "Vitamina B6 & C", "title_en": "Vitamin B6 & C", "desc_es": "Fortalece el sistema inmune y apoya el sistema nervioso.", "desc_en": "Strengthens immunity and supports nervous system.", "icon": "fa-shield-halved"},
            {"title_es": "Fibra Prebiótica", "title_en": "Prebiotic Fiber", "desc_es": "Promueve una digestión saludable y saciedad natural.", "desc_en": "Promotes healthy digestion and natural satiety.", "icon": "fa-leaf"},
            {"title_es": "100% Natural", "title_en": "100% Natural", "desc_es": "Cero grasas, fuente pura de carbohidratos saludables.", "desc_en": "Zero fat, pure source of healthy carbohydrates.", "icon": "fa-sun"}
        ]
    },
    {
        "id": "platano",
        "file": "platano.html",
        "name_es": "Plátano Verde / Barraganete",
        "name_en": "Green Plantain / Barraganete",
        "scientific": "Musa balbisiana x acuminata (AAB)",
        "hero_class": "product-hero-platano",
        "img": "assets/images/platano.jpg?v=4",
        "badge_es": "Disponibilidad: Todo el Año",
        "badge_en": "Availability: All Year Round",
        "tagline_es": "Plátano macho verde barraganete de textura densa y alto contenido de almidón resistente, ideal para consumo en fresco e industria de snacks.",
        "tagline_en": "Green barraganete plantain with dense texture and high resistant starch, ideal for fresh culinary use and snack processing.",
        "origin_es": "El Carmen (Manabí), Santo Domingo de los Tsáchilas y Los Ríos, Ecuador",
        "origin_en": "El Carmen (Manabí), Santo Domingo and Los Ríos, Ecuador",
        "grade_es": "Primera Calidad Exportación • Cero defectos en cáscara",
        "grade_en": "First Quality Export • Zero skin defects",
        "calibers_es": "Grado 48 a 56 mm de diámetro en dedo central",
        "calibers_en": "Grade 48 to 56 mm diameter on central finger",
        "length_es": "Longitud de 9.5 a 12 pulgadas (24 - 30 cm)",
        "length_en": "Length 9.5 to 12 inches (24 - 30 cm)",
        "pack_es": "Cajas de cartón de 50 lb (22.7 kg) o 40 lb (18.14 kg) con bolsa polietileno",
        "pack_en": "Cardboard boxes of 50 lb (22.7 kg) or 40 lb (18.14 kg) with poly liner",
        "temp_es": "+11.5°C a +13.0°C (53°F - 55°F)",
        "temp_en": "+11.5°C to +13.0°C (53°F - 55°F)",
        "vent_es": "Ventilación 15-20 cbm/h • Humedad relativa 85%",
        "vent_en": "Ventilation 15-20 cbm/h • Relative humidity 85%",
        "shelf_es": "30 a 40 días en frío controlado manteniéndose 100% verde",
        "shelf_en": "30 to 40 days under controlled cold keeping 100% green state",
        "pallet_es": "40 a 48 cajas por pallet • 800 a 960 cajas por contenedor 40ft Reefer",
        "pallet_en": "40 to 48 boxes per pallet • 800 to 960 boxes per 40ft Reefer",
        "brix_es": "Almidón > 32% (Ideal para procesamiento industrial de chifles y tostones)",
        "brix_en": "Starch > 32% (Ideal for plantain chips and tostones processing)",
        "certs_es": "GLOBALG.A.P., BASC, AGROCALIDAD Fitosanitario de Exportación",
        "certs_en": "GLOBALG.A.P., BASC, AGROCALIDAD Export Phytosanitary",
        "nutri": [
            {"title_es": "Almidón Resistente", "title_en": "Resistant Starch", "desc_es": "Excelente para la salud intestinal y control glucémico.", "desc_en": "Superb for gut microbiota and glycemic control.", "icon": "fa-seedling"},
            {"title_es": "Magnesio & Potasio", "title_en": "Magnesium & Potassium", "desc_es": "Apoya la función muscular y recuperación metabólica.", "desc_en": "Supports muscular recovery and metabolic function.", "icon": "fa-bolt"},
            {"title_es": "Libre de Gluten", "title_en": "Gluten-Free", "desc_es": "Materia prima idónea para harinas y productos celíacos.", "desc_en": "Prime raw material for gluten-free flour and snacks.", "icon": "fa-wheat-awn-circle-exclamation"},
            {"title_es": "Energía Compleja", "title_en": "Complex Energy", "desc_es": "Carbohidratos de absorción lenta y sostenida.", "desc_en": "Slow-release sustained energy carbohydrates.", "icon": "fa-battery-full"}
        ]
    },
    {
        "id": "pitahaya_roja",
        "file": "pitahaya-roja.html",
        "name_es": "Pitahaya Roja (Red Dragon Fruit)",
        "name_en": "Red Dragon Fruit (Pitahaya Roja)",
        "scientific": "Hylocereus monacanthus (Pulpa Roja / Fucsia)",
        "hero_class": "product-hero-pitared",
        "img": "assets/images/pitahaya-roja.jpg?v=4",
        "badge_es": "Superfruta Exótica • Alta Demanda",
        "badge_en": "Exotic Superfruit • High Demand",
        "tagline_es": "Espectacular fruta dragón de corteza roja y pulpa púrpura profunda cargada de betalaínas y antioxidantes de primer nivel.",
        "tagline_en": "Spectacular red-skinned dragon fruit with deep purple flesh packed with premium betalains and antioxidants.",
        "origin_es": "Península de Santa Elena, Guayas y Manabí, Ecuador",
        "origin_en": "Santa Elena Peninsula, Guayas and Manabí, Ecuador",
        "grade_es": "Export Grade A / Extra • Brácteas verdes y firmes",
        "grade_en": "Export Grade A / Extra • Firm green bracts",
        "calibers_es": "Calibres 6, 7, 8, 9, 10, 12 (Pesos de 350g a 700g por fruto)",
        "calibers_en": "Calibers 6, 7, 8, 9, 10, 12 (Weights 350g to 700g per fruit)",
        "length_es": "Frutos ovalados simétricos de 10 a 16 cm",
        "length_en": "Symmetric oval fruits 10 to 16 cm",
        "pack_es": "Cajas de cartón de 4.0 kg y 4.5 kg neto con alveolos individuales o acolchado",
        "pack_en": "Cardboard boxes of 4.0 kg and 4.5 kg net with individual trays or padding",
        "temp_es": "+7.0°C a +10.0°C (45°F - 50°F) con ventilación controlada",
        "temp_en": "+7.0°C to +10.0°C (45°F - 50°F) with controlled airflow",
        "vent_es": "Ventilación 15 cbm/h • Humedad relativa 85-90%",
        "vent_en": "Ventilation 15 cbm/h • Relative humidity 85-90%",
        "shelf_es": "21 a 28 días en transporte marítimo o despacho aéreo express",
        "shelf_en": "21 to 28 days in maritime reefer or express airfreight",
        "pallet_es": "180 a 200 cajas por pallet • Carga aérea por AWB o contenedor marítimo",
        "pallet_en": "180 to 200 boxes per pallet • Airfreight AWB or ocean reefer",
        "brix_es": "12° a 15° Grados Brix (Sabor fresco, hidratante y suave)",
        "brix_en": "12° to 15° Brix (Fresh, hydrating and smooth flavor)",
        "certs_es": "GLOBALG.A.P., BPA Ecuador, AGROCALIDAD Fitosanitario",
        "certs_en": "GLOBALG.A.P., Ecuador GAP, AGROCALIDAD Phytosanitary",
        "nutri": [
            {"title_es": "Betalaínas & Antocianinas", "title_en": "Betalains & Anthocyanins", "desc_es": "Poderosos antioxidantes naturales para rejuvenecimiento celular.", "desc_en": "Powerful natural antioxidants for cellular anti-aging.", "icon": "fa-dna"},
            {"title_es": "Vitamina C", "title_en": "Vitamin C", "desc_es": "Estimula la producción de colágeno y defensas corporales.", "desc_en": "Boosts collagen production and body defense.", "icon": "fa-shield-virus"},
            {"title_es": "Semillas Ricas en Omega", "title_en": "Omega-Rich Seeds", "desc_es": "Ácidos grasos esenciales que favorecen el corazón.", "desc_en": "Essential fatty acids supporting cardiovascular health.", "icon": "fa-droplet"},
            {"title_es": "Hidratación Pura", "title_en": "Pure Hydration", "desc_es": "Más del 85% de agua biológicamente pura.", "desc_en": "Over 85% biologically structured water.", "icon": "fa-glass-water"}
        ]
    },
    {
        "id": "pitahaya_amarilla",
        "file": "pitahaya-amarilla.html",
        "name_es": "Pitahaya Amarilla de Palora (Yellow Dragon)",
        "name_en": "Palora Yellow Dragon Fruit",
        "scientific": "Selenicereus megalanthus (Pitahaya Amarilla de la Amazonía)",
        "hero_class": "product-hero-pitayellow",
        "img": "assets/images/pitahaya-amarilla.jpg?v=4",
        "badge_es": "Dulzura Suprema (18° Brix) • Denominación Palora",
        "badge_en": "Supreme Sweetness (18° Brix) • Palora Origin",
        "tagline_es": "La reina de las frutas exóticas del Ecuador: corteza amarilla con espinas removidas y una pulpa dulce crujiente con sabor inigualable.",
        "tagline_en": "The queen of Ecuadorian exotics: bright yellow skin with destemmed thorns and a crunchy, supremely sweet pulp.",
        "origin_es": "Valle de Palora, Morona Santiago (Amazonía Ecuatoriana)",
        "origin_en": "Palora Valley, Morona Santiago (Ecuadorian Amazon Rainforest)",
        "grade_es": "Extra Calidad Gourmet • Fruta desespinada y pulida mecánicamente",
        "grade_en": "Extra Gourmet Quality • Despined and polished under strict hygiene",
        "calibers_es": "Calibres 6, 7, 8, 9, 10, 12 (Peso 250g a 500g por fruto)",
        "calibers_en": "Calibers 6, 7, 8, 9, 10, 12 (Weight 250g to 500g per piece)",
        "length_es": "Forma cónica-ovalada de 10 a 14 cm",
        "length_en": "Conical-oval shape 10 to 14 cm",
        "pack_es": "Cajas de cartón de 2.5 kg, 4.0 kg y 4.5 kg neto",
        "pack_en": "Cardboard boxes of 2.5 kg, 4.0 kg and 4.5 kg net",
        "temp_es": "+10.0°C a +12.0°C (50°F - 54°F)",
        "temp_en": "+10.0°C to +12.0°C (50°F - 54°F)",
        "vent_es": "Ventilación 10-15 cbm/h • Humedad relativa 85%",
        "vent_en": "Ventilation 10-15 cbm/h • Relative humidity 85%",
        "shelf_es": "25 a 35 días en frío / Despachos aéreos diarios a Miami, Madrid, Dubái y Shanghai",
        "shelf_en": "25 to 35 days cold chain / Daily air shipments to Miami, Madrid, Dubai & Shanghai",
        "pallet_es": "160 a 240 cajas por pallet dependiendo del peso de caja",
        "pallet_en": "160 to 240 boxes per pallet depending on box format",
        "brix_es": "16° a 19° Grados Brix (La pitahaya más dulce del planeta)",
        "brix_en": "16° to 19° Brix (The sweetest dragon fruit on Earth)",
        "certs_es": "GLOBALG.A.P., AGROCALIDAD Certificado de Sitio Libre, FDA Registrado",
        "certs_en": "GLOBALG.A.P., AGROCALIDAD Pest-Free Protocol, FDA Registered",
        "nutri": [
            {"title_es": "Efecto Digestivo Suave", "title_en": "Digestive Cleanse", "desc_es": "Famosa por su acción depurativa natural y captina.", "desc_en": "Renowned for gentle natural bowel regularity & captine.", "icon": "fa-feather"},
            {"title_es": "18° Brix Natural", "title_en": "18° Natural Brix", "desc_es": "Dulzura intensa de fructosa sin azúcares procesados.", "desc_en": "Intense fruit sugar taste without processed carbs.", "icon": "fa-candy-cane"},
            {"title_es": "Fósforo & Calcio", "title_en": "Phosphorus & Calcium", "desc_es": "Minerales esenciales para huesos y función cognitiva.", "desc_en": "Essential minerals for bone density and focus.", "icon": "fa-brain"},
            {"title_es": "Antioxidantes Polifenólicos", "title_en": "Polyphenols", "desc_es": "Protección contra radicales libres y estrés oxidativo.", "desc_en": "Protection against oxidative cell stress.", "icon": "fa-shield-halved"}
        ]
    },
    {
        "id": "malanga",
        "file": "malanga.html",
        "name_es": "Malanga / Taro (Blanca & Lila)",
        "name_en": "Malanga / Taro Root (White & Purple)",
        "scientific": "Xanthosoma sagittifolium / Colocasia esculenta",
        "hero_class": "product-hero-malanga",
        "img": "assets/images/malanga.jpg?v=4",
        "badge_es": "Tubérculo Hipoalergénico • Alta Resistencia",
        "badge_en": "Hypoallergenic Root • Long Shelf Life",
        "tagline_es": "Tubérculo tropical selecto, lavado a presión, cepillado y desinfectado, con pulpa densa de altísimo valor en los mercados étnico y gourmet.",
        "tagline_en": "Select tropical root, pressure-washed, brushed and sanitized, with dense flesh valued in ethnic and gourmet markets.",
        "origin_es": "Santo Domingo de los Tsáchilas, Quinindé y Manabí, Ecuador",
        "origin_en": "Santo Domingo, Quinindé and Manabí, Ecuador",
        "grade_es": "Grado 1 Exportación • Corteza limpia, sin cortes ni pudrición",
        "grade_en": "Export Grade 1 • Clean skin, free of decay or mechanical damage",
        "calibers_es": "Tamaño Mediano (300g - 600g) y Grande (600g - 1200g)",
        "calibers_en": "Medium size (300g - 600g) and Large size (600g - 1200g)",
        "length_es": "Cormos uniformes de 15 a 28 cm",
        "length_en": "Uniform corms 15 to 28 cm",
        "pack_es": "Cajas de cartón corrugado de 40 lb (18.14 kg) o 50 lb (22.7 kg)",
        "pack_en": "Corrugated boxes 40 lb (18.14 kg) or 50 lb (22.7 kg)",
        "temp_es": "+10.0°C a +13.0°C (50°F - 55°F) con baja humedad para evitar brotes",
        "temp_en": "+10.0°C to +13.0°C (50°F - 55°F) with dry airflow to prevent sprouting",
        "vent_es": "Ventilación 15 cbm/h • Humedad relativa 70-80%",
        "vent_en": "Ventilation 15 cbm/h • Relative humidity 70-80%",
        "shelf_es": "45 a 60 días en contenedor refrigerado de atmósfera convencional",
        "shelf_en": "45 to 60 days in refrigerated maritime container",
        "pallet_es": "40 a 48 cajas por pallet • 800 a 960 cajas por contenedor 40ft",
        "pallet_en": "40 to 48 boxes per pallet • 800 to 960 boxes per 40ft container",
        "brix_es": "Alto contenido de almidón no alergénico y proteínas vegetales",
        "brix_en": "High non-allergenic starch and plant-based protein content",
        "certs_es": "GLOBALG.A.P., BASC, Inspección Fitosanitaria AGROCALIDAD",
        "certs_en": "GLOBALG.A.P., BASC, AGROCALIDAD Phytosanitary Clearance",
        "nutri": [
            {"title_es": "100% Hipoalergénico", "title_en": "Hypoallergenic", "desc_es": "Ideal para bebés y personas con intolerancias alimentarias.", "desc_en": "Top choice for sensitive stomachs and gluten allergies.", "icon": "fa-heart"},
            {"title_es": "Bajo Índice Glucémico", "title_en": "Low Glycemic Index", "desc_es": "Absorción lenta sin picos bruscos de glucosa.", "desc_en": "Slow digestion avoiding sugar spikes.", "icon": "fa-chart-line"},
            {"title_es": "Hierro & Magnesio", "title_en": "Iron & Magnesium", "desc_es": "Previene la anemia y mejora la circulación sanguínea.", "desc_en": "Helps prevent anemia and improves blood flow.", "icon": "fa-vial"},
            {"title_es": "Fibra Dietética", "title_en": "Dietary Fiber", "desc_es": "Favorece la microbiota y tránsito digestivo regular.", "desc_en": "Supports gut microbiome and bowel regularity.", "icon": "fa-bowl-food"}
        ]
    },
    {
        "id": "maracuya",
        "file": "maracuya.html",
        "name_es": "Maracuyá (Passion Fruit)",
        "name_en": "Passion Fruit (Maracuyá)",
        "scientific": "Passiflora edulis f. flavicarpa (Maracuyá Amarillo)",
        "hero_class": "product-hero-maracuya",
        "img": "assets/images/maracuya.jpg?v=4",
        "badge_es": "Aroma Intenso • Rendimiento de Pulpa > 35%",
        "badge_en": "Intense Aroma • Pulp Yield > 35%",
        "tagline_es": "Fruta de la pasión de piel amarilla dorada y aroma seductor. Pulpa gelatinosa de exquisita acidez cítrica y azúcares naturales.",
        "tagline_en": "Golden passion fruit with alluring aroma, offering a sublime balance of lively acidity and natural sweetness.",
        "origin_es": "Manabí, Guayas y Esmeraldas, Ecuador",
        "origin_en": "Manabí, Guayas and Esmeraldas, Ecuador",
        "grade_es": "Categoría Extra / Primera • Piel lisa o ligeramente rugosa, peso completo",
        "grade_en": "Extra Category / First Class • Smooth to slightly wrinkled skin, heavy juice feel",
        "calibers_es": "Conteo por caja: 24, 28, 32, 36 unidades (Pesos 110g - 180g por fruta)",
        "calibers_en": "Counts per box: 24, 28, 32, 36 fruits (Weights 110g - 180g each)",
        "length_es": "Diámetro ecuatorial 6.0 a 8.5 cm",
        "length_en": "Equatorial diameter 6.0 to 8.5 cm",
        "pack_es": "Cajas de cartón corrugado de 2.0 kg o 4.0 kg neto",
        "pack_en": "Corrugated boxes 2.0 kg or 4.0 kg net",
        "temp_es": "+7.0°C a +10.0°C (45°F - 50°F)",
        "temp_en": "+7.0°C to +10.0°C (45°F - 50°F)",
        "vent_es": "Ventilación 15 cbm/h • Humedad relativa 85%",
        "vent_en": "Ventilation 15 cbm/h • Relative humidity 85%",
        "shelf_es": "25 a 30 días en transporte marítimo o aéreo",
        "shelf_en": "25 to 30 days maritime reefer or air shipping",
        "pallet_es": "180 a 240 cajas por pallet",
        "pallet_en": "180 to 240 boxes per pallet",
        "brix_es": "14.0° a 17.5° Grados Brix • Acidez cítrica 3.5% - 4.8%",
        "brix_en": "14.0° to 17.5° Brix • Citric acidity 3.5% - 4.8%",
        "certs_es": "GLOBALG.A.P., BPA Ecuador, Certificación Fitosanitaria AGROCALIDAD",
        "certs_en": "GLOBALG.A.P., Ecuador GAP, AGROCALIDAD Export Cert",
        "nutri": [
            {"title_es": "Mega Dosis Vitamina C", "title_en": "High Vitamin C", "desc_es": "Potente antioxidante que refuerza defensas y absorción de hierro.", "desc_en": "Potent antioxidant strengthening immune defenses.", "icon": "fa-lemon"},
            {"title_es": "Vitamina A & Carotenos", "title_en": "Vitamin A & Carotenes", "desc_es": "Favorece la salud visual y luminosidad de la piel.", "desc_en": "Protects eye health and enhances skin glow.", "icon": "fa-eye"},
            {"title_es": "Pasiflorina Relajante", "title_en": "Passiflorine Calm", "desc_es": "Alcaloide natural con efecto calmante para el estrés y descanso.", "desc_en": "Natural calming alkaloid supporting restful sleep.", "icon": "fa-moon"},
            {"title_es": "Fibra Soluble", "title_en": "Soluble Fiber", "desc_es": "Ayuda a reducir colesterol y equilibrar glucemia.", "desc_en": "Assists cholesterol reduction and blood balance.", "icon": "fa-heart-circle-check"}
        ]
    },
    {
        "id": "pina",
        "file": "pina.html",
        "name_es": "Piña Golden MD2 Extra Dulce",
        "name_en": "MD2 Extra Sweet Golden Pineapple",
        "scientific": "Ananas comosus var. MD2 (Golden Sweet)",
        "hero_class": "product-hero-pina",
        "img": "assets/images/pina.jpg?v=4",
        "badge_es": "Calidad Premium • Color Dorado Intenso",
        "badge_en": "Premium Quality • Intense Gold Color",
        "tagline_es": "Piña MD2 ecuatoriana con aroma embriagador, bajo nivel de acidez, pulpa amarillo oro ultra jugosa y corona verde fresca intacta.",
        "tagline_en": "Ecuadorian MD2 pineapple with mesmerizing aroma, low acidity, ultra-juicy golden flesh, and a fresh green crown.",
        "origin_es": "Provincias de Los Ríos, Guayas y Santo Domingo, Ecuador",
        "origin_en": "Provinces of Los Ríos, Guayas and Santo Domingo, Ecuador",
        "grade_es": "Grado Exportación Extra • Corona proporcional (1.0 a 1.5 veces el fruto)",
        "grade_en": "Extra Export Grade • Proportional crown (1.0 to 1.5x fruit length)",
        "calibers_es": "Calibres 5, 6, 7, 8, 9 y 10 frutas por caja (1.2 kg a 2.4 kg por fruta)",
        "calibers_en": "Calibers 5, 6, 7, 8, 9 and 10 fruits per box (1.2 kg to 2.4 kg each)",
        "length_es": "Color de cáscara: Grados de madurez C1, C2, C3 o C4 según pedido",
        "length_en": "Shell color: Maturity stages C1, C2, C3 or C4 as requested",
        "pack_es": "Cajas telescópicas de cartón corrugado de 11.5 kg o 12.0 kg neto",
        "pack_en": "Telescopic corrugated boxes 11.5 kg or 12.0 kg net",
        "temp_es": "+7.5°C a +8.5°C (46°F - 48°F) para maduración controlada",
        "temp_en": "+7.5°C to +8.5°C (46°F - 48°F) for controlled ripening",
        "vent_es": "Ventilación 15-20 cbm/h • Humedad relativa 85-90%",
        "vent_en": "Ventilation 15-20 cbm/h • Relative humidity 85-90%",
        "shelf_es": "28 a 35 días en frío continuo",
        "shelf_en": "28 to 35 days in continuous cold chain",
        "pallet_es": "70 a 80 cajas por pallet • 1,400 a 1,600 cajas por contenedor 40ft Reefer",
        "pallet_en": "70 to 80 boxes per pallet • 1,400 to 1,600 boxes per 40ft Reefer",
        "brix_es": "13.5° a 16.0° Grados Brix (Dulzura garantizada)",
        "brix_en": "13.5° to 16.0° Brix (Guaranteed sweetness)",
        "certs_es": "GLOBALG.A.P., Rainforest Alliance, BASC, AGROCALIDAD Fitosanitario",
        "certs_en": "GLOBALG.A.P., Rainforest Alliance, BASC, AGROCALIDAD Phytosanitary",
        "nutri": [
            {"title_es": "Bromelina Activa", "title_en": "Active Bromelain", "desc_es": "Enzima proteolítica antiinflamatoria que acelera la digestión.", "desc_en": "Proteolytic anti-inflammatory enzyme aiding protein digestion.", "icon": "fa-fire"},
            {"title_es": "Vitamina C Pura", "title_en": "Pure Vitamin C", "desc_es": "Más del 100% del requerimiento diario por porción.", "desc_en": "Over 100% daily value per single serving.", "icon": "fa-certificate"},
            {"title_es": "Manganeso Esencial", "title_en": "Essential Manganese", "desc_es": "Favorece la formación ósea y metabolismo de grasas.", "desc_en": "Supports bone density and fat metabolization.", "icon": "fa-bone"},
            {"title_es": "Hidratación Diurética", "title_en": "Diuretic Hydration", "desc_es": "Depura toxinas y reduce retención de líquidos.", "desc_en": "Flushes toxins and counters fluid retention.", "icon": "fa-water"}
        ]
    },
    {
        "id": "mango",
        "file": "mango.html",
        "name_es": "Mango de Exportación (Tommy & Kent)",
        "name_en": "Export Mango (Tommy Atkins & Kent)",
        "scientific": "Mangifera indica (Tommy Atkins, Kent, Keitt, Ataulfo)",
        "hero_class": "product-hero-mango",
        "img": "assets/images/mango.jpg?v=4",
        "badge_es": "Temporada: Octubre - Febrero • Tratamiento USDA",
        "badge_en": "Season: October - February • USDA Hot Water Treated",
        "tagline_es": "Mangos ecuatorianos de coloración roja y dorada vibrante, pulpa carnosa sin fibra y tratamiento hidrotérmico avalado por inspectores USDA-APHIS.",
        "tagline_en": "Vibrant red and golden Ecuadorian mangos with fiberless flesh, certified by USDA-APHIS hydrothermal hot-water treatment.",
        "origin_es": "Guayas (Daule, Pedro Carbo) y Santa Elena, Ecuador",
        "origin_en": "Guayas (Daule, Pedro Carbo) and Santa Elena, Ecuador",
        "grade_es": "Grado Exportación USDA / UE • Lavado, hidrotérmico y encerado",
        "grade_en": "USDA / EU Export Grade • Washed, hot water treated and waxed",
        "calibers_es": "Calibres 6, 7, 8, 9, 10, 12, 14 unidades por caja de 4 kg (280g - 650g por mango)",
        "calibers_en": "Calibers 6, 7, 8, 9, 10, 12, 14 units per 4 kg box (280g - 650g each)",
        "length_es": "Forma ovoide uniforme con blush rojo superior al 60% en Tommy Atkins",
        "length_en": "Uniform ovoid shape with >60% red blush on Tommy Atkins",
        "pack_es": "Cajas de cartón corrugado abiertas tipo bandeja de 4.0 kg (8.8 lb) neto",
        "pack_en": "Open-top tray corrugated boxes 4.0 kg (8.8 lb) net",
        "temp_es": "+10.0°C a +12.0°C (50°F - 54°F)",
        "temp_en": "+10.0°C to +12.0°C (50°F - 54°F)",
        "vent_es": "Ventilación 15-20 cbm/h • Humedad relativa 85-90%",
        "vent_en": "Ventilation 15-20 cbm/h • Relative humidity 85-90%",
        "shelf_es": "21 a 28 días en tránsito marítimo hacia EE.UU. y Europa",
        "shelf_en": "21 to 28 days transit to US and European ports",
        "pallet_es": "240 a 264 cajas por pallet • 4,800 a 5,280 cajas por contenedor 40ft Reefer",
        "pallet_en": "240 to 264 boxes per pallet • 4,800 to 5,280 boxes per 40ft Reefer",
        "brix_es": "12.5° a 15.0° Grados Brix al arribo de maduración",
        "brix_en": "12.5° to 15.0° Brix at ripening arrival",
        "certs_es": "GLOBALG.A.P., USDA-APHIS Fitosanitario, BASC, AGROCALIDAD",
        "certs_en": "GLOBALG.A.P., USDA-APHIS Phytosanitary, BASC, AGROCALIDAD",
        "nutri": [
            {"title_es": "Vitamina A & Beta-carotenos", "title_en": "Vitamin A & Carotenes", "desc_es": "Excelente para la visión y protección de la piel.", "desc_en": "Outstanding for vision protection and radiant skin.", "icon": "fa-sun"},
            {"title_es": "Antioxidante Mangiferina", "title_en": "Mangiferin Antioxidant", "desc_es": "Bioactivo natural que apoya la salud cardiovascular.", "desc_en": "Natural bioactive supporting cardio health.", "icon": "fa-heart-pulse"},
            {"title_es": "Vitamina C", "title_en": "Vitamin C", "desc_es": "Gran aporte inmunológico y síntesis de colágeno.", "desc_en": "Immune enhancer and natural collagen promoter.", "icon": "fa-shield"},
            {"title_es": "Fibra Suave", "title_en": "Soluble Fiber", "desc_es": "Digestión placentera sin pesadez estomacal.", "desc_en": "Pleasant digestion without stomach heaviness.", "icon": "fa-bowl-rice"}
        ]
    }
]

def render_page(p):
    # build nutrition cards
    nutri_html = ""
    for n in p["nutri"]:
        nutri_html += f"""
            <div class="nutrition-card">
              <div class="nutri-icon"><i class="fa-solid {n['icon']}"></i></div>
              <h4 data-i18n="{p['id']}_nutri_{n['icon']}_title">{n['title_es']}</h4>
              <p data-i18n="{p['id']}_nutri_{n['icon']}_desc">{n['desc_es']}</p>
            </div>"""

    # build other fruits navigation
    other_fruits_html = ""
    for o in products:
        if o["id"] != p["id"]:
            other_fruits_html += f"""
            <a href="{o['file']}" class="related-fruit-card">
              <img src="{o['img']}" alt="{o['name_es']}" loading="lazy">
              <div class="related-fruit-info">
                <h4>{o['name_es']}</h4>
                <span><i class="fa-solid fa-arrow-right"></i> Ver Catálogo</span>
              </div>
            </a>"""

    html = f"""<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{p['name_es']} | Exportación Global Market GM</title>
  <meta name="description" content="Exportación de {p['name_es']} desde Ecuador. {p['tagline_es']} Ficha técnica, calibres, empaque y cotización B2B.">
  <link rel="icon" type="image/png" href="assets/images/favicon.png?v=3">

  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Stylesheet -->
  <link rel="stylesheet" href="styles.css?v=4">
</head>
<body>

  <!-- TOP BAR -->
  <div class="top-bar">
    <div class="container top-bar-inner">
      <div class="top-bar-contact">
        <a href="tel:+593999999999"><i class="fa-solid fa-phone"></i> +593 99 999 9999</a>
        <a href="mailto:exports@globalmarket-gm.com"><i class="fa-solid fa-envelope"></i> exports@globalmarket-gm.com</a>
        <span class="top-bar-badge"><i class="fa-solid fa-shield-halved"></i> GlobalG.A.P. Certified</span>
      </div>
      <div class="top-bar-lang">
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
        <a href="index.html#inicio" class="nav-link" data-i18n="nav_home">Inicio</a>
        <a href="index.html#nosotros" class="nav-link" data-i18n="nav_about">Quiénes Somos</a>
        <a href="index.html#productos" class="nav-link active" data-i18n="nav_products">Productos</a>
        <a href="index.html#certificaciones" class="nav-link" data-i18n="nav_cert">Certificaciones</a>
        <a href="index.html#logistica" class="nav-link" data-i18n="nav_logistics">Logística</a>
        <a href="index.html#cotizador" class="nav-link nav-btn-cta" data-i18n="nav_quote">Cotizar Ahora</a>
      </nav>

      <button class="nav-mobile-toggle" id="mobileToggle" aria-label="Abrir Menú">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- PRODUCT HERO HEADER CON IMAGEN -->
  <section class="product-hero {p['hero_class']}">
    <div class="container">
      <div class="breadcrumbs">
        <a href="index.html">Inicio</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
        <a href="index.html#productos">Nuestros Productos</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
        <span>{p['name_es']}</span>
      </div>

      <div class="product-hero-content" style="max-width: 800px;">
        <span class="badge badge-gold" style="display: inline-block; margin-bottom: 0.75rem; padding: 0.35rem 0.85rem; border-radius: 99px;">
          <i class="fa-solid fa-award"></i> {p['badge_es']}
        </span>
        <h1 class="hero-title" style="font-size: 2.75rem; margin-bottom: 0.75rem;">{p['name_es']}</h1>
        <p style="font-size: 1.15rem; font-style: italic; color: var(--accent-gold); margin-bottom: 1rem;">{p['scientific']}</p>
        <p class="hero-desc" style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">{p['tagline_es']}</p>

        <div class="hero-cta-group">
          <a href="#cotizador-producto" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-file-invoice-dollar"></i> Cotizar {p['name_es']}
          </a>
          <a href="https://wa.me/593999999999?text=Hola%20Global%20Market,%20deseo%20cotizar%20{p['name_es']}%20para%20exportación." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
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
            <img src="{p['img']}" alt="{p['name_es']}" class="detail-main-img">
            <div style="padding: 1.25rem; background: #FFFFFF; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="color: var(--primary-dark); font-size: 1.05rem;"><i class="fa-solid fa-location-dot text-primary"></i> Origen:</strong>
                <span style="font-size: 0.95rem; color: var(--text-muted); margin-left: 0.35rem;">{p['origin_es']}</span>
              </div>
              <span class="product-badge badge-green" style="position: static;">100% Export Quality</span>
            </div>
          </div>

          <div style="margin-top: 2.5rem;">
            <div class="section-tag">Propiedades & Beneficios</div>
            <h3 style="font-size: 1.6rem; color: var(--text-main); margin-bottom: 0.5rem;">Valor Nutricional y Cualidades</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Cosechado en el punto óptimo de madurez para preservar todas sus propiedades organolépticas.</p>
            
            <div class="nutrition-grid">
              {nutri_html}
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
                  <td><strong>{p['scientific']}</strong></td>
                </tr>
                <tr>
                  <th>Grado Comercial:</th>
                  <td>{p['grade_es']}</td>
                </tr>
                <tr>
                  <th>Calibres / Tamaños:</th>
                  <td>{p['calibers_es']}</td>
                </tr>
                <tr>
                  <th>Longitud / Dimensiones:</th>
                  <td>{p['length_es']}</td>
                </tr>
                <tr>
                  <th>Dulzura / Sólidos Solubles:</th>
                  <td>{p['brix_es']}</td>
                </tr>
                <tr>
                  <th>Formato de Empaque:</th>
                  <td>{p['pack_es']}</td>
                </tr>
                <tr>
                  <th>Temperatura en Reefer:</th>
                  <td>{p['temp_es']}</td>
                </tr>
                <tr>
                  <th>Ventilación & Humedad:</th>
                  <td>{p['vent_es']}</td>
                </tr>
                <tr>
                  <th>Vida Útil en Tránsito:</th>
                  <td>{p['shelf_es']}</td>
                </tr>
                <tr>
                  <th>Paletizado & Capacidad:</th>
                  <td>{p['pallet_es']}</td>
                </tr>
                <tr>
                  <th>Certificaciones:</th>
                  <td><span style="color: var(--primary); font-weight: 600;">{p['certs_es']}</span></td>
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

  <!-- COTIZADOR DEDICADO PARA ESTE PRODUCTO -->
  <section class="section-padding" id="cotizador-producto" style="background: var(--bg-dark); color: #FFFFFF;">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-tag">Cotización Directa</div>
        <h2 class="section-title text-white">Solicitar Cotización de {p['name_es']}</h2>
        <p class="section-subtitle text-white-muted">
          Completa el formulario y recibe una propuesta FOB / CIF formal en menos de 24 horas.
        </p>
      </div>

      <div class="quote-form-card" style="max-width: 800px; margin: 0 auto;">
        <form id="productQuoteForm" class="quote-form">
          <input type="hidden" id="selectedProduct" value="{p['name_es']}">
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
                <option value="FOB Guayaquil / Posorja">FOB (Puerto Guayaquil / Posorja)</option>
                <option value="CIF Puerto de Destino">CIF (Costo, Seguro y Flete)</option>
                <option value="CFR Puerto de Destino">CFR (Costo y Flete)</option>
              </select>
            </div>
          </div>

          <div class="form-group" style="margin-top: 1rem;">
            <label for="pComments" class="form-label"><i class="fa-solid fa-comment-dots"></i> Especificaciones Particulares</label>
            <textarea id="pComments" rows="3" class="form-textarea" placeholder="Indicar calibres específicos, empaque de marca privada o semanas deseadas de embarque..."></textarea>
          </div>

          <div class="form-submit-row" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fa-solid fa-paper-plane"></i> Enviar Solicitud de Cotización
            </button>
            <a href="https://wa.me/593999999999?text=Hola%20Global%20Market,%20deseo%20cotizar%20{p['name_es']}%20para%20exportación." target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
              <i class="fa-brands fa-whatsapp"></i> Cotizar por WhatsApp
            </a>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- OTRAS FRUTAS DISPONIBLES EN EL CATÁLOGO -->
  <section class="other-fruits-strip">
    <div class="container">
      <div class="section-header text-center">
        <div class="section-tag">Explora Más</div>
        <h2 class="section-title">Otros Productos de Nuestro Catálogo de Exportación</h2>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        {other_fruits_html}
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="main-footer">
    <div class="container footer-container">
      <div class="footer-col brand-col">
        <div class="footer-logo">
          <img src="assets/images/logo.png?v=3" alt="Global Market Logo" class="footer-logo-img">
        </div>
        <p class="footer-desc">
          Exportación de frutas tropicales ecuatorianas de alta gama hacia América del Norte, Europa, Asia y Medio Oriente.
        </p>
        <div class="footer-socials">
          <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://wa.me/593999999999" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Nuestras Frutas</h4>
        <ul class="footer-links">
          <li><a href="banano.html">Banano Cavendish Premium</a></li>
          <li><a href="platano.html">Plátano Verde / Barraganete</a></li>
          <li><a href="pitahaya-roja.html">Pitahaya Roja (Red Dragon)</a></li>
          <li><a href="pitahaya-amarilla.html">Pitahaya Amarilla de Palora</a></li>
          <li><a href="malanga.html">Malanga / Taro (Blanca & Lila)</a></li>
          <li><a href="maracuya.html">Maracuyá (Passion Fruit)</a></li>
          <li><a href="pina.html">Piña Golden MD2</a></li>
          <li><a href="mango.html">Mango Tommy / Kent</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Navegación</h4>
        <ul class="footer-links">
          <li><a href="index.html#inicio">Inicio</a></li>
          <li><a href="index.html#nosotros">Quiénes Somos</a></li>
          <li><a href="index.html#certificaciones">Certificaciones de Calidad</a></li>
          <li><a href="index.html#logistica">Logística y Cadena de Frío</a></li>
          <li><a href="index.html#cotizador">Cotizador B2B</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Estándares</h4>
        <div class="footer-badges-list">
          <span class="f-badge">GlobalG.A.P.</span>
          <span class="f-badge">GRASP</span>
          <span class="f-badge">Rainforest</span>
          <span class="f-badge">BASC</span>
          <span class="f-badge">USDA Fitosanitario</span>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>&copy; <span id="year">2026</span> Global Market GM. Todos los derechos reservados.</p>
        <p class="footer-made-with">Exportando frescura de Ecuador para el mundo 🌍</p>
      </div>
    </div>
  </footer>

  <!-- FLOATING WHATSAPP BUTTON -->
  <a href="https://wa.me/593999999999?text=Hola%20Global%20Market,%20deseo%20cotizar%20{p['name_es']}%20para%20exportación." target="_blank" rel="noopener noreferrer" class="floating-wa" aria-label="Contactar por WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
    <span class="wa-tooltip">¡Chatea con un asesor de exportación!</span>
  </a>

  <!-- Script JS -->
  <script src="app.js?v=4"></script>
</body>
</html>
"""
    return html

# Write each page
for p in products:
    filepath = os.path.join("/Users/contabilidad/.gemini/antigravity-ide/scratch/GlobalMarket", p["file"])
    with open(filepath, "w", encoding="utf-8") as f:
        f.write(render_page(p))
    print(f"✓ Generada página: {p['file']}")
