// GlobalMarket GM - Admin Console Logic
document.addEventListener('DOMContentLoaded', () => {
  let appState = {
    products: [],
    settings: {},
    activeFruitId: 'banano',
    quotes: []
  };

  // Toast Helper
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

  // Load Initial Data
  async function loadInitialData() {
    try {
      const res = await fetch('api.php?action=get_data');
      const data = await res.json();
      if (data.success) {
        appState.products = data.products;
        appState.settings = data.settings;
        renderActiveFruit(appState.activeFruitId);
      }
    } catch (err) {
      console.error('Error cargando datos:', err);
    }
  }

  // Render Active Fruit
  function renderActiveFruit(fruitId) {
    const fruit = appState.products.find(p => p.id === fruitId);
    if (!fruit) return;

    appState.activeFruitId = fruitId;

    // Update Header
    document.getElementById('currentFruitTitle').textContent = fruit.name_es;
    document.getElementById('currentFruitScientific').textContent = fruit.scientific;
    document.getElementById('btnPreviewFruit').href = `../${fruit.file}`;
    document.getElementById('specFruitId').value = fruit.id;

    // Update Counts
    const photoCount = (fruit.gallery || []).length;
    document.getElementById('activePhotoCount').textContent = photoCount;

    // Update Selector Cards active class
    document.querySelectorAll('.fruit-card').forEach(card => {
      card.classList.toggle('active', card.getAttribute('data-fruit-id') === fruitId);
    });

    // Render Gallery Grid
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

  // Render Gallery Grid
  function renderGalleryGrid(fruit) {
    const grid = document.getElementById('adminGalleryGrid');
    grid.innerHTML = '';

    const gallery = fruit.gallery || [];
    if (gallery.length === 0) {
      grid.innerHTML = '<p class="text-muted" style="grid-column: 1/-1; padding: 2rem 0; text-align: center;">No hay fotos en esta galería. Sube la primera fotografía arriba.</p>';
      return;
    }

    const cleanMainImg = (fruit.img || '').split('?')[0];

    gallery.forEach((imgUrl, index) => {
      const cleanUrl = imgUrl.split('?')[0];
      const isMain = cleanUrl === cleanMainImg;
      
      // Determine square thumb url
      let sqUrl = cleanUrl;
      if (!cleanUrl.includes('-sq.')) {
        const lastDot = cleanUrl.lastIndexOf('.');
        if (lastDot !== -1) {
          sqUrl = cleanUrl.substring(0, lastDot) + '-sq' + cleanUrl.substring(lastDot);
        }
      }

      const card = document.createElement('div');
      card.className = 'admin-photo-card';
      card.innerHTML = `
        ${isMain ? '<span class="photo-badge-main"><i class="fa-solid fa-star"></i> Foto Principal</span>' : ''}
        <img src="../${sqUrl}?v=${Date.now()}" alt="${fruit.name_es}">
        <div class="photo-actions-overlay">
          ${!isMain ? `
            <button type="button" class="btn-photo-action btn-photo-main" title="Fijar como Foto Principal" data-url="${cleanUrl}">
              <i class="fa-regular fa-star"></i>
            </button>
          ` : ''}
          <button type="button" class="btn-photo-action btn-photo-delete" title="Eliminar Foto" data-index="${index}" data-url="${cleanUrl}">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
      `;

      // Event: Set Main Photo
      const btnMain = card.querySelector('.btn-photo-main');
      if (btnMain) {
        btnMain.addEventListener('click', async () => {
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
              showToast('Foto principal actualizada correctamente');
            }
          } catch (e) {
            showToast('Error al actualizar foto principal', true);
          }
        });
      }

      // Event: Delete Photo
      const btnDel = card.querySelector('.btn-photo-delete');
      if (btnDel) {
        btnDel.addEventListener('click', async () => {
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
            }
          } catch (e) {
            showToast('Error al eliminar fotografía', true);
          }
        });
      }

      grid.appendChild(card);
    });
  }

  // Fruit Selector Clicks
  document.querySelectorAll('.fruit-card').forEach(card => {
    card.addEventListener('click', () => {
      const fId = card.getAttribute('data-fruit-id');
      renderActiveFruit(fId);
    });
  });

  // Sidebar Tabs Navigation
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
      }
    });
  });

  // Sub-Tabs Navigation (Gallery / Specs)
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

  // Drag & Drop File Upload
  const dropzone = document.getElementById('uploadDropzone');
  const fileInput = document.getElementById('fileInput');
  const uploadProgress = document.getElementById('uploadProgress');

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
        showToast('Foto subida, recortada 1:1 y publicada en la web');
      } else {
        showToast(result.error || 'Error al subir imagen', true);
      }
    } catch (e) {
      uploadProgress.style.display = 'none';
      showToast('Error de conexión al subir imagen', true);
    }
  }

  // Save Product Specs Form
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
        // Update local state
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

  // Save Site Settings Form
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
        showToast('Ajustes generales actualizados con éxito');
      } else {
        showToast(result.error || 'Error al guardar ajustes', true);
      }
    } catch (err) {
      showToast('Error de conexión', true);
    }
  });

  // Load Quotes
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

  // Change Password Form
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

  // Rebuild All Button
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

  // Initial Load
  loadInitialData();
});
