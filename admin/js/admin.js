// GlobalMarket GM - Complete Admin Console Controller
document.addEventListener('DOMContentLoaded', () => {
  // State Initialization
  const rawInitial = window.INITIAL_DATA || {};
  let appState = {
    products: rawInitial.products || [],
    settings: rawInitial.settings || {},
    home: rawInitial.home || {},
    menu: rawInitial.menu || [],
    quotes: rawInitial.quotes || [],
    activeFruitId: 'banano'
  };

  let dragSrcIndex = null;

  // Toast Notification Helper
  function showToast(message, isError = false) {
    const toast = document.getElementById('adminToast');
    const toastMsg = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');

    toastMsg.textContent = message;
    if (isError) {
      toastIcon.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="color: #ef4444;"></i>';
      toast.style.borderColor = '#ef4444';
    } else {
      toastIcon.innerHTML = '<i class="fa-solid fa-circle-check" style="color: #fbbf24;"></i>';
      toast.style.borderColor = '#d97706';
    }

    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  }

  // =========================================================================
  // 1. RENDER ACTIVE FRUIT, SPECIAL IMAGES & DRAGGABLE GALLERY
  // =========================================================================
  function renderActiveFruit(fruitId) {
    const fruit = appState.products.find(p => p.id === fruitId);
    if (!fruit) return;

    appState.activeFruitId = fruitId;

    // Header & Meta
    document.getElementById('currentFruitTitle').textContent = fruit.name_es;
    document.getElementById('currentFruitScientific').textContent = fruit.scientific;
    document.getElementById('btnPreviewFruit').href = `../${fruit.file}`;
    document.getElementById('specFruitId').value = fruit.id;

    // Special images previews
    const cleanHeroBg = (fruit.hero_bg || fruit.img || '').split('?')[0];
    const cleanMainImg = (fruit.img || '').split('?')[0];
    document.getElementById('previewHeroBg').src = `../${cleanHeroBg}?v=${Date.now()}`;
    document.getElementById('previewMainImg').src = `../${cleanMainImg}?v=${Date.now()}`;

    // Photo count
    const photoCount = (fruit.gallery || []).length;
    document.getElementById('activePhotoCount').textContent = photoCount;
    
    const countBadge = document.getElementById(`countBadge_${fruit.id}`);
    if (countBadge) {
      countBadge.innerHTML = `<i class="fa-solid fa-images"></i> ${photoCount} fotos`;
    }

    // Update active state on fruit cards
    document.querySelectorAll('.fruit-card').forEach(card => {
      card.classList.toggle('active', card.getAttribute('data-fruit-id') === fruitId);
    });

    // Render Gallery
    renderGalleryGrid(fruit);

    // Populate Specs Form
    document.getElementById('specNameEs').value = fruit.name_es || '';
    document.getElementById('specScientific').value = fruit.scientific || '';
    document.getElementById('specBadge').value = fruit.badge_es || '';
    document.getElementById('specOrigin').value = fruit.origin_es || '';
    document.getElementById('specTagline').value = fruit.tagline_es || '';
    document.getElementById('specGrade').value = fruit.grade_es || '';
    document.getElementById('specCalibers').value = fruit.calibers_es || '';
    document.getElementById('specBrix').value = fruit.brix_es || '';
    document.getElementById('specPack').value = fruit.pack_es || '';
    document.getElementById('specTemp').value = fruit.temp_es || '';
    document.getElementById('specShelf').value = fruit.shelf_es || '';
    document.getElementById('specPallet').value = fruit.pallet_es || '';
    document.getElementById('specCerts').value = fruit.certs_es || '';
  }

  function renderGalleryGrid(fruit) {
    const grid = document.getElementById('adminGalleryGrid');
    grid.innerHTML = '';

    const gallery = fruit.gallery || [];
    if (gallery.length === 0) {
      grid.innerHTML = '<div style="grid-column: 1/-1; padding: 3rem 1rem; text-align: center; color: var(--text-muted);"><i class="fa-regular fa-image" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>No hay fotos en esta galería todavía. Sube fotografías arrastrándolas arriba o con el botón "Subir Nueva Fotografía".</div>';
      return;
    }

    const cleanMainImg = (fruit.img || '').split('?')[0];
    const cleanHeroBg = (fruit.hero_bg || '').split('?')[0];

    gallery.forEach((imgUrl, index) => {
      const cleanUrl = imgUrl.split('?')[0];
      const isMain = cleanUrl === cleanMainImg;
      const isHero = cleanUrl === cleanHeroBg;
      
      // Compute 1:1 square thumb URL
      let sqUrl = cleanUrl;
      if (!cleanUrl.includes('-sq.')) {
        const lastDot = cleanUrl.lastIndexOf('.');
        if (lastDot !== -1) {
          sqUrl = cleanUrl.substring(0, lastDot) + '-sq.jpg';
        }
      }

      const card = document.createElement('div');
      card.className = 'admin-photo-card';
      card.setAttribute('draggable', 'true');
      card.setAttribute('data-index', index);
      card.setAttribute('data-url', cleanUrl);

      card.innerHTML = `
        <span class="photo-order-badge">#${index + 1}</span>
        ${isMain ? '<span class="photo-badge-main"><i class="fa-solid fa-star"></i> Principal</span>' : ''}
        ${isHero ? '<span class="photo-badge-main" style="top: 36px; background: #0d5c3a;"><i class="fa-solid fa-panorama"></i> Cabecera</span>' : ''}
        <img src="../${sqUrl}?v=${Date.now()}" alt="${fruit.name_es}" onerror="this.src='../${cleanUrl}'">
        <div class="photo-actions-overlay">
          <div class="action-row">
            ${index > 0 ? `<button type="button" class="btn-photo-action btn-photo-move btn-move-left" title="Mover hacia la izquierda" data-index="${index}"><i class="fa-solid fa-chevron-left"></i></button>` : ''}
            <a href="../${cleanUrl}" target="_blank" class="btn-photo-action" style="background: rgba(255,255,255,0.2); color: #fff;" title="Ver en alta resolución">
              <i class="fa-solid fa-up-right-and-down-left-and-up-right-to-center"></i>
            </a>
            ${index < gallery.length - 1 ? `<button type="button" class="btn-photo-action btn-photo-move btn-move-right" title="Mover hacia la derecha" data-index="${index}"><i class="fa-solid fa-chevron-right"></i></button>` : ''}
          </div>
          <div class="action-row" style="margin-top: 0.35rem;">
            ${!isMain ? `
              <button type="button" class="btn-photo-action btn-photo-main" title="Fijar como Foto Principal de Referencia" data-url="${cleanUrl}">
                <i class="fa-solid fa-star"></i>
              </button>
            ` : ''}
            ${!isHero ? `
              <button type="button" class="btn-photo-action btn-photo-hero" title="Fijar como Imagen de Cabecera (Hero Banner)" data-url="${cleanUrl}">
                <i class="fa-solid fa-panorama"></i>
              </button>
            ` : ''}
            <button type="button" class="btn-photo-action btn-photo-delete" title="Eliminar Foto" data-index="${index}" data-url="${cleanUrl}">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </div>
      `;

      // Drag and drop event listeners
      card.addEventListener('dragstart', (e) => {
        dragSrcIndex = index;
        card.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', index);
      });

      card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        document.querySelectorAll('.admin-photo-card').forEach(c => c.classList.remove('drag-over'));
      });

      card.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
      });

      card.addEventListener('dragenter', () => {
        if (dragSrcIndex !== index) {
          card.classList.add('drag-over');
        }
      });

      card.addEventListener('dragleave', () => {
        card.classList.remove('drag-over');
      });

      card.addEventListener('drop', (e) => {
        e.preventDefault();
        card.classList.remove('drag-over');
        const srcIdx = dragSrcIndex;
        const targetIdx = index;

        if (srcIdx !== null && srcIdx !== targetIdx) {
          const item = fruit.gallery.splice(srcIdx, 1)[0];
          fruit.gallery.splice(targetIdx, 0, item);
          renderActiveFruit(fruit.id);
          saveGalleryOrder(fruit);
        }
      });

      // Left / Right Move Buttons
      const btnLeft = card.querySelector('.btn-move-left');
      if (btnLeft) {
        btnLeft.addEventListener('click', (e) => {
          e.stopPropagation();
          const item = fruit.gallery.splice(index, 1)[0];
          fruit.gallery.splice(index - 1, 0, item);
          renderActiveFruit(fruit.id);
          saveGalleryOrder(fruit);
        });
      }

      const btnRight = card.querySelector('.btn-move-right');
      if (btnRight) {
        btnRight.addEventListener('click', (e) => {
          e.stopPropagation();
          const item = fruit.gallery.splice(index, 1)[0];
          fruit.gallery.splice(index + 1, 0, item);
          renderActiveFruit(fruit.id);
          saveGalleryOrder(fruit);
        });
      }

      // Set as Main Reference Photo Action
      const btnMain = card.querySelector('.btn-photo-main');
      if (btnMain) {
        btnMain.addEventListener('click', async (e) => {
          e.stopPropagation();
          try {
            const res = await fetch('api.php?action=set_main_image', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ product_id: fruit.id, image_url: cleanUrl })
            });
            const result = await res.json();
            if (result.success) {
              fruit.img = cleanUrl;
              renderActiveFruit(fruit.id);
              showToast('Foto fijada como Principal de Referencia');
            }
          } catch (e) {
            showToast('Error al actualizar foto principal', true);
          }
        });
      }

      // Set as Hero Background Action
      const btnHero = card.querySelector('.btn-photo-hero');
      if (btnHero) {
        btnHero.addEventListener('click', async (e) => {
          e.stopPropagation();
          try {
            const res = await fetch('api.php?action=set_hero_bg', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ product_id: fruit.id, image_url: cleanUrl })
            });
            const result = await res.json();
            if (result.success) {
              fruit.hero_bg = cleanUrl;
              renderActiveFruit(fruit.id);
              showToast('Imagen fijada como Fondo de Cabecera (Hero Banner)');
            }
          } catch (e) {
            showToast('Error al actualizar cabecera', true);
          }
        });
      }

      // Delete Photo Action
      const btnDel = card.querySelector('.btn-photo-delete');
      if (btnDel) {
        btnDel.addEventListener('click', async (e) => {
          e.stopPropagation();
          if (!confirm('¿Seguro que deseas eliminar esta fotografía de la galería?')) return;
          try {
            const res = await fetch('api.php?action=delete_image', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ product_id: fruit.id, index: index, image_url: cleanUrl })
            });
            const result = await res.json();
            if (result.success) {
              fruit.gallery = result.gallery;
              renderActiveFruit(fruit.id);
              showToast('Fotografía eliminada con éxito');
            } else {
              showToast(result.error || 'Error al eliminar', true);
            }
          } catch (e) {
            showToast('Error de conexión al eliminar foto', true);
          }
        });
      }

      grid.appendChild(card);
    });
  }

  // Save Gallery Order to Server
  async function saveGalleryOrder(fruit) {
    try {
      const res = await fetch('api.php?action=reorder_gallery', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: fruit.id, gallery: fruit.gallery })
      });
      const result = await res.json();
      if (result.success) {
        showToast('Ubicación y orden de galería guardado en la web');
      } else {
        showToast('Error al guardar orden', true);
      }
    } catch (e) {
      showToast('Error de conexión al guardar orden', true);
    }
  }

  // Bind Fruit Selector Click Events
  document.querySelectorAll('.fruit-card').forEach(card => {
    card.addEventListener('click', () => {
      const fId = card.getAttribute('data-fruit-id');
      renderActiveFruit(fId);
    });
  });

  // =========================================================================
  // 2. HERO BG & MAIN REFERENCE DIRECT UPLOADERS
  // =========================================================================
  const btnUploadHeroBg = document.getElementById('btnUploadHeroBg');
  const heroBgFileInput = document.getElementById('heroBgFileInput');
  const btnUploadMainImg = document.getElementById('btnUploadMainImg');
  const mainImgFileInput = document.getElementById('mainImgFileInput');

  if (btnUploadHeroBg && heroBgFileInput) {
    btnUploadHeroBg.addEventListener('click', () => heroBgFileInput.click());
    heroBgFileInput.addEventListener('change', async () => {
      if (heroBgFileInput.files.length > 0) {
        await handleSpecialUpload(heroBgFileInput.files[0], 'hero_bg');
        heroBgFileInput.value = '';
      }
    });
  }

  if (btnUploadMainImg && mainImgFileInput) {
    btnUploadMainImg.addEventListener('click', () => mainImgFileInput.click());
    mainImgFileInput.addEventListener('change', async () => {
      if (mainImgFileInput.files.length > 0) {
        await handleSpecialUpload(mainImgFileInput.files[0], 'reference_img');
        mainImgFileInput.value = '';
      }
    });
  }

  async function handleSpecialUpload(file, type) {
    const formData = new FormData();
    formData.append('product_id', appState.activeFruitId);
    formData.append('type', type);
    formData.append('image', file);

    showToast('Subiendo y actualizando imagen...', false);

    try {
      const res = await fetch('api.php?action=upload_special_image', {
        method: 'POST',
        body: formData
      });
      const result = await res.json();
      if (result.success) {
        const fruit = appState.products.find(p => p.id === appState.activeFruitId);
        if (fruit) {
          if (type === 'hero_bg') {
            fruit.hero_bg = result.image_url;
          } else {
            fruit.img = result.image_url;
          }
          renderActiveFruit(fruit.id);
        }
        showToast(type === 'hero_bg' ? 'Imagen de cabecera actualizada con éxito' : 'Foto principal de referencia actualizada con éxito');
      } else {
        showToast(result.error || 'Error al subir', true);
      }
    } catch (e) {
      showToast('Error de conexión al subir imagen especial', true);
    }
  }

  // =========================================================================
  // 3. TABS NAVIGATION (Sidebar & Sub-Tabs)
  // =========================================================================
  document.querySelectorAll('.sidebar-nav .nav-item').forEach(btn => {
    btn.addEventListener('click', () => {
      const tabId = btn.getAttribute('data-tab');
      document.querySelectorAll('.sidebar-nav .nav-item').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

      btn.classList.add('active');
      const targetPane = document.getElementById(`tab-${tabId}`);
      if (targetPane) targetPane.classList.add('active');

      if (tabId === 'quotes') {
        loadQuotes();
      } else if (tabId === 'menu') {
        renderMenuEditor();
      }
    });
  });

  document.querySelectorAll('.subtab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const subtabId = btn.getAttribute('data-subtab');
      document.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.subtab-content').forEach(c => c.classList.remove('active'));

      btn.classList.add('active');
      const targetSub = document.getElementById(`subtab-${subtabId}`);
      if (targetSub) targetSub.classList.add('active');
    });
  });

  // =========================================================================
  // 4. GALLERY FILE UPLOAD (Drag & Drop + Button)
  // =========================================================================
  const dropzone = document.getElementById('uploadDropzone');
  const fileInput = document.getElementById('fileInput');
  const btnSelectFiles = document.getElementById('btnSelectFiles');
  const uploadProgress = document.getElementById('uploadProgress');

  if (btnSelectFiles) {
    btnSelectFiles.addEventListener('click', (e) => {
      e.stopPropagation();
      fileInput.click();
    });
  }

  ['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, (e) => {
      e.preventDefault();
      dropzone.classList.remove('dragover');
    });
  });

  dropzone.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    if (files.length > 0) {
      handleFileUpload(files[0]);
    }
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      handleFileUpload(fileInput.files[0]);
      fileInput.value = '';
    }
  });

  async function handleFileUpload(file) {
    uploadProgress.style.display = 'flex';
    const formData = new FormData();
    formData.append('product_id', appState.activeFruitId);
    formData.append('image', file);

    try {
      const res = await fetch('api.php?action=upload_image', {
        method: 'POST',
        body: formData
      });
      const result = await res.json();
      uploadProgress.style.display = 'none';

      if (result.success) {
        const fruit = appState.products.find(p => p.id === appState.activeFruitId);
        if (fruit) {
          fruit.gallery = result.gallery;
          renderActiveFruit(fruit.id);
        }
        showToast('Foto subida, recortada 1:1 y agregada a la galería');
      } else {
        showToast(result.error || 'Error al subir fotografía', true);
      }
    } catch (e) {
      uploadProgress.style.display = 'none';
      showToast('Error de conexión al subir fotografía', true);
    }
  }

  // =========================================================================
  // 5. PRODUCT SPECS FORM SUBMISSION
  // =========================================================================
  document.getElementById('productSpecsForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
      const res = await fetch('api.php?action=save_product_text', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (result.success) {
        const fruit = appState.products.find(p => p.id === data.product_id);
        if (fruit) {
          Object.assign(fruit, data);
          renderActiveFruit(fruit.id);
        }
        showToast('Ficha técnica guardada y publicada en la web');
      } else {
        showToast(result.error || 'Error al guardar', true);
      }
    } catch (err) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 6. HOME PAGE EDITOR SUBMISSION
  // =========================================================================
  document.getElementById('homeEditorForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const homeData = {
      hero: {
        badge: document.getElementById('heroBadge').value.trim(),
        title: document.getElementById('heroTitle').value.trim(),
        desc: document.getElementById('heroDesc').value.trim(),
        btn_explore: 'Explorar Catálogo',
        btn_quote: 'Cotizador B2B',
        btn_whatsapp: 'WhatsApp Directo',
        bg_image: appState.home.hero?.bg_image || 'assets/images/hero-banner.jpg',
        stats: [
          { num: document.getElementById('statNum_0').value.trim(), label: document.getElementById('statLbl_0').value.trim() },
          { num: document.getElementById('statNum_1').value.trim(), label: document.getElementById('statLbl_1').value.trim() },
          { num: document.getElementById('statNum_2').value.trim(), label: document.getElementById('statLbl_2').value.trim() },
          { num: document.getElementById('statNum_3').value.trim(), label: document.getElementById('statLbl_3').value.trim() }
        ]
      },
      about: {
        tag: document.getElementById('aboutTag').value.trim(),
        title: document.getElementById('aboutTitle').value.trim(),
        p1: document.getElementById('aboutP1').value.trim(),
        p2: document.getElementById('aboutP2').value.trim(),
        badge_title: document.getElementById('aboutBadgeTitle').value.trim(),
        badge_sub: document.getElementById('aboutBadgeSub').value.trim(),
        image: appState.home.about?.image || 'assets/images/hero-banner.jpg'
      },
      certs: {
        tag: document.getElementById('certsTag').value.trim(),
        title: document.getElementById('certsTitle').value.trim(),
        desc: document.getElementById('certsDesc').value.trim(),
        items: appState.home.certs?.items || []
      },
      logistics: {
        tag: document.getElementById('logisticsTag').value.trim(),
        title: document.getElementById('logisticsTitle').value.trim(),
        desc: document.getElementById('logisticsDesc').value.trim(),
        image: appState.home.logistics?.image || 'assets/images/logistica.jpg',
        steps: appState.home.logistics?.steps || []
      },
      footer: {
        about_text: document.getElementById('footerAbout').value.trim(),
        copyright: document.getElementById('footerCopyright').value.trim()
      }
    };

    try {
      const res = await fetch('api.php?action=save_home_content', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(homeData)
      });
      const result = await res.json();
      if (result.success) {
        appState.home = homeData;
        showToast('Página de Inicio actualizada y publicada');
      } else {
        showToast(result.error || 'Error al guardar portada', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 7. NAVIGATION MENU MANAGER
  // =========================================================================
  function renderMenuEditor() {
    const list = document.getElementById('menuItemsList');
    list.innerHTML = '';

    appState.menu.forEach((item, index) => {
      const isChecked = item.visible !== false ? 'checked' : '';
      const row = document.createElement('div');
      row.className = 'menu-item-row';
      row.style.background = 'rgba(0,0,0,0.3)';
      row.style.border = '1px solid var(--admin-card-border)';
      row.style.borderRadius = 'var(--radius-sm)';
      row.style.padding = '1.25rem';
      row.style.marginBottom = '1rem';

      let submenuHtml = '';
      if (item.has_submenu && item.submenu && item.submenu.length > 0) {
        submenuHtml = `<div style="margin-top: 1rem; padding-left: 1.5rem; border-left: 2px solid var(--gold-accent);">
          <h5 style="color: var(--gold-light); margin-bottom: 0.75rem;"><i class="fa-solid fa-list-ul"></i> Submenú de Frutas (${item.submenu.length} Frutas):</h5>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
            ${item.submenu.map((sub, sIdx) => `
              <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.03); padding: 0.5rem 0.75rem; border-radius: 6px;">
                <input type="checkbox" id="sub_vis_${index}_${sIdx}" ${sub.visible !== false ? 'checked' : ''} data-item-idx="${index}" data-sub-idx="${sIdx}" class="sub-menu-vis">
                <input type="text" id="sub_lbl_${index}_${sIdx}" value="${sub.label}" class="form-control form-control-sm" style="flex: 1;" data-item-idx="${index}" data-sub-idx="${sIdx}">
              </div>
            `).join('')}
          </div>
        </div>`;
      }

      row.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
          <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
            <label class="switch-pill" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff;">
              <input type="checkbox" class="menu-vis-check" data-index="${index}" ${isChecked}>
              <span>${isChecked ? 'Visible' : 'Oculto'}</span>
            </label>
            <input type="text" class="form-control menu-lbl-input" data-index="${index}" value="${item.label}" style="max-width: 260px;">
            <input type="text" class="form-control menu-url-input" data-index="${index}" value="${item.url}" style="flex: 1; color: var(--text-muted);">
          </div>
        </div>
        ${submenuHtml}
      `;

      list.appendChild(row);
    });
  }

  document.getElementById('menuEditorForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const updatedMenu = JSON.parse(JSON.stringify(appState.menu));

    document.querySelectorAll('.menu-vis-check').forEach(chk => {
      const idx = parseInt(chk.getAttribute('data-index'));
      updatedMenu[idx].visible = chk.checked;
    });

    document.querySelectorAll('.menu-lbl-input').forEach(inp => {
      const idx = parseInt(inp.getAttribute('data-index'));
      updatedMenu[idx].label = inp.value.trim();
    });

    document.querySelectorAll('.menu-url-input').forEach(inp => {
      const idx = parseInt(inp.getAttribute('data-index'));
      updatedMenu[idx].url = inp.value.trim();
    });

    // Submenus
    document.querySelectorAll('.sub-menu-vis').forEach(chk => {
      const iIdx = parseInt(chk.getAttribute('data-item-idx'));
      const sIdx = parseInt(chk.getAttribute('data-sub-idx'));
      if (updatedMenu[iIdx] && updatedMenu[iIdx].submenu && updatedMenu[iIdx].submenu[sIdx]) {
        updatedMenu[iIdx].submenu[sIdx].visible = chk.checked;
      }
    });

    document.querySelectorAll('[id^="sub_lbl_"]').forEach(inp => {
      const iIdx = parseInt(inp.getAttribute('data-item-idx'));
      const sIdx = parseInt(inp.getAttribute('data-sub-idx'));
      if (updatedMenu[iIdx] && updatedMenu[iIdx].submenu && updatedMenu[iIdx].submenu[sIdx]) {
        updatedMenu[iIdx].submenu[sIdx].label = inp.value.trim();
      }
    });

    try {
      const res = await fetch('api.php?action=save_menu', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedMenu)
      });
      const result = await res.json();
      if (result.success) {
        appState.menu = updatedMenu;
        showToast('Menú de navegación actualizado en toda la web');
      } else {
        showToast(result.error || 'Error al guardar menú', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 8. GLOBAL SITE SETTINGS SUBMISSION
  // =========================================================================
  document.getElementById('siteSettingsForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = {
      phone: formData.get('phone'),
      whatsapp: formData.get('whatsapp'),
      email: formData.get('email'),
      address: formData.get('address'),
      certs_badge: formData.get('certs_badge'),
      social: {
        facebook: formData.get('social_fb'),
        instagram: formData.get('social_ig'),
        linkedin: formData.get('social_li')
      }
    };

    try {
      const res = await fetch('api.php?action=save_site_settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (result.success) {
        appState.settings = data;
        showToast('Ajustes generales actualizados en toda la web');
      } else {
        showToast(result.error || 'Error al guardar ajustes', true);
      }
    } catch (err) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 9. QUOTES INBOX & SECURITY
  // =========================================================================
  async function loadQuotes() {
    const tbody = document.getElementById('quotesTableBody');
    const emptyState = document.getElementById('quotesEmptyState');
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding: 2rem;"><i class="fa-solid fa-spinner fa-spin"></i> Cargando cotizaciones...</td></tr>';

    try {
      const res = await fetch('api.php?action=get_quotes');
      const data = await res.json();
      if (data.success) {
        appState.quotes = data.quotes;
        const countBadge = document.getElementById('quotesCountBadge');
        if (countBadge) {
          countBadge.textContent = data.quotes.length;
          countBadge.style.display = data.quotes.length > 0 ? 'inline-block' : 'none';
        }

        if (data.quotes.length === 0) {
          tbody.innerHTML = '';
          emptyState.style.display = 'block';
          return;
        }

        emptyState.style.display = 'none';
        tbody.innerHTML = '';

        data.quotes.forEach(q => {
          const tr = document.createElement('tr');
          const cleanPhone = (q.phone || '').replace(/[^0-9]/g, '');
          const waLink = cleanPhone ? `https://wa.me/${cleanPhone}?text=Hola%20${encodeURIComponent(q.client_name)},%20recibimos%20tu%20solicitud%20de%20cotización%20en%20GlobalMarket%20GM.` : '#';
          
          tr.innerHTML = `
            <td><small class="text-muted">${q.date || 'Reciente'}</small></td>
            <td><strong>${q.client_name || 'Anónimo'}</strong></td>
            <td><span class="badge-cms" style="background:#0d5c3a;">${q.product || 'General'}</span></td>
            <td>${q.destination || 'N/A'}</td>
            <td>${q.volume || 'N/A'} (${q.incoterm || 'CIF'})</td>
            <td>
              <div><i class="fa-solid fa-envelope text-muted"></i> <a href="mailto:${q.email}" style="color:#fbbf24; text-decoration:none;">${q.email}</a></div>
              ${q.phone ? `<div><i class="fa-brands fa-whatsapp text-muted"></i> <a href="${waLink}" target="_blank" style="color:#10b981; text-decoration:none;">${q.phone}</a></div>` : ''}
            </td>
            <td>
              <div style="display:flex; gap:0.4rem;">
                ${cleanPhone ? `<a href="${waLink}" target="_blank" class="btn btn-sm btn-primary" title="Contactar por WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>` : ''}
                <button type="button" class="btn btn-sm btn-danger btn-delete-quote" data-id="${q.id}" title="Eliminar cotización"><i class="fa-solid fa-trash-can"></i></button>
              </div>
            </td>
          `;

          const btnDelQuote = tr.querySelector('.btn-delete-quote');
          if (btnDelQuote) {
            btnDelQuote.addEventListener('click', async () => {
              if (!confirm('¿Eliminar esta solicitud de cotización?')) return;
              await fetch('api.php?action=delete_quote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: q.id })
              });
              loadQuotes();
              showToast('Cotización eliminada');
            });
          }

          tbody.appendChild(tr);
        });
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:#ef4444;">Error al cargar cotizaciones</td></tr>';
    }
  }

  document.getElementById('btnRefreshQuotes').addEventListener('click', loadQuotes);

  // Change Password
  document.getElementById('changePasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
      showToast('La nueva contraseña y su confirmación no coinciden', true);
      return;
    }

    try {
      const res = await fetch('api.php?action=change_password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ current_password: currentPassword, new_password: newPassword })
      });
      const result = await res.json();
      if (result.success) {
        showToast('Contraseña actualizada correctamente');
        e.target.reset();
      } else {
        showToast(result.error || 'Error al cambiar contraseña', true);
      }
    } catch (err) {
      showToast('Error de conexión', true);
    }
  });

  // Rebuild All Site
  document.getElementById('btnRebuildAll').addEventListener('click', async () => {
    const btn = document.getElementById('btnRebuildAll');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publicando...';

    try {
      const res = await fetch('api.php?action=rebuild_all');
      const result = await res.json();
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Publicar Cambios';

      if (result.success) {
        showToast(result.message || 'Sitio web publicado y sincronizado');
      } else {
        showToast('Error al publicar cambios', true);
      }
    } catch (e) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Publicar Cambios';
      showToast('Error de conexión al publicar', true);
    }
  });

  // =========================================================================
  // 10. INITIAL RENDER
  // =========================================================================
  renderActiveFruit(appState.activeFruitId);
});
