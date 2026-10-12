<?php
// Partial: Footer (Refactored to match Stitch Institutional Standard)
?>
<?php if (Auth::check()): ?>
<footer class="w-full bg-surface-container-lowest border-t border-surface-container mt-16 py-8">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-primary-container text-secondary flex items-center justify-center font-bold">
                <span class="material-symbols-outlined text-[18px]">anchor</span>
            </div>
            <div>
                <div class="font-bold text-xs text-primary leading-tight">Portal Hub Kelautan RI</div>
                <div class="text-[11px] text-on-surface-variant">Sistem Wrapper Micro-App Terpadu &bull; Standard ISO/IEC 27001 KKP</div>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs text-on-surface-variant font-medium">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px] text-secondary">verified_user</span>
                <span>Sandbox Runtime Strict</span>
            </span>
            <span class="hidden sm:inline text-outline-variant">•</span>
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px] text-secondary">speed</span>
                <span>Sub-500ms Clean Routing</span>
            </span>
            <span class="hidden sm:inline text-outline-variant">•</span>
            <span>&copy; <?= date('Y') ?> Kementerian Kelautan dan Perikanan</span>
        </div>
    </div>
</footer>
<?php endif; ?>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2"></div>

<script>
// User Menu Dropdown Handler
document.addEventListener('DOMContentLoaded', () => {
    const userBtn = document.getElementById('stitchUserBtn');
    const userDropdown = document.getElementById('stitchUserDropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && !userBtn.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }

    // Bookmark Toggle Ajax Handler
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            const appId = btn.getAttribute('data-app-id');
            const icon = btn.querySelector('.material-symbols-outlined');

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
                        btn.classList.add('text-secondary');
                        btn.classList.remove('text-on-surface-variant');
                        if (icon) icon.style.fontVariationSettings = "'FILL' 1";
                        showStitchToast('Aplikasi ditambahkan ke favorit!', 'check_circle');
                    } else {
                        btn.classList.remove('text-secondary');
                        btn.classList.add('text-on-surface-variant');
                        if (icon) icon.style.fontVariationSettings = "'FILL' 0";
                        showStitchToast('Aplikasi dihapus dari favorit.', 'info');

                        if (window.location.search.includes('view=favorites')) {
                            const cardItem = btn.closest('.app-card');
                            if (cardItem) cardItem.remove();
                        }
                    }
                }
            } catch (err) {
                console.error('Bookmark error:', err);
            }
        });
    });
});

function showStitchToast(message, iconName = 'check_circle') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'flex items-center gap-2.5 px-4 py-3 bg-surface-container-lowest border border-surface-container shadow-lg rounded-xl text-on-surface text-xs font-semibold transform transition-all duration-300';
    toast.innerHTML = `<span class="material-symbols-outlined text-[18px] text-secondary">${iconName}</span><span>${message}</span>`;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}
</script>
</body>
</html>
