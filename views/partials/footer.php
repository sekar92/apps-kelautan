<?php
// Partial: Footer
?>
<?php if (Auth::check()): ?>
<footer style="border-top: 1px solid var(--border-glass); padding: 32px 0 24px; margin-top: 60px; background: rgba(10, 16, 30, 0.6);">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 28px; height: 28px; border-radius: 6px; background: linear-gradient(135deg, #06b6d4, #0284c7); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 13px;">
                <i class="fa-solid fa-anchor"></i>
            </div>
            <div>
                <div style="font-weight: 700; color: #fff; font-size: 13px;">Portal Hub Aplikasi Kelautan</div>
                <div style="font-size: 11px; color: var(--text-muted);">Sistem Wrapper SPA & GAS Terpadu &bull; v1.0 Production</div>
            </div>
        </div>

        <div style="display: flex; gap: 20px; font-size: 12px; color: var(--text-muted);">
            <span><i class="fa-solid fa-shield-halved" style="color: #10b981;"></i> Sandboxed iFrame NFR 1</span>
            <span><i class="fa-solid fa-bolt" style="color: #06b6d4;"></i> Ultra Low-latency Routing</span>
            <span><i class="fa-solid fa-database" style="color: #f59e0b;"></i> MariaDB / SQLite Ready</span>
        </div>
    </div>
</footer>
<?php endif; ?>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Custom Client Script -->
<script src="<?= url('/assets/js/main.js') ?>"></script>
</body>
</html>
