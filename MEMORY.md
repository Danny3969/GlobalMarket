# 🧠 MEMORIA DEL PROYECTO: GLOBAL MARKET GM

> **Documento de Continuidad Operativa y Técnica**  
> Última Actualización: 31 de Agosto de 2026  
> Repositorio GitHub Oficial: [https://github.com/Danny3969/GlobalMarket](https://github.com/Danny3969/GlobalMarket)  
> Sitio Web en Producción: [https://globalmarket-gm.com](https://globalmarket-gm.com)

---

## 1. 📌 Resumen Ejecutivo del Proyecto
**Global Market GM** es una plataforma web corporativa bilingüe (Español / Inglés) de clase mundial diseñada para la exportación mayorista internacional de frutas tropicales frescas desde Ecuador y Latinoamérica hacia Norteamérica, Europa, Asia y Medio Oriente.

### Frutas Exportadas y Catálogo:
1. **Banano Cavendish Premium:** Calibre 39-46, empaques 18.14 kg / 40 lb, suministro continuo todo el año.
2. **Mango de Exportación (Tommy Atkins & Kent):** Tratamiento hidrotérmico USDA-APHIS, 12°-15° Brix, calibres 6 al 14.
3. **Pitahaya Roja (Red Dragon Fruit - *Hylocereus monacanthus*):** Superfruta magenta rica en antioxidantes, transporte aéreo y marítimo.
4. **Pitahaya Amarilla de Palora (*Selenicereus megalanthus*):** Denominación de origen Palora (Amazonía), 16°-19° Brix (la más dulce del mundo).

---

## 2. 🌐 Configuración de Hosting, Dominios y Servidor

* **Proveedor / IP del Servidor:** `23.145.120.19` (cPanel / Apache / LiteSpeed)
* **Usuario cPanel / FTP:** `jumofres`
* **Contraseña cPanel / FTP:** `c0KrKa7d&Fjo4EN;1`
* **Panel de Control:** `https://23.145.120.19:2083/` o `https://www.jumofresh.com:2083/`
* **Directorio de Producción:** `/globalmarket-gm.com/` (Raíz donde está activa la web)
* **Dominio Principal Activo:** `https://globalmarket-gm.com` (`HTTP/2 200 OK`)
* **Dominio Secundario Redirigido:** `https://www.jumofresh.com` redirige con regla `301 Permanente` hacia `https://globalmarket-gm.com/` a través de `/public_html/.htaccess`.

---

## 3. 📂 Estructura del Código y Archivos del Proyecto

```text
GlobalMarket/
├── assets/
│   └── images/
│       ├── logo.png                # Logotipo Oficial de Global Market GM (Vector/HD)
│       ├── hero-banner.jpg         # Banner principal: Plantación y canastas de frutas al amanecer
│       ├── banano.jpg              # Fotografía comercial Banano Cavendish Premium
│       ├── mango.jpg               # Fotografía comercial Mango Tommy/Kent
│       ├── pitahaya-roja.jpg       # Fotografía comercial Pitahaya Roja abierta y entera
│       ├── pitahaya-amarilla.jpg   # Fotografía comercial Pitahaya Amarilla de Palora
│       └── logistica.jpg           # Fotografía de contenedor reefer y barco portacontenedores
├── index.html                      # Estructura semántica HTML5, metatags SEO y accesibilidad
├── styles.css                      # Sistema de diseño CSS moderno (Glassmorphism, Verde Esmeralda/Oro)
├── app.js                          # Motor bilingüe ES/EN, modal de fichas técnicas, cotizador RFQ y WhatsApp
├── .htaccess                       # Configuración Apache: HTTPS forzado, compresión GZIP y caché 1 año
├── deploy.py                       # Script Python para desplegar cambios automáticamente por FTP
├── README.md                       # Resumen público del repositorio
└── MEMORY.md                       # Memoria técnica completa para sincronización entre dispositivos
```

---

## 4. ⚙️ Características Técnicas y Módulos Desarrollados

1. **Motor Bilingüe Reactivo (ES/EN):**
   * Diccionario dinámico en `app.js` que traduce instantáneamente toda la interfaz, fichas técnicas, mensajes y botones sin recargar la página.
2. **Modal Interactivo de Fichas Técnicas:**
   * Detalla calibres, especie botánica, temperatura de transporte en contenedor reefer (°C), empaque y vida en tránsito.
3. **Cotizador y Formulario RFQ B2B:**
   * Permite a compradores seleccionar fruta, volumen de contenedores FCL / carga aérea, puerto de destino e Incoterms (FOB, CIF, CFR).
   * Genera el mensaje formateado y lo envía directamente por correo o **WhatsApp Web/Móvil**.
4. **Optimización de Velocidad y SEO:**
   * Compresión `mod_deflate` activa.
   * `ExpiresDefault` configurado para caché de 1 año en imágenes y 1 mes en estilos y scripts.
   * Puntuación de carga casi instantánea (< 0.5s) al no depender de plugins pesados de WordPress.

---

## 5. 🚀 Cómo Continuar o Modificar el Proyecto desde Otra Computadora

Si te conectas desde otro equipo o abres una nueva sesión:

### Paso 1: Clonar el Repositorio
```bash
git clone https://github.com/Danny3969/GlobalMarket.git
cd GlobalMarket
```

### Paso 2: Editar los Archivos
* **Textos o Traducciones:** Modificar `index.html` o el diccionario en `app.js`.
* **Estilos y Colores:** Modificar variables CSS en `styles.css`.
* **Nuevas Frutas o Imágenes:** Agregar las fotos en `assets/images/` y vincularlas en `index.html`.

### Paso 3: Desplegar los Cambios al Servidor
Simplemente ejecuta:
```bash
python3 deploy.py
```
*(El script se conectará automáticamente al hosting vía FTP y actualizará únicamente los archivos modificados).*

### Paso 4: Guardar y Subir a GitHub
```bash
git add .
git commit -m "feat: Actualización de contenidos"
git push origin main
```
