/**
 * Portal Hub Aplikasi Kelautan - Frontend Interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
    initUserMenu();
    initSearchAndFilter();
    initBookmarks();
    initFlashAlerts();
});

// User dropdown menu
function initUserMenu() {
    const userBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('show');
        });
    }
}

// Live client-side instant search & category pill filtering
function initSearchAndFilter() {
    const searchInput = document.getElementById('appSearchInput');
    const appCards = document.querySelectorAll('.app-card-item');
    const emptyState = document.getElementById('catalogEmptyState');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            let visibleCount = 0;

            appCards.forEach(card => {
                const title = card.getAttribute('data-title')?.toLowerCase() || '';
                const desc = card.getAttribute('data-desc')?.toLowerCase() || '';
                const cat = card.getAttribute('data-cat')?.toLowerCase() || '';

                if (title.includes(query) || desc.includes(query) || cat.includes(query)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        });
    }
}

// AJAX Bookmark Toggle (FR 2.4)
function initBookmarks() {
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const appId = btn.getAttribute('data-app-id');
            const icon = btn.querySelector('i');

            try {
                const formData = new FormData();
                formData.append('app_id', appId);

                const response = await fetch(window.PORTAL_BASE_URL + '/api/bookmark', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    if (data.is_bookmarked) {
                        btn.classList.add('active');
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        showToast('⭐ Disimpan ke aplikasi favorit!');
                    } else {
                        btn.classList.remove('active');
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        showToast('Dihapus dari aplikasi favorit.');
                        
                        // If we are on favorites view, smoothly hide card
                        if (window.location.search.includes('view=favorites')) {
                            const cardItem = btn.closest('.app-card-item');
                            if (cardItem) {
                                cardItem.style.opacity = '0';
                                setTimeout(() => cardItem.remove(), 250);
                            }
                        }
                    }
                } else {
                    showToast(data.error || 'Gagal menyimpan bookmark.', 'error');
                }
            } catch (err) {
                console.error('Bookmark error:', err);
                showToast('Terjadi kesalahan saat memproses bookmark.', 'error');
            }
        });
    });
}

// Toast notification helper
function showToast(message, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #06b6d4;"></i> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Fullscreen toggle for iFrame Wrapper (FR 5.2)
function toggleAppFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.error('Error attempting to enable fullscreen:', err);
        });
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

// Copy URL to Clipboard
function copyCurrentUrl() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showToast('📋 Tautan aplikasi berhasil disalin ke clipboard!');
    }).catch(() => {
        showToast('Gagal menyalin tautan.', 'error');
    });
}

// Reload iframe
function reloadIframe() {
    const iframe = document.getElementById('appMainFrame');
    if (iframe) {
        const loader = document.getElementById('iframeLoader');
        if (loader) loader.style.display = 'flex';
        iframe.src = iframe.src;
    }
}

// Hide flash alerts automatically
function initFlashAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

// Generic Modal triggers
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('show');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('show');
    }
}
