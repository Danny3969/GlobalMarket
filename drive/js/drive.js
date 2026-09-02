// GlobalMarket GM - Cloud Drive & Intranet Controller
document.addEventListener('DOMContentLoaded', () => {
  const rawInit = window.INITIAL_DRIVE_DATA || {};

  let driveState = {
    currentPath: rawInit.current_path || 'GlobalMarket',
    currentView: 'grid',
    inTrashView: false,
    folders: rawInit.folders || [],
    files: rawInit.files || [],
    favorites: rawInit.favorites || [],
    trashItems: [],
    trashCount: rawInit.trash_count || 0,
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
  // 1. DIRECTORY LOADER
  // =========================================================================
  async function loadDirectory(path = 'GlobalMarket') {
    driveState.inTrashView = false;
    document.getElementById('trashBanner').style.display = 'none';
    document.getElementById('driveToolbar').style.display = 'flex';
    document.getElementById('sidebarNavRoot').classList.add('active');
    document.getElementById('sidebarNavTrash').classList.remove('active');

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
        driveState.favorites = data.favorites || [];
        driveState.stats = data.stats;
        driveState.user = data.user;

        renderSidebarFavorites();
        renderBreadcrumbs();
        renderExplorer();
      } else {
        showToast(data.error || 'Error al cargar directorio', true);
      }
    } catch (e) {
      contentArea.innerHTML = '<div style="text-align:center; padding: 4rem 1rem; color: #ef4444;"><i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; margin-bottom: 1rem;"></i><p>Error de conexión al cargar archivos</p></div>';
    }
  }

  // =========================================================================
  // 2. TRASH LOADER
  // =========================================================================
  async function loadTrashView() {
    driveState.inTrashView = true;
    driveState.currentPath = '_trash_';
    document.getElementById('trashBanner').style.display = 'flex';
    document.getElementById('driveToolbar').style.display = 'none';
    document.getElementById('sidebarNavRoot').classList.remove('active');
    document.getElementById('sidebarNavTrash').classList.add('active');

    const breadcrumbs = document.getElementById('driveBreadcrumbs');
    breadcrumbs.innerHTML = `
      <a href="#" class="crumb-link" id="crumbBackHome"><i class="fa-solid fa-hard-drive"></i> Mi Unidad</a>
      <span class="crumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
      <span class="crumb-current"><i class="fa-solid fa-trash-can text-danger"></i> Papelera de Reciclaje</span>
    `;
    document.getElementById('crumbBackHome').addEventListener('click', (e) => {
      e.preventDefault();
      loadDirectory('GlobalMarket');
    });

    const contentArea = document.getElementById('driveExplorerContent');
    contentArea.innerHTML = '<div style="text-align:center; padding: 4rem 1rem; color: var(--text-muted);"><i class="fa-solid fa-spinner fa-spin" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--gold-light);"></i><p>Cargando papelera...</p></div>';

    try {
      const res = await fetch('api.php?action=get_trash');
      const data = await res.json();

      if (data.success) {
        driveState.trashItems = data.items || [];
        driveState.trashCount = data.count || 0;
        updateTrashBadgeCount(driveState.trashCount);
        renderTrashView();
      } else {
        showToast(data.error || 'Error al cargar la papelera', true);
      }
    } catch (e) {
      contentArea.innerHTML = '<div style="text-align:center; padding: 4rem 1rem; color: #ef4444;"><i class="fa-solid fa-triangle-exclamation" style="font-size: 2.5rem; margin-bottom: 1rem;"></i><p>Error de conexión al cargar la papelera</p></div>';
    }
  }

  function updateTrashBadgeCount(cnt) {
    const el = document.getElementById('sidebarTrashCount');
    if (el) {
      el.textContent = cnt;
    }
  }

  // =========================================================================
  // 3. RENDER SIDEBAR FAVORITES
  // =========================================================================
  function renderSidebarFavorites() {
    const container = document.getElementById('sidebarFavorites');
    if (!container) return;
    container.innerHTML = '';

    const favs = driveState.favorites || [];
    if (favs.length === 0) {
      container.innerHTML = '<div class="sidebar-empty-favs"><small>Sin carpetas fijadas</small></div>';
      return;
    }

    favs.forEach(f => {
      const a = document.createElement('a');
      a.href = '#';
      a.className = 'sidebar-fav-link';
      a.dataset.path = f.path;
      a.title = f.name;
      a.innerHTML = `<i class="fa-solid fa-star text-gold"></i><span>${escapeHtml(f.name)}</span>`;
      a.addEventListener('click', (e) => {
        e.preventDefault();
        loadDirectory(f.path);
      });
      container.appendChild(a);
    });
  }

  // =========================================================================
  // 4. RENDER BREADCRUMBS
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
        span.innerHTML = `<i class="fa-solid fa-folder-open text-gold"></i> ${escapeHtml(c.name)}`;
        container.appendChild(span);
      } else {
        const a = document.createElement('a');
        a.className = 'crumb-link';
        a.innerHTML = idx === 0 ? '<i class="fa-solid fa-hard-drive"></i> Mi Unidad' : escapeHtml(c.name);
        a.addEventListener('click', (e) => {
          e.preventDefault();
          loadDirectory(c.path);
        });
        container.appendChild(a);
      }
    });
  }

  // =========================================================================
  // 5. RENDER EXPLORER (GRID OR LIST)
  // =========================================================================
  function renderExplorer() {
    const container = document.getElementById('driveExplorerContent');
    container.innerHTML = '';

    const q = (driveState.searchQuery || '').toLowerCase().trim();
    const filteredFolders = driveState.folders.filter(f => f.name.toLowerCase().includes(q));
    const filteredFiles = driveState.files.filter(f => f.name.toLowerCase().includes(q));

    if (filteredFolders.length === 0 && filteredFiles.length === 0) {
      container.innerHTML = `
        <div class="drive-empty-state">
          <i class="fa-regular fa-folder-open"></i>
          <h4>Esta carpeta está vacía</h4>
          <p>${(driveState.user.is_admin || driveState.user.role === 'collab') ? 'Arrastra archivos aquí o usa el botón "Subir Archivo".' : 'No hay documentos disponibles en esta sección.'}</p>
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

  // Close any open context dropdowns
  function closeAllDropdownMenus() {
    document.querySelectorAll('.drive-dropdown-menu.show').forEach(m => m.classList.remove('show'));
    document.querySelectorAll('.btn-card-menu.active').forEach(b => b.classList.remove('active'));
  }

  // Global listener to close dropdowns when clicking outside
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.drive-menu-wrapper')) {
      closeAllDropdownMenus();
    }
  });

  // Grid View Renderer
  function renderGridView(container, folders, files) {
    const isAdmin = driveState.user.is_admin;
    const isCollab = driveState.user.role === 'collab';

    // FOLDERS
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
          <div class="folder-card-top">
            <div class="folder-icon-wrapper">
              <i class="fa-solid fa-folder folder-icon-main"></i>
              ${f.is_favorite ? '<i class="fa-solid fa-star folder-fav-star" title="Carpeta Favorita"></i>' : ''}
            </div>
            <div class="drive-menu-wrapper">
              <button type="button" class="btn-card-menu" title="Opciones">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <div class="drive-dropdown-menu">
                <button type="button" class="dropdown-menu-item item-open">
                  <i class="fa-solid fa-folder-open text-gold"></i> Abrir Carpeta
                </button>
                <button type="button" class="dropdown-menu-item item-fav">
                  <i class="fa-${f.is_favorite ? 'solid' : 'regular'} fa-star text-gold"></i> ${f.is_favorite ? 'Quitar de Favoritos' : 'Marcar Favorita'}
                </button>
                ${isAdmin ? `
                  <button type="button" class="dropdown-menu-item item-rename">
                    <i class="fa-solid fa-pen-to-square" style="color: #60a5fa;"></i> Renombrar
                  </button>
                ` : ''}
                ${(isAdmin || isCollab) ? `
                  <div class="dropdown-menu-divider"></div>
                  <button type="button" class="dropdown-menu-item danger item-trash">
                    <i class="fa-solid fa-trash-can"></i> Enviar a Papelera
                  </button>
                ` : ''}
              </div>
            </div>
          </div>
          <div class="folder-card-body">
            <h4 class="folder-name" title="${escapeHtml(f.name)}">${escapeHtml(f.name)}</h4>
            <div class="folder-meta">
              <span><i class="fa-solid fa-box-archive" style="font-size: 0.72rem;"></i> ${f.items_count} elemento${f.items_count === 1 ? '' : 's'}</span>
              <span>•</span>
              <span>${f.mtime}</span>
            </div>
          </div>
        `;

        // Open Folder on card body or icon click
        card.querySelector('.folder-card-body').addEventListener('click', () => loadDirectory(f.path));
        card.querySelector('.folder-icon-wrapper').addEventListener('click', () => loadDirectory(f.path));

        // 3-Dots Button Toggle
        const menuBtn = card.querySelector('.btn-card-menu');
        const menuDropdown = card.querySelector('.drive-dropdown-menu');

        menuBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isOpen = menuDropdown.classList.contains('show');
          closeAllDropdownMenus();
          if (!isOpen) {
            menuDropdown.classList.add('show');
            menuBtn.classList.add('active');
          }
        });

        // Menu Actions
        card.querySelector('.item-open').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          loadDirectory(f.path);
        });

        card.querySelector('.item-fav').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          toggleFavorite(f.path, f.name);
        });

        if (isAdmin) {
          card.querySelector('.item-rename').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdownMenus();
            openRenameModal(f.path, f.name);
          });
        }

        if (isAdmin || isCollab) {
          card.querySelector('.item-trash').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdownMenus();
            moveToTrash(f.path, f.name);
          });
        }

        fGrid.appendChild(card);
      });

      container.appendChild(fGrid);
    }

    // FILES
    if (files.length > 0) {
      const fileTitle = document.createElement('h4');
      fileTitle.className = 'section-title';
      fileTitle.style.marginTop = folders.length > 0 ? '2.5rem' : '0';
      fileTitle.innerHTML = `<i class="fa-solid fa-file"></i> Archivos (${files.length})`;
      container.appendChild(fileTitle);

      const fGrid = document.createElement('div');
      fGrid.className = 'files-grid';

      files.forEach(file => {
        const card = document.createElement('div');
        card.className = 'file-card';

        card.innerHTML = `
          <div class="file-card-top">
            <span class="file-type-pill" style="color: ${file.color}; border: 1px solid ${file.color}40;">
              <i class="fa-solid ${file.icon}"></i> ${escapeHtml(file.ext.toUpperCase())}
            </span>
            <div class="drive-menu-wrapper">
              <button type="button" class="btn-card-menu" title="Opciones">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <div class="drive-dropdown-menu">
                ${file.previewable ? `
                  <button type="button" class="dropdown-menu-item item-preview">
                    <i class="fa-solid fa-eye text-gold"></i> Previsualizar
                  </button>
                ` : ''}
                <a href="api.php?action=download&path=${encodeURIComponent(file.path)}" class="dropdown-menu-item item-download">
                  <i class="fa-solid fa-download" style="color: var(--success);"></i> Descargar
                </a>
                ${isAdmin ? `
                  <button type="button" class="dropdown-menu-item item-rename">
                    <i class="fa-solid fa-pen-to-square" style="color: #60a5fa;"></i> Renombrar
                  </button>
                ` : ''}
                ${(isAdmin || isCollab) ? `
                  <div class="dropdown-menu-divider"></div>
                  <button type="button" class="dropdown-menu-item danger item-trash">
                    <i class="fa-solid fa-trash-can"></i> Enviar a Papelera
                  </button>
                ` : ''}
              </div>
            </div>
          </div>
          <div class="file-preview-area">
            <i class="fa-solid ${file.icon} file-preview-icon" style="color: ${file.color};"></i>
          </div>
          <div class="file-card-body">
            <h4 class="file-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</h4>
            <div class="file-meta">
              <span>${file.size_formatted}</span>
              <span>${file.mtime}</span>
            </div>
          </div>
        `;

        const openAction = () => {
          if (file.previewable) {
            openPreview(file);
          } else {
            window.location.href = `api.php?action=download&path=${encodeURIComponent(file.path)}`;
          }
        };

        card.querySelector('.file-preview-area').addEventListener('click', openAction);
        card.querySelector('.file-card-body').addEventListener('click', openAction);

        // 3-Dots Button Toggle
        const menuBtn = card.querySelector('.btn-card-menu');
        const menuDropdown = card.querySelector('.drive-dropdown-menu');

        menuBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          const isOpen = menuDropdown.classList.contains('show');
          closeAllDropdownMenus();
          if (!isOpen) {
            menuDropdown.classList.add('show');
            menuBtn.classList.add('active');
          }
        });

        if (file.previewable) {
          card.querySelector('.item-preview').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdownMenus();
            openPreview(file);
          });
        }

        if (isAdmin) {
          card.querySelector('.item-rename').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdownMenus();
            openRenameModal(file.path, file.name);
          });
        }

        if (isAdmin || isCollab) {
          card.querySelector('.item-trash').addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdownMenus();
            moveToTrash(file.path, file.name);
          });
        }

        fGrid.appendChild(card);
      });

      container.appendChild(fGrid);
    }
  }

  // List View Renderer
  function renderListView(container, folders, files) {
    const isAdmin = driveState.user.is_admin;
    const isCollab = driveState.user.role === 'collab';

    const tableWrapper = document.createElement('div');
    tableWrapper.className = 'drive-table-wrapper';

    const table = document.createElement('table');
    table.className = 'drive-list-table';

    table.innerHTML = `
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Tipo / Elementos</th>
          <th>Tamaño</th>
          <th>Última Modificación</th>
          <th style="text-align: right; width: 80px;">Opciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');

    // FOLDERS ROW
    folders.forEach(f => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="cursor: pointer;">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-folder text-gold" style="font-size: 1.35rem;"></i>
            <span style="font-weight: 600; color: #ffffff;">${escapeHtml(f.name)}</span>
            ${f.is_favorite ? '<i class="fa-solid fa-star text-gold" style="font-size: 0.8rem;" title="Favorita"></i>' : ''}
          </div>
        </td>
        <td>Carpeta (${f.items_count} items)</td>
        <td>—</td>
        <td>${f.mtime}</td>
        <td style="text-align: right;">
          <div class="drive-menu-wrapper" style="display: inline-block;">
            <button type="button" class="btn-card-menu" title="Opciones">
              <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div class="drive-dropdown-menu">
              <button type="button" class="dropdown-menu-item item-open">
                <i class="fa-solid fa-folder-open text-gold"></i> Abrir Carpeta
              </button>
              <button type="button" class="dropdown-menu-item item-fav">
                <i class="fa-${f.is_favorite ? 'solid' : 'regular'} fa-star text-gold"></i> ${f.is_favorite ? 'Quitar de Favoritos' : 'Marcar Favorita'}
              </button>
              ${isAdmin ? `
                <button type="button" class="dropdown-menu-item item-rename">
                  <i class="fa-solid fa-pen-to-square" style="color: #60a5fa;"></i> Renombrar
                </button>
              ` : ''}
              ${(isAdmin || isCollab) ? `
                <div class="dropdown-menu-divider"></div>
                <button type="button" class="dropdown-menu-item danger item-trash">
                  <i class="fa-solid fa-trash-can"></i> Enviar a Papelera
                </button>
              ` : ''}
            </div>
          </div>
        </td>
      `;

      tr.querySelector('td:first-child').addEventListener('click', () => loadDirectory(f.path));

      const menuBtn = tr.querySelector('.btn-card-menu');
      const menuDropdown = tr.querySelector('.drive-dropdown-menu');

      menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = menuDropdown.classList.contains('show');
        closeAllDropdownMenus();
        if (!isOpen) {
          menuDropdown.classList.add('show');
          menuBtn.classList.add('active');
        }
      });

      tr.querySelector('.item-open').addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdownMenus();
        loadDirectory(f.path);
      });

      tr.querySelector('.item-fav').addEventListener('click', (e) => {
        e.stopPropagation();
        closeAllDropdownMenus();
        toggleFavorite(f.path, f.name);
      });

      if (isAdmin) {
        tr.querySelector('.item-rename').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          openRenameModal(f.path, f.name);
        });
      }

      if (isAdmin || isCollab) {
        tr.querySelector('.item-trash').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          moveToTrash(f.path, f.name);
        });
      }

      tbody.appendChild(tr);
    });

    // FILES ROW
    files.forEach(file => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="cursor: ${file.previewable ? 'pointer' : 'default'};">
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid ${file.icon}" style="font-size: 1.25rem; color: ${file.color};"></i>
            <span style="font-weight: 500; color: #ffffff;">${escapeHtml(file.name)}</span>
          </div>
        </td>
        <td>${file.type_name}</td>
        <td>${file.size_formatted}</td>
        <td>${file.mtime}</td>
        <td style="text-align: right;">
          <div class="drive-menu-wrapper" style="display: inline-block;">
            <button type="button" class="btn-card-menu" title="Opciones">
              <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div class="drive-dropdown-menu">
              ${file.previewable ? `
                <button type="button" class="dropdown-menu-item item-preview">
                  <i class="fa-solid fa-eye text-gold"></i> Previsualizar
                </button>
              ` : ''}
              <a href="api.php?action=download&path=${encodeURIComponent(file.path)}" class="dropdown-menu-item item-download">
                <i class="fa-solid fa-download" style="color: var(--success);"></i> Descargar
              </a>
              ${isAdmin ? `
                <button type="button" class="dropdown-menu-item item-rename">
                  <i class="fa-solid fa-pen-to-square" style="color: #60a5fa;"></i> Renombrar
                </button>
              ` : ''}
              ${(isAdmin || isCollab) ? `
                <div class="dropdown-menu-divider"></div>
                <button type="button" class="dropdown-menu-item danger item-trash">
                  <i class="fa-solid fa-trash-can"></i> Enviar a Papelera
                </button>
              ` : ''}
            </div>
          </div>
        </td>
      `;

      const openAction = () => {
        if (file.previewable) openPreview(file);
      };
      tr.querySelector('td:first-child').addEventListener('click', openAction);

      const menuBtn = tr.querySelector('.btn-card-menu');
      const menuDropdown = tr.querySelector('.drive-dropdown-menu');

      menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = menuDropdown.classList.contains('show');
        closeAllDropdownMenus();
        if (!isOpen) {
          menuDropdown.classList.add('show');
          menuBtn.classList.add('active');
        }
      });

      if (file.previewable) {
        tr.querySelector('.item-preview').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          openPreview(file);
        });
      }

      if (isAdmin) {
        tr.querySelector('.item-rename').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          openRenameModal(file.path, file.name);
        });
      }

      if (isAdmin || isCollab) {
        tr.querySelector('.item-trash').addEventListener('click', (e) => {
          e.stopPropagation();
          closeAllDropdownMenus();
          moveToTrash(file.path, file.name);
        });
      }

      tbody.appendChild(tr);
    });

    tableWrapper.appendChild(table);
    container.appendChild(tableWrapper);
  }

  // =========================================================================
  // 6. RENDER TRASH VIEW
  // =========================================================================
  function renderTrashView() {
    const container = document.getElementById('driveExplorerContent');
    container.innerHTML = '';

    const isAdmin = driveState.user.is_admin;
    const items = driveState.trashItems || [];

    if (items.length === 0) {
      container.innerHTML = `
        <div class="drive-empty-state">
          <i class="fa-solid fa-trash-can" style="color: #64748b;"></i>
          <h4>La papelera de reciclaje está vacía</h4>
          <p>Los archivos que elimines se guardarán aquí temporalmente por si necesitas restaurarlos.</p>
        </div>
      `;
      return;
    }

    const tableWrapper = document.createElement('div');
    tableWrapper.className = 'drive-table-wrapper';

    const table = document.createElement('table');
    table.className = 'drive-list-table';

    table.innerHTML = `
      <thead>
        <tr>
          <th>Elemento Eliminado</th>
          <th>Ubicación Original</th>
          <th>Tamaño</th>
          <th>Fecha de Eliminación</th>
          <th>Eliminado Por</th>
          <th style="text-align: right;">Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = table.querySelector('tbody');

    items.forEach(item => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid ${item.is_folder ? 'fa-folder text-gold' : 'fa-file'}" style="font-size: 1.25rem;"></i>
            <span style="font-weight: 600; color: #ffffff;">${escapeHtml(item.name)}</span>
          </div>
        </td>
        <td><code style="color: var(--gold-light); font-size: 0.8rem;">${escapeHtml(item.original_path)}</code></td>
        <td>${item.size_formatted}</td>
        <td>${item.deleted_at}</td>
        <td>${escapeHtml(item.deleted_by || 'Admin')}</td>
        <td style="text-align: right;">
          <div style="display: inline-flex; gap: 0.4rem;">
            <button type="button" class="btn btn-primary btn-sm btn-restore" title="Restaurar a su ubicación original">
              <i class="fa-solid fa-rotate-left"></i> Restaurar
            </button>
            ${isAdmin ? `
              <button type="button" class="btn btn-danger btn-sm btn-purge" title="Eliminar definitivamente">
                <i class="fa-solid fa-trash-xmark"></i> Eliminar
              </button>
            ` : ''}
          </div>
        </td>
      `;

      tr.querySelector('.btn-restore').addEventListener('click', () => {
        restoreFromTrash(item.id, item.name);
      });

      if (isAdmin) {
        tr.querySelector('.btn-purge').addEventListener('click', () => {
          purgeFromTrash(item.id, item.name);
        });
      }

      tbody.appendChild(tr);
    });

    tableWrapper.appendChild(table);
    container.appendChild(tableWrapper);
  }

  // =========================================================================
  // 7. FAVORITES TOGGLE
  // =========================================================================
  async function toggleFavorite(path, name) {
    try {
      const res = await fetch('api.php?action=toggle_favorite', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ path, name })
      });
      const data = await res.json();

      if (data.success) {
        driveState.favorites = data.favorites;
        renderSidebarFavorites();

        // Update card in current list
        driveState.folders.forEach(f => {
          if (f.path === path) {
            f.is_favorite = data.is_favorite;
          }
        });
        renderExplorer();
        showToast(data.message);
      } else {
        showToast(data.error || 'Error al actualizar favoritos', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  }

  // =========================================================================
  // 8. RENAME MODAL & ACTION
  // =========================================================================
  function openRenameModal(path, currentName) {
    document.getElementById('renameItemPath').value = path;
    document.getElementById('renameItemNewName').value = currentName;
    document.getElementById('renameModal').classList.add('active');
    setTimeout(() => {
      document.getElementById('renameItemNewName').focus();
      document.getElementById('renameItemNewName').select();
    }, 100);
  }

  document.getElementById('btnCloseRenameModal').addEventListener('click', () => {
    document.getElementById('renameModal').classList.remove('active');
  });

  document.getElementById('formRenameItem').addEventListener('submit', async (e) => {
    e.preventDefault();
    const path = document.getElementById('renameItemPath').value;
    const newName = document.getElementById('renameItemNewName').value.trim();

    if (!newName) {
      showToast('Ingresa un nombre válido', true);
      return;
    }

    try {
      const res = await fetch('api.php?action=rename_item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ path, new_name: newName })
      });
      const data = await res.json();

      if (data.success) {
        document.getElementById('renameModal').classList.remove('active');
        showToast(data.message || 'Renombrado con éxito');
        loadDirectory(driveState.currentPath);
      } else {
        showToast(data.error || 'Error al renombrar', true);
      }
    } catch (err) {
      showToast('Error de conexión al renombrar', true);
    }
  });

  // =========================================================================
  // 9. SOFT DELETE / MOVE TO TRASH
  // =========================================================================
  async function moveToTrash(path, name) {
    if (!confirm(`¿Enviar "${name}" a la Papelera de Reciclaje?`)) {
      return;
    }

    try {
      const res = await fetch('api.php?action=move_to_trash', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ path })
      });
      const data = await res.json();

      if (data.success) {
        driveState.trashCount++;
        updateTrashBadgeCount(driveState.trashCount);
        showToast(data.message || 'Elemento movido a la papelera');
        loadDirectory(driveState.currentPath);
      } else {
        showToast(data.error || 'Error al mover a la papelera', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  }

  // =========================================================================
  // 10. RESTORE & PURGE TRASH ACTIONS
  // =========================================================================
  async function restoreFromTrash(id, name) {
    try {
      const res = await fetch('api.php?action=restore_item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();

      if (data.success) {
        showToast(data.message || `"${name}" restaurado con éxito`);
        loadTrashView();
      } else {
        showToast(data.error || 'Error al restaurar', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  }

  async function purgeFromTrash(id, name) {
    if (!confirm(`¿Estás seguro de eliminar permanentemente "${name}"?\nEsta acción no se puede deshacer.`)) {
      return;
    }

    try {
      const res = await fetch('api.php?action=purge_item', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();

      if (data.success) {
        showToast(data.message || 'Elemento eliminado permanentemente');
        loadTrashView();
      } else {
        showToast(data.error || 'Error al eliminar', true);
      }
    } catch (e) {
      showToast('Error de conexión', true);
    }
  }

  const btnEmptyTrash = document.getElementById('btnEmptyTrash');
  if (btnEmptyTrash) {
    btnEmptyTrash.addEventListener('click', async () => {
      if (!confirm('¿Deseas vaciar por completo la Papelera de Reciclaje?\nTodos los archivos se eliminarán de forma definitiva y no podrán recuperarse.')) {
        return;
      }

      try {
        const res = await fetch('api.php?action=empty_trash', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
          showToast(data.message || 'Papelera vaciada');
          loadTrashView();
        } else {
          showToast(data.error || 'Error al vaciar papelera', true);
        }
      } catch (e) {
        showToast('Error de conexión', true);
      }
    });
  }

  // =========================================================================
  // 11. DRAG & DROP MULTI-FILE UPLOADER
  // =========================================================================
  const dragOverlay = document.getElementById('dragDropOverlay');
  const targetNameEl = document.getElementById('dragDropTargetName');
  let dragCounter = 0;

  window.addEventListener('dragenter', (e) => {
    e.preventDefault();
    if (driveState.inTrashView) return;
    dragCounter++;
    if (targetNameEl) {
      targetNameEl.innerHTML = `Subiendo a: <strong>${escapeHtml(driveState.currentPath)}</strong>`;
    }
    dragOverlay.classList.add('active');
  });

  window.addEventListener('dragover', (e) => {
    e.preventDefault();
  });

  window.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dragCounter--;
    if (dragCounter <= 0) {
      dragCounter = 0;
      dragOverlay.classList.remove('active');
    }
  });

  window.addEventListener('drop', async (e) => {
    e.preventDefault();
    dragCounter = 0;
    dragOverlay.classList.remove('active');

    if (driveState.inTrashView) {
      showToast('No se pueden subir archivos dentro de la papelera', true);
      return;
    }

    if (!driveState.user.is_admin && driveState.user.role !== 'collab') {
      showToast('Tu cuenta no tiene permisos para subir archivos', true);
      return;
    }

    const files = e.dataTransfer.files;
    if (!files || files.length === 0) return;

    await uploadMultipleFiles(files);
  });

  async function uploadMultipleFiles(fileList) {
    const total = fileList.length;
    let uploadedCount = 0;

    for (let i = 0; i < total; i++) {
      const file = fileList[i];
      showToast(`Subiendo archivo ${i + 1} de ${total}: "${file.name}"...`);

      const fd = new FormData();
      fd.append('file', file);
      fd.append('target_path', driveState.currentPath);

      try {
        const res = await fetch('api.php?action=upload_file', {
          method: 'POST',
          body: fd
        });
        const data = await res.json();
        if (data.success) {
          uploadedCount++;
        } else {
          showToast(`Error al subir ${file.name}: ${data.error}`, true);
        }
      } catch (err) {
        showToast(`Error de conexión al subir ${file.name}`, true);
      }
    }

    if (uploadedCount > 0) {
      showToast(`¡${uploadedCount} archivo${uploadedCount === 1 ? '' : 's'} subido${uploadedCount === 1 ? '' : 's'} con éxito!`);
      loadDirectory(driveState.currentPath);
    }
  }

  // =========================================================================
  // 12. MANUAL FILE UPLOAD & NEW FOLDER BUTTONS
  // =========================================================================
  const btnUpload = document.getElementById('btnUploadFile');
  const fileInput = document.getElementById('driveFileInput');

  if (btnUpload && fileInput) {
    btnUpload.addEventListener('click', () => {
      fileInput.click();
    });

    fileInput.addEventListener('change', async () => {
      if (fileInput.files.length > 0) {
        await uploadMultipleFiles(fileInput.files);
        fileInput.value = '';
      }
    });
  }

  // =========================================================================
  // 12. NEW FOLDER MODAL
  // =========================================================================
  const btnNewFolder = document.getElementById('btnNewFolder');
  const folderModal = document.getElementById('folderModal');
  const btnCloseFolder = document.getElementById('btnCloseFolderModal');
  const formFolder = document.getElementById('formNewFolder');

  function openFolderModal() {
    if (!folderModal) return;
    folderModal.classList.add('show', 'active');
    document.getElementById('newFolderName').value = '';
    setTimeout(() => document.getElementById('newFolderName').focus(), 100);
  }

  function closeFolderModal() {
    if (!folderModal) return;
    folderModal.classList.remove('show', 'active');
  }

  if (btnNewFolder && folderModal) {
    btnNewFolder.addEventListener('click', openFolderModal);
    if (btnCloseFolder) btnCloseFolder.addEventListener('click', closeFolderModal);
    folderModal.addEventListener('click', (e) => {
      if (e.target === folderModal) closeFolderModal();
    });

    formFolder.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('newFolderName').value.trim();
      if (!name) return;

      try {
        const res = await fetch('api.php?action=create_folder', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, parent_path: driveState.currentPath })
        });
        const data = await res.json();

        if (data.success) {
          closeFolderModal();
          showToast('Carpeta creada con éxito');
          loadDirectory(driveState.currentPath);
        } else {
          showToast(data.error || 'Error al crear carpeta', true);
        }
      } catch (err) {
        showToast('Error de conexión', true);
      }
    });
  }

  // =========================================================================
  // 13. PREVIEW MODAL
  // =========================================================================
  function openPreview(file) {
    const modal = document.getElementById('previewModal');
    const title = document.getElementById('previewModalTitle');
    const body = document.getElementById('previewModalBody');
    const downloadBtn = document.getElementById('btnPreviewDownload');

    title.textContent = file.name;
    downloadBtn.href = `api.php?action=download&path=${encodeURIComponent(file.path)}`;

    const ext = file.ext;
    if (ext === 'pdf') {
      body.innerHTML = `<iframe src="api.php?action=view&path=${encodeURIComponent(file.path)}" class="preview-iframe"></iframe>`;
    } else if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
      body.innerHTML = `<img src="api.php?action=view&path=${encodeURIComponent(file.path)}" class="preview-img" alt="${escapeHtml(file.name)}">`;
    } else if (['mp4', 'mov'].includes(ext)) {
      body.innerHTML = `<video src="api.php?action=view&path=${encodeURIComponent(file.path)}" class="preview-video" controls autoplay></video>`;
    } else {
      body.innerHTML = `<iframe src="api.php?action=view&path=${encodeURIComponent(file.path)}" class="preview-iframe"></iframe>`;
    }

    modal.classList.add('show', 'active');
  }

  function closePreviewModal() {
    const modal = document.getElementById('previewModal');
    if (!modal) return;
    modal.classList.remove('show', 'active');
    document.getElementById('previewModalBody').innerHTML = '';
  }

  const btnClosePreview = document.getElementById('btnClosePreview');
  if (btnClosePreview) {
    btnClosePreview.addEventListener('click', closePreviewModal);
  }
  const previewModalEl = document.getElementById('previewModal');
  if (previewModalEl) {
    previewModalEl.addEventListener('click', (e) => {
      if (e.target === previewModalEl) closePreviewModal();
    });
  }

  // =========================================================================
  // 14. VIEW TOGGLE (GRID / LIST)
  // =========================================================================
  document.querySelectorAll('.btn-view-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.btn-view-toggle').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      driveState.currentView = btn.dataset.view;
      if (driveState.inTrashView) {
        renderTrashView();
      } else {
        renderExplorer();
      }
    });
  });

  // =========================================================================
  // 15. SEARCH INPUT
  // =========================================================================
  const searchInput = document.getElementById('driveSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      driveState.searchQuery = e.target.value;
      if (!driveState.inTrashView) {
        renderExplorer();
      }
    });
  }

  // =========================================================================
  // 16. SIDEBAR NAVIGATION
  // =========================================================================
  const navRoot = document.getElementById('sidebarNavRoot');
  if (navRoot) {
    navRoot.addEventListener('click', (e) => {
      e.preventDefault();
      loadDirectory('GlobalMarket');
    });
  }

  const navTrash = document.getElementById('sidebarNavTrash');
  if (navTrash) {
    navTrash.addEventListener('click', (e) => {
      e.preventDefault();
      loadTrashView();
    });
  }

  // =========================================================================
  // 17. USER MANAGEMENT & ROLES (SAAS TABS, CARDS & EDITING)
  // =========================================================================
  const btnManageUsers = document.getElementById('btnManageUsers');
  const sidebarBtnUsers = document.getElementById('sidebarBtnUsers');
  const usersModal = document.getElementById('usersModal');
  const btnCloseUsers = document.getElementById('btnCloseUsersModal');

  // User management state
  const userModuleState = {
    users: [],
    searchQuery: ''
  };

  function switchUserTab(targetTabId) {
    document.querySelectorAll('.modal-tab-btn').forEach(btn => {
      if (btn.dataset.tab === targetTabId) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    document.querySelectorAll('#usersModal .tab-pane').forEach(pane => {
      if (pane.id === targetTabId) {
        pane.classList.add('active');
      } else {
        pane.classList.remove('active');
      }
    });

    const editTabBtn = document.getElementById('tabBtnEdit');
    if (editTabBtn) {
      if (targetTabId === 'tabEditUser') {
        editTabBtn.style.display = 'flex';
      } else {
        editTabBtn.style.display = 'none';
      }
    }
  }

  function openUsersModal() {
    if (!usersModal) return;
    usersModal.classList.add('show', 'active');
    switchUserTab('tabUsersList');
    loadUsersList();
  }

  function closeUsersModal() {
    if (!usersModal) return;
    usersModal.classList.remove('show', 'active');
  }

  if (btnManageUsers) {
    btnManageUsers.addEventListener('click', (e) => {
      e.preventDefault();
      openUsersModal();
    });
  }

  if (sidebarBtnUsers) {
    sidebarBtnUsers.addEventListener('click', (e) => {
      e.preventDefault();
      openUsersModal();
    });
  }

  if (btnCloseUsers) {
    btnCloseUsers.addEventListener('click', closeUsersModal);
  }

  if (usersModal) {
    usersModal.addEventListener('click', (e) => {
      if (e.target === usersModal) closeUsersModal();
    });

    // Tab Navigation Buttons Click
    document.querySelectorAll('.modal-tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.dataset.tab;
        switchUserTab(targetTab);
      });
    });

    // Toolbar Buttons
    const btnGoToCreate = document.getElementById('btnGoToCreateUser');
    if (btnGoToCreate) {
      btnGoToCreate.addEventListener('click', () => switchUserTab('tabCreateUser'));
    }

    const btnCancelCreate = document.getElementById('btnCancelCreateUser');
    if (btnCancelCreate) {
      btnCancelCreate.addEventListener('click', () => switchUserTab('tabUsersList'));
    }

    const btnCancelEdit = document.getElementById('btnCancelEditUser');
    if (btnCancelEdit) {
      btnCancelEdit.addEventListener('click', () => switchUserTab('tabUsersList'));
    }

    // Search Input in Users List
    const searchUserInput = document.getElementById('searchUserInput');
    if (searchUserInput) {
      searchUserInput.addEventListener('input', (e) => {
        userModuleState.searchQuery = e.target.value.toLowerCase().trim();
        renderUsersList();
      });
    }

    // Create User Form Submit
    const formCreate = document.getElementById('formCreateUser');
    if (formCreate) {
      formCreate.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('newUserName').value.trim();
        const username = document.getElementById('newUserUsername').value.trim();
        const email = document.getElementById('newUserEmail').value.trim();
        const password = document.getElementById('newUserPassword').value.trim();
        const role = document.getElementById('newUserRole').value;

        try {
          const res = await fetch('api.php?action=create_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, username, email, password, role })
          });
          const data = await res.json();

          if (data.success) {
            showToast(data.message || 'Usuario creado exitosamente');
            formCreate.reset();
            switchUserTab('tabUsersList');
            loadUsersList();
          } else {
            showToast(data.error || 'Error al crear usuario', true);
          }
        } catch (err) {
          showToast('Error de conexión con el servidor', true);
        }
      });
    }

    // Edit User Form Submit
    const formEdit = document.getElementById('formEditUser');
    if (formEdit) {
      formEdit.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('editUserId').value;
        const name = document.getElementById('editUserName').value.trim();
        const username = document.getElementById('editUserUsername').value.trim();
        const email = document.getElementById('editUserEmail').value.trim();
        const password = document.getElementById('editUserPassword').value.trim();
        const role = document.getElementById('editUserRole').value;

        try {
          const res = await fetch('api.php?action=update_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, name, username, email, password, role })
          });
          const data = await res.json();

          if (data.success) {
            showToast(data.message || 'Usuario actualizado con éxito');
            document.getElementById('editUserPassword').value = '';
            switchUserTab('tabUsersList');
            loadUsersList();
          } else {
            showToast(data.error || 'Error al actualizar usuario', true);
          }
        } catch (err) {
          showToast('Error de conexión con el servidor', true);
        }
      });
    }
  }

  async function loadUsersList() {
    const container = document.getElementById('usersCardsContainer');
    if (!container) return;
    container.innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 2.5rem;"><i class="fa-solid fa-spinner fa-spin" style="font-size: 1.5rem; color: var(--gold-light);"></i><p style="margin-top: 0.5rem;">Cargando directorio de usuarios...</p></div>';

    try {
      const res = await fetch('api.php?action=get_users');
      const data = await res.json();

      if (data.success) {
        userModuleState.users = data.users || [];
        const badge = document.getElementById('usersCountBadge');
        if (badge) badge.textContent = userModuleState.users.length;
        renderUsersList();
      } else {
        container.innerHTML = `<div style="text-align: center; color: #ef4444; padding: 2rem;">${escapeHtml(data.error || 'Error al obtener usuarios')}</div>`;
      }
    } catch (e) {
      container.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 2rem;">Error de conexión con el servidor</div>';
    }
  }

  function renderUsersList() {
    const container = document.getElementById('usersCardsContainer');
    if (!container) return;

    let filtered = userModuleState.users;
    if (userModuleState.searchQuery) {
      const q = userModuleState.searchQuery;
      filtered = filtered.filter(u => 
        (u.name && u.name.toLowerCase().includes(q)) ||
        (u.username && u.username.toLowerCase().includes(q)) ||
        (u.email && u.email.toLowerCase().includes(q)) ||
        (u.role && u.role.toLowerCase().includes(q))
      );
    }

    if (filtered.length === 0) {
      container.innerHTML = `
        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
          <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; color: rgba(255, 255, 255, 0.15); margin-bottom: 0.75rem;"></i>
          <p style="font-size: 0.95rem; color: #e2e8f0;">No se encontraron usuarios</p>
          <small style="color: var(--text-muted);">Prueba con otros términos de búsqueda o registra un nuevo usuario</small>
        </div>
      `;
      return;
    }

    container.innerHTML = '';

    filtered.forEach(u => {
      const isMe = u.username === driveState.user.name || u.id === (driveState.user.id || '');
      const isSuper = u.role === 'superadmin';
      
      const roleBadges = {
        superadmin: '<span class="user-role-badge badge-superadmin"><i class="fa-solid fa-crown"></i> SUPERADMIN</span>',
        admin: '<span class="user-role-badge badge-admin"><i class="fa-solid fa-shield-halved"></i> Administrador</span>',
        collab: '<span class="user-role-badge badge-collab"><i class="fa-solid fa-pen-ruler"></i> Colaborador</span>',
        client: '<span class="user-role-badge badge-client"><i class="fa-solid fa-user"></i> Cliente</span>'
      };

      const avatarClasses = {
        superadmin: 'avatar-superadmin',
        admin: 'avatar-admin',
        collab: 'avatar-collab',
        client: 'avatar-client'
      };

      const initial = (u.name || u.username || 'U').charAt(0).toUpperCase();

      const card = document.createElement('div');
      card.className = 'user-item-card';

      card.innerHTML = `
        <div class="user-main-info">
          <div class="user-avatar-circle ${avatarClasses[u.role] || 'avatar-client'}">
            ${isSuper ? '<i class="fa-solid fa-crown" style="font-size: 1rem;"></i>' : initial}
          </div>
          <div class="user-meta-texts">
            <div class="user-title-line">
              <span class="user-name-text">${escapeHtml(u.name)}</span>
              <span class="user-username-badge">@${escapeHtml(u.username)}</span>
              ${roleBadges[u.role] || u.role}
            </div>
            <div class="user-contact-line">
              <span><i class="fa-solid fa-envelope" style="font-size: 0.7rem;"></i> ${escapeHtml(u.email || 'Sin correo asignado')}</span>
              <span>•</span>
              <span><i class="fa-solid fa-calendar-check" style="font-size: 0.7rem;"></i> Alta: ${u.created_at || '—'}</span>
            </div>
          </div>
        </div>

        <div class="user-card-actions">
          <button type="button" class="btn-card-edit" title="Editar datos y contraseña de este usuario">
            <i class="fa-solid fa-pen-to-square text-gold"></i> Editar
          </button>
          ${!isSuper && !isMe ? `
            <button type="button" class="btn-card-del" title="Eliminar este usuario">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          ` : ''}
        </div>
      `;

      // Event: Edit User
      card.querySelector('.btn-card-edit').addEventListener('click', () => {
        openEditUserForm(u);
      });

      // Event: Delete User
      const delBtn = card.querySelector('.btn-card-del');
      if (delBtn) {
        delBtn.addEventListener('click', () => {
          deleteUser(u.id, u.name);
        });
      }

      container.appendChild(card);
    });
  }

  function openEditUserForm(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name || '';
    document.getElementById('editUserUsername').value = user.username || '';
    document.getElementById('editUserEmail').value = user.email || '';
    document.getElementById('editUserPassword').value = '';
    document.getElementById('editUserRole').value = user.role || 'client';

    switchUserTab('tabEditUser');
    setTimeout(() => {
      document.getElementById('editUserName').focus();
    }, 100);
  }

  async function deleteUser(id, name) {
    if (!confirm(`¿Estás seguro de eliminar permanentemente al usuario "${name}"?\nEsta acción no se puede deshacer.`)) {
      return;
    }

    try {
      const res = await fetch('api.php?action=delete_user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await res.json();

      if (data.success) {
        showToast(data.message || 'Usuario eliminado con éxito');
        loadUsersList();
      } else {
        showToast(data.error || 'Error al eliminar usuario', true);
      }
    } catch (e) {
      showToast('Error de conexión al eliminar usuario', true);
    }
  }

  // Global Keydown for Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeFolderModal();
      closeRenameModal();
      closePreviewModal();
      closeUsersModal();
      closeAllDropdownMenus();
    }
  });

  // Escape HTML Helper
  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Initial Render
  renderBreadcrumbs();
  renderExplorer();
  renderSidebarFavorites();
});
