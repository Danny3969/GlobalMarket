// GlobalMarket GM - Cloud Drive Controller
document.addEventListener('DOMContentLoaded', () => {
  const rawInit = window.INITIAL_DRIVE_DATA || {};

  let driveState = {
    currentPath: rawInit.current_path || 'GlobalMarket',
    currentView: 'grid',
    folders: rawInit.folders || [],
    files: rawInit.files || [],
    breadcrumbs: rawInit.breadcrumbs || [{ name: 'GlobalMarket', path: 'GlobalMarket' }],
    stats: rawInit.stats || {},
    user: rawInit.user || {},
    searchQuery: ''
  };

  // Toast Helper
  function showToast(message, isError = false) {
    const toast = document.getElementById('driveToast');
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
  // 1. LOAD DIRECTORY
  // =========================================================================
  async function loadDirectory(path = 'GlobalMarket') {
    const contentArea = document.getElementById('driveExplorerContent');
    contentArea.innerHTML = '<div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);"><i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--gold-light);"></i><p>Cargando archivos...</p></div>';

    try {
      const res = await fetch(`api.php?action=get_tree&path=${encodeURIComponent(path)}`);
      const data = await res.json();

      if (data.success) {
        driveState.currentPath = data.current_path;
        driveState.breadcrumbs = data.breadcrumbs;
        driveState.folders = data.folders;
        driveState.files = data.files;
        driveState.stats = data.stats;
        driveState.user = data.user;

        updateUIPermissions();
        renderBreadcrumbs();
        renderExplorer();
      } else {
        showToast(data.error || 'Error al cargar directorio', true);
      }
    } catch (e) {
      contentArea.innerHTML = '<div style="text-align:center; padding: 4rem 1rem; color: #ef4444;"><i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; margin-bottom: 1rem;"></i><p>Error de conexión al cargar archivos</p></div>';
    }
  }

  function updateUIPermissions() {
    const isAdmin = driveState.user.is_admin;
    const adminOnlyBtns = document.querySelectorAll('.admin-only');
    adminOnlyBtns.forEach(el => {
      el.style.display = isAdmin ? '' : 'none';
    });
  }

  // =========================================================================
  // 2. RENDER BREADCRUMBS
  // =========================================================================
  function renderBreadcrumbs() {
    const container = document.getElementById('driveBreadcrumbs');
    container.innerHTML = '';

    const crumbs = driveState.breadcrumbs || [];
    crumbs.forEach((c, idx) => {
      const isLast = idx === crumbs.length - 1;

      if (idx > 0) {
        const sep = document.createElement('span');
        sep.className = 'crumb-separator';
        sep.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        container.appendChild(sep);
      }

      if (isLast) {
        const span = document.createElement('span');
        span.className = 'crumb-current';
        span.innerHTML = `<i class="fa-solid fa-folder-open text-gold"></i> ${c.name}`;
        container.appendChild(span);
      } else {
        const a = document.createElement('a');
        a.className = 'crumb-link';
        a.innerHTML = idx === 0 ? '<i class="fa-solid fa-hard-drive"></i> GlobalMarket' : c.name;
        a.addEventListener('click', (e) => {
          e.preventDefault();
          loadDirectory(c.path);
        });
        container.appendChild(a);
      }
    });
  }

  // =========================================================================
  // 3. RENDER EXPLORER (GRID OR LIST)
  // =========================================================================
  function renderExplorer() {
    const container = document.getElementById('driveExplorerContent');
    container.innerHTML = '';

    // Filter by search query
    const q = (driveState.searchQuery || '').toLowerCase().trim();
    const filteredFolders = driveState.folders.filter(f => f.name.toLowerCase().includes(q));
    const filteredFiles = driveState.files.filter(f => f.name.toLowerCase().includes(q));

    if (filteredFolders.length === 0 && filteredFiles.length === 0) {
      container.innerHTML = `
        <div class="drive-empty-state">
          <i class="fa-regular fa-folder-open"></i>
          <h4>Esta carpeta está vacía</h4>
          <p>${driveState.user.is_admin ? 'Sube documentos o crea carpetas usando los botones superiores.' : 'No hay documentos disponibles en esta sección.'}</p>
        </div>
      `;
      return;
    }

    if (driveState.currentView === 'grid') {
      renderGridView(container, filteredFolders, filteredFiles);
    } else {
      renderListView(container, filteredFolders, filteredFiles);
    }
  }

  // Grid View Renderer
  function renderGridView(container, folders, files) {
    // Folders Section
    if (folders.length > 0) {
      const folderTitle = document.createElement('h4');
      folderTitle.className = 'section-title';
      folderTitle.innerHTML = `<i class="fa-solid fa-folder"></i> Carpetas (${folders.length})`;
      container.appendChild(folderTitle);

      const fGrid = document.createElement('div');
      fGrid.className = 'folders-grid';

      folders.forEach(f => {
        const card = document.createElement('div');
        card.className = 'folder-card';
        card.innerHTML = `
          <div class="folder-card-main">
            <i class="fa-solid fa-folder folder-icon"></i>
            <div class="folder-info">
              <div class="folder-name" title="${f.name}">${f.name}</div>
              <div class="folder-count">${f.items_count} elemento${f.items_count !== 1 ? 's' : ''}</div>
            </div>
          </div>
          ${driveState.user.is_admin ? `
            <div class="folder-actions" style="display: flex; gap: 0.2rem;">
              <button type="button" class="btn-card-action btn-del btn-del-folder" title="Eliminar Carpeta" data-path="${f.path}">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          ` : ''}
        `;

        card.querySelector('.folder-card-main').addEventListener('click', () => {
          loadDirectory(f.path);
        });

        const btnDel = card.querySelector('.btn-del-folder');
        if (btnDel) {
          btnDel.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteItem(f.path, f.name, true);
          });
        }

        fGrid.appendChild(card);
      });

      container.appendChild(fGrid);
    }

    // Files Section
    if (files.length > 0) {
      const fileTitle = document.createElement('h4');
      fileTitle.className = 'section-title';
      fileTitle.innerHTML = `<i class="fa-solid fa-file-lines"></i> Archivos (${files.length})`;
      container.appendChild(fileTitle);

      const fileGrid = document.createElement('div');
      fileGrid.className = 'files-grid';

      files.forEach(file => {
        const card = document.createElement('div');
        card.className = 'file-card';
        
        let previewHtml = `<i class="fa-solid ${file.icon}" style="color: ${file.color};"></i>`;
        if (['jpg', 'jpeg', 'png', 'webp'].includes(file.ext)) {
          previewHtml = `<img src="api.php?action=view&path=${encodeURIComponent(file.path)}" alt="${file.name}" style="width:100%; height:100%; object-fit:cover;">`;
        }

        card.innerHTML = `
          <div class="file-card-preview" title="Hacer clic para previsualizar">
            ${previewHtml}
          </div>
          <div class="file-card-info">
            <div class="file-name" title="${file.name}">${file.name}</div>
            <div class="file-meta">
              <span>${file.size_formatted}</span>
              <span>${file.mtime.split(' ')[0]}</span>
            </div>
            <div class="file-card-actions">
              ${file.previewable ? `
                <button type="button" class="btn-card-action btn-preview-file" title="Previsualizar Documento" data-path="${file.path}" data-name="${file.name}" data-ext="${file.ext}">
                  <i class="fa-solid fa-eye"></i>
                </button>
              ` : ''}
              <a href="api.php?action=download&path=${encodeURIComponent(file.path)}" class="btn-card-action" title="Descargar Archivo">
                <i class="fa-solid fa-download"></i>
              </a>
              ${driveState.user.is_admin ? `
                <button type="button" class="btn-card-action btn-del btn-del-file" title="Eliminar Archivo" data-path="${file.path}">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              ` : ''}
            </div>
          </div>
        `;

        card.querySelector('.file-card-preview').addEventListener('click', () => {
          if (file.previewable) {
            openPreviewModal(file.path, file.name, file.ext);
          } else {
            window.location.href = `api.php?action=download&path=${encodeURIComponent(file.path)}`;
          }
        });

        const btnPrev = card.querySelector('.btn-preview-file');
        if (btnPrev) {
          btnPrev.addEventListener('click', (e) => {
            e.stopPropagation();
            openPreviewModal(file.path, file.name, file.ext);
          });
        }

        const btnDel = card.querySelector('.btn-del-file');
        if (btnDel) {
          btnDel.addEventListener('click', (e) => {
            e.stopPropagation();
            deleteItem(file.path, file.name, false);
          });
        }

        fileGrid.appendChild(card);
      });

      container.appendChild(fileGrid);
    }
  }

  // List View Renderer
  function renderListView(container, folders, files) {
    const table = document.createElement('table');
    table.className = 'drive-list-table';

    table.innerHTML = `
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Tipo</th>
          <th>Tamaño</th>
          <th>Última Modificación</th>
          <th style="text-align: right;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        ${folders.map(f => `
          <tr class="folder-row" data-path="${f.path}">
            <td>
              <div class="table-item-name">
                <i class="fa-solid fa-folder text-gold"></i>
                <span>${f.name}</span>
              </div>
            </td>
            <td><span class="badge-vault">Carpeta</span></td>
            <td>${f.items_count} elementos</td>
            <td>${f.mtime}</td>
            <td style="text-align: right;">
              ${driveState.user.is_admin ? `
                <button type="button" class="btn-card-action btn-del btn-del-folder" data-path="${f.path}" title="Eliminar"><i class="fa-solid fa-trash-can"></i></button>
              ` : ''}
            </td>
          </tr>
        `).join('')}
        ${files.map(file => `
          <tr class="file-row" data-path="${file.path}">
            <td>
              <div class="table-item-name btn-preview-inline" data-path="${file.path}" data-name="${file.name}" data-ext="${file.ext}" data-prev="${file.previewable}">
                <i class="fa-solid ${file.icon}" style="color: ${file.color};"></i>
                <span>${file.name}</span>
              </div>
            </td>
            <td><span class="badge-vault" style="background: rgba(255,255,255,0.06);">${file.type_name}</span></td>
            <td>${file.size_formatted}</td>
            <td>${file.mtime}</td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.35rem;">
                ${file.previewable ? `
                  <button type="button" class="btn-card-action btn-preview-file" data-path="${file.path}" data-name="${file.name}" data-ext="${file.ext}" title="Ver"><i class="fa-solid fa-eye"></i></button>
                ` : ''}
                <a href="api.php?action=download&path=${encodeURIComponent(file.path)}" class="btn-card-action" title="Descargar"><i class="fa-solid fa-download"></i></a>
                ${driveState.user.is_admin ? `
                  <button type="button" class="btn-card-action btn-del btn-del-file" data-path="${file.path}" title="Eliminar"><i class="fa-solid fa-trash-can"></i></button>
                ` : ''}
              </div>
            </td>
          </tr>
        `).join('')}
      </tbody>
    `;

    // Bind folder row clicks
    table.querySelectorAll('.folder-row').forEach(tr => {
      tr.querySelector('.table-item-name').addEventListener('click', () => {
        loadDirectory(tr.getAttribute('data-path'));
      });
    });

    // Bind preview clicks
    table.querySelectorAll('.btn-preview-inline').forEach(el => {
      el.addEventListener('click', () => {
        const canPrev = el.getAttribute('data-prev') === 'true';
        const p = el.getAttribute('data-path');
        const n = el.getAttribute('data-name');
        const ext = el.getAttribute('data-ext');
        if (canPrev) {
          openPreviewModal(p, n, ext);
        } else {
          window.location.href = `api.php?action=download&path=${encodeURIComponent(p)}`;
        }
      });
    });

    table.querySelectorAll('.btn-preview-file').forEach(btn => {
      btn.addEventListener('click', () => {
        openPreviewModal(btn.getAttribute('data-path'), btn.getAttribute('data-name'), btn.getAttribute('data-ext'));
      });
    });

    table.querySelectorAll('.btn-del-folder').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteItem(btn.getAttribute('data-path'), 'esta carpeta', true);
      });
    });

    table.querySelectorAll('.btn-del-file').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteItem(btn.getAttribute('data-path'), 'este archivo', false);
      });
    });

    container.appendChild(table);
  }

  // =========================================================================
  // 4. PREVIEW MODAL
  // =========================================================================
  function openPreviewModal(path, filename, ext) {
    const modal = document.getElementById('previewModal');
    const title = document.getElementById('previewModalTitle');
    const body = document.getElementById('previewModalBody');
    const btnDownload = document.getElementById('btnPreviewDownload');

    title.textContent = filename;
    btnDownload.href = `api.php?action=download&path=${encodeURIComponent(path)}`;

    const streamUrl = `api.php?action=view&path=${encodeURIComponent(path)}`;

    if (ext === 'pdf') {
      body.innerHTML = `<iframe src="${streamUrl}"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
      body.innerHTML = `<img src="${streamUrl}" alt="${filename}">`;
    } else if (['mp4', 'mov'].includes(ext)) {
      body.innerHTML = `<video controls autoplay><source src="${streamUrl}" type="video/mp4">Tu navegador no soporta video.</video>`;
    } else {
      body.innerHTML = `<iframe src="${streamUrl}"></iframe>`;
    }

    modal.classList.add('show');
  }

  document.getElementById('btnClosePreview').addEventListener('click', () => {
    document.getElementById('previewModal').classList.remove('show');
    document.getElementById('previewModalBody').innerHTML = '';
  });

  // =========================================================================
  // 5. UPLOAD & DRAG DROP
  // =========================================================================
  const btnUpload = document.getElementById('btnUploadFile');
  const fileInput = document.getElementById('driveFileInput');

  if (btnUpload && fileInput) {
    btnUpload.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        handleUpload(fileInput.files[0]);
        fileInput.value = '';
      }
    });
  }

  async function handleUpload(file) {
    const formData = new FormData();
    formData.append('target_path', driveState.currentPath);
    formData.append('file', file);

    showToast(`Subiendo ${file.name}...`, false);

    try {
      const res = await fetch('api.php?action=upload_file', {
        method: 'POST',
        body: formData
      });
      const result = await res.json();
      if (result.success) {
        showToast('Archivo subido con éxito');
        loadDirectory(driveState.currentPath);
      } else {
        showToast(result.error || 'Error al subir', true);
      }
    } catch (e) {
      showToast('Error de conexión al subir archivo', true);
    }
  }

  // =========================================================================
  // 6. CREATE FOLDER
  // =========================================================================
  const btnNewFolder = document.getElementById('btnNewFolder');
  const folderModal = document.getElementById('folderModal');
  const formNewFolder = document.getElementById('formNewFolder');

  if (btnNewFolder) {
    btnNewFolder.addEventListener('click', () => {
      document.getElementById('newFolderName').value = '';
      folderModal.classList.add('show');
    });
  }

  document.getElementById('btnCloseFolderModal').addEventListener('click', () => {
    folderModal.classList.remove('show');
  });

  formNewFolder.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = document.getElementById('newFolderName').value.trim();
    if (!name) return;

    try {
      const res = await fetch('api.php?action=create_folder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name, parent_path: driveState.currentPath })
      });
      const result = await res.json();
      folderModal.classList.remove('show');
      if (result.success) {
        showToast('Carpeta creada con éxito');
        loadDirectory(driveState.currentPath);
      } else {
        showToast(result.error || 'Error al crear carpeta', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 7. DELETE ITEM
  // =========================================================================
  async function deleteItem(path, name, isFolder) {
    const msg = isFolder 
      ? `¿Seguro que deseas eliminar la carpeta "${name}" y todo su contenido?`
      : `¿Seguro que deseas eliminar el archivo "${name}"?`;

    if (!confirm(msg)) return;

    try {
      const res = await fetch('api.php?action=delete_item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ path: path })
      });
      const result = await res.json();
      if (result.success) {
        showToast(result.message || 'Eliminado con éxito');
        loadDirectory(driveState.currentPath);
      } else {
        showToast(result.error || 'Error al eliminar', true);
      }
    } catch (e) {
      showToast('Error de conexión al eliminar', true);
    }
  }

  // =========================================================================
  // 8. USER MANAGEMENT MODAL
  // =========================================================================
  const btnUsersModal = document.getElementById('btnManageUsers');
  const usersModal = document.getElementById('usersModal');
  const formCreateUser = document.getElementById('formCreateUser');

  if (btnUsersModal) {
    btnUsersModal.addEventListener('click', () => {
      loadUsersList();
      usersModal.classList.add('show');
    });
  }

  document.getElementById('btnCloseUsersModal').addEventListener('click', () => {
    usersModal.classList.remove('show');
  });

  async function loadUsersList() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:1.5rem;"><i class="fa-solid fa-spinner fa-spin"></i> Cargando usuarios...</td></tr>';

    try {
      const res = await fetch('api.php?action=get_users');
      const data = await res.json();
      if (data.success) {
        tbody.innerHTML = '';
        data.users.forEach(u => {
          const tr = document.createElement('tr');
          const roleLabels = { admin: 'Administrador', client: 'Cliente', collab: 'Colaborador' };
          
          tr.innerHTML = `
            <td><strong>${u.name}</strong><br><small class="text-muted">@${u.username}</small></td>
            <td><span class="user-role-badge">${roleLabels[u.role] || u.role}</span></td>
            <td>${u.email || '-'}</td>
            <td><small class="text-muted">${u.created_at || 'Reciente'}</small></td>
            <td style="text-align: right;">
              <button type="button" class="btn btn-danger btn-sm btn-delete-user" data-id="${u.id}" title="Eliminar"><i class="fa-solid fa-trash-can"></i></button>
            </td>
          `;

          const btnDel = tr.querySelector('.btn-delete-user');
          if (btnDel) {
            btnDel.addEventListener('click', async () => {
              if (!confirm(`¿Eliminar al usuario ${u.name}?`)) return;
              const res = await fetch('api.php?action=delete_user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: u.id })
              });
              const result = await res.json();
              if (result.success) {
                showToast('Usuario eliminado');
                loadUsersList();
              } else {
                showToast(result.error || 'Error al eliminar', true);
              }
            });
          }

          tbody.appendChild(tr);
        });
      }
    } catch (e) {
      tbody.innerHTML = '<tr><td colspan="5" style="color:#ef4444; text-align:center;">Error al cargar usuarios</td></tr>';
    }
  }

  formCreateUser.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
      name: document.getElementById('newUserName').value.trim(),
      username: document.getElementById('newUserUsername').value.trim(),
      email: document.getElementById('newUserEmail').value.trim(),
      password: document.getElementById('newUserPassword').value.trim(),
      role: document.getElementById('newUserRole').value
    };

    try {
      const res = await fetch('api.php?action=create_user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const result = await res.json();
      if (result.success) {
        showToast('Usuario creado correctamente');
        formCreateUser.reset();
        loadUsersList();
      } else {
        showToast(result.error || 'Error al crear usuario', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  });

  // =========================================================================
  // 9. SEARCH & VIEW TOGGLES & SIDEBAR LINKS
  // =========================================================================
  const searchInput = document.getElementById('driveSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      driveState.searchQuery = e.target.value;
      renderExplorer();
    });
  }

  document.querySelectorAll('.btn-view-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.btn-view-toggle').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      driveState.currentView = btn.getAttribute('data-view');
      renderExplorer();
    });
  });

  document.querySelectorAll('.sidebar-shortcut').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      document.querySelectorAll('.sidebar-shortcut').forEach(l => l.classList.remove('active'));
      link.classList.add('active');
      const targetPath = link.getAttribute('data-path');
      loadDirectory(targetPath);
    });
  });

  // =========================================================================
  // 10. INITIAL RENDER
  // =========================================================================
  updateUIPermissions();
  renderBreadcrumbs();
  renderExplorer();
});
