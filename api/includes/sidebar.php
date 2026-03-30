<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['user_role'] ?? '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand" style="justify-content: space-between; padding-right: 14px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <div class="brand-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="brand-text">
                <strong>Sistem Pengaduan</strong>
                <small>Sarana Sekolah</small>
            </div>
        </div>
        <!-- Hanya terlihat di setting mobile jika ada custom CSS, namun karena ruangnya sempit kita biarkan saja flex -->
        <button class="close-sidebar-btn" onclick="toggleSidebar()" style="background:none; border:none; padding:4px; font-size:1.4rem; color:var(--text-muted); cursor:pointer; display:none;" id="btnCloseSidebar"><i class="fas fa-times"></i></button>
    </div>
    <style>
        @media (max-width: 900px) {
            #btnCloseSidebar { display: block !important; }
        }
    </style>

    <nav style="padding: 12px 0; flex: 1; display: flex; flex-direction: column;">
        <?php if ($role == 'admin'): ?>
            <div class="sidebar-section">Menu Utama</div>
            <a href="dashboard.php" class="nav-link <?php echo($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="aspirasi.php" class="nav-link <?php echo($current_page == 'aspirasi.php' || $current_page == 'detail_aspirasi.php') ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i> Data Pengaduan
            </a>

            <div class="sidebar-section">Manajemen</div>
            <a href="users.php" class="nav-link <?php echo($current_page == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Pengguna
            </a>
            <a href="kategori.php" class="nav-link <?php echo($current_page == 'kategori.php') ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori
            </a>

            <div class="sidebar-section">Laporan</div>
            <a href="laporan.php" class="nav-link <?php echo($current_page == 'laporan.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-chart-column"></i> Laporan & Statistik
            </a>

        <?php
else: ?>
            <div class="sidebar-section">Menu</div>
            <a href="dashboard.php" class="nav-link <?php echo($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-house"></i> Beranda
            </a>
            <a href="lapor.php" class="nav-link <?php echo($current_page == 'lapor.php') ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane"></i> Buat Laporan
            </a>
            <a href="riwayat.php" class="nav-link <?php echo($current_page == 'riwayat.php') ? 'active' : ''; ?>">
                <i class="fas fa-clock-rotate-left"></i> Riwayat Laporan
            </a>
        <?php
endif; ?>
    </nav>

    <div class="sidebar-footer">
        <!-- User info -->
        <div style="display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--bg); border-radius:10px; margin-bottom:8px;">
            <div class="avatar-circle" style="width:32px;height:32px;font-size:.78rem;">
                <?php echo strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1)); ?>
            </div>
            <div style="overflow:hidden;">
                <div style="font-size:.82rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    <?php echo htmlspecialchars($_SESSION['user_nama'] ?? ''); ?>
                </div>
                <div style="font-size:.72rem;color:var(--text-muted);text-transform:capitalize;">
                    <?php echo htmlspecialchars($role); ?>
                </div>
            </div>
        </div>
        <a href="../logout.php" class="nav-link danger" id="logoutLink">
            <i class="fas fa-arrow-right-from-bracket"></i> Keluar
        </a>
    </div>
</aside>

<script>
document.getElementById('logoutLink').addEventListener('click', function(e) {
    e.preventDefault();
    const href = this.getAttribute('href');
    
    Swal.fire({
        title: 'Yakin ingin keluar?',
        text: "Anda akan mengakhiri sesi saat ini.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        background: 'rgba(255, 255, 255, 0.95)',
        backdrop: 'rgba(15, 23, 42, 0.4)'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = href;
        }
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.toggle('active');
    if (overlay) overlay.classList.toggle('active');
}
</script>
