<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/functions.php';

check_role('siswa');

$user_id = $_SESSION['user_id'];
$user_nama = $_SESSION['user_nama'];

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_user=$user_id"))['c'];
$tunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_user=$user_id AND status='Menunggu'"))['c'];
$proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_user=$user_id AND status='Proses'"))['c'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_user=$user_id AND status='Selesai'"))['c'];

// Latest 5
$latest = mysqli_query($conn, "SELECT a.*, k.nama_kategori
    FROM aspirasi a JOIN kategori k ON a.id_kategori=k.id_kategori
    WHERE a.id_user=$user_id ORDER BY a.tanggal DESC LIMIT 5");

// Last response to show
$last_resp = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ub.respon, ub.tanggal_respon, a.judul, a.status
     FROM umpan_balik ub
     JOIN aspirasi a ON ub.id_aspirasi=a.id_aspirasi
     WHERE a.id_user=$user_id ORDER BY ub.tanggal_respon DESC LIMIT 1"));

$hour = (int)date('H');
$greeting = $hour < 11 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
?>

<div class="mesh-gradient"></div>

<style>
/* ─── Flash ─── */
.flash { display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:500; }
.flash.success { background:var(--success-soft);color:#065f46;border:1px solid #a7f3d0; }
.flash.error   { background:var(--danger-soft); color:#7f1d1d; border:1px solid #fecaca; }

/* ─── Hero banner ─── */
.hero-banner {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #818cf8 100%);
    border-radius: 24px; padding: 48px 40px;
    color: white; margin-bottom: 32px;
    position: relative; overflow: hidden;
    box-shadow: 0 20px 40px rgba(79, 70, 229, 0.2);
}
.hero-banner::before {
    content: '';
    position: absolute; top: -60px; right: -60px;
    width: 260px; height: 260px; border-radius: 50%;
    background: rgba(255,255,255,.07);
    animation: float 6s ease-in-out infinite;
}
.hero-banner::after {
    content: '';
    position: absolute; bottom: -80px; right: 120px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,.05);
    animation: float 8s ease-in-out infinite reverse;
}
.hero-greeting { font-size:.9rem; opacity:.8; margin-bottom:8px; letter-spacing:.04em; animation: fadeInUp 0.5s ease both 0.2s; }
.hero-name     { font-size:2.4rem; font-weight:800; margin-bottom:12px; font-family:'Plus Jakarta Sans',sans-serif; line-height:1.2; animation: fadeInUp 0.5s ease both 0.3s; }
.hero-sub      { font-size:.95rem; opacity:.85; margin-bottom:28px; max-width:520px; line-height:1.7; animation: fadeInUp 0.5s ease both 0.4s; }
.hero-actions  { display:flex; gap:16px; flex-wrap:wrap; position:relative; z-index:1; animation: fadeInUp 0.5s ease both 0.5s; }
.hero-btn {
    display:inline-flex; align-items:center; gap:10px;
    padding:13px 26px; border-radius:14px;
    font-size:.9rem; font-weight:700; cursor:pointer;
    text-decoration:none; transition:all .3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.hero-btn-white { background:white; color:#4f46e5; }
.hero-btn-white:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(0,0,0,.15), 0 0 20px rgba(255,255,255,0.4); }
.hero-btn-glass { background:rgba(255,255,255,.12); color:white; border:1.5px solid rgba(255,255,255,.2); backdrop-filter: blur(8px); }
.hero-btn-glass:hover { background:rgba(255,255,255,.2); transform:translateY(-2px); border-color: rgba(255,255,255,0.4); }

/* ─── Quick action cards ─── */
.quick-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:32px; }
.quick-card {
    background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.5); border-radius: 20px;
    padding: 24px; text-decoration: none; color: inherit;
    display: flex; align-items: center; gap: 18px;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}
.quick-card:hover { border-color: var(--primary); transform: translateY(-8px) scale(1.02); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
.quick-icon { width:52px; height:52px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.quick-label { font-size:.85rem; color:var(--text-muted); margin-bottom:4px; font-weight: 500; }
.quick-val   { font-size:1.8rem; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif; line-height:1; }

/* ─── Notification card ─── */
.notif-card {
    background: rgba(255, 251, 235, 0.8);
    backdrop-filter: blur(10px);
    border: 1.5px solid #fde68a;
    border-radius: 20px; padding: 20px 24px;
    display: flex; align-items: flex-start; gap: 16px;
    margin-bottom: 32px;
    animation: scaleIn 0.6s ease both 0.9s;
    position: relative; overflow: hidden;
}
.notif-card::before {
    content: ''; position: absolute; top:0; left:0; width:4px; height:100%; background: #f59e0b;
}
.notif-icon { width:44px;height:44px;border-radius:12px;background:#f59e0b;color:white;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0; }
</style>

<main class="main-content">

    <!-- Top Bar -->
    <div class="top-bar" style="animation: fadeInUp 0.5s ease both;">
        <div class="top-bar-left">
            <h1 style="font-weight: 800; letter-spacing: -0.02em;">Beranda Siswa</h1>
            <p style="color: var(--text-muted);">Pantau dan kelola laporan pengaduan Anda</p>
        </div>
        <div class="top-bar-right">
            <a href="lapor.php" class="btn btn-primary" style="border-radius: 12px; padding: 12px 24px; font-weight: 700;">
                <i class="fas fa-plus" style="margin-right: 8px;"></i> Buat Laporan
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
    <div class="flash <?php echo $_SESSION['flash']['type']; ?>">
        <i class="fas fa-circle-check"></i> <?php echo $_SESSION['flash']['message'];
    unset($_SESSION['flash']); ?>
    </div>
    <?php
endif; ?>

    <!-- ── Hero Banner ── -->
    <div class="hero-banner">
        <div class="hero-greeting"><i class="fas fa-sun" style="color: #fde047;"></i> &nbsp;<?php echo $greeting; ?></div>
        <div class="hero-name"><?php echo htmlspecialchars($user_nama); ?>! 👋</div>
        <div class="hero-sub">
            Punya keluhan sarana sekolah? Laporkan sekarang dan pantau perkembangannya secara real-time melalui dashboard interaktif Anda.
        </div>
        <div class="hero-actions">
            <a href="lapor.php" class="hero-btn hero-btn-white">
                <i class="fas fa-paper-plane"></i> Kirim Laporan Baru
            </a>
            <a href="riwayat.php" class="hero-btn hero-btn-glass">
                <i class="fas fa-list-check"></i> Lihat Riwayat
            </a>
        </div>
    </div>

    <!-- ── Quick Stats (clickable) ── -->
    <div class="quick-grid">
        <a href="riwayat.php" class="quick-card" style="animation: fadeInUp 0.6s ease both 0.6s;">
            <div class="quick-icon" style="background:var(--primary-soft);color:var(--primary);">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="quick-label">Total Laporan</div>
                <div class="quick-val" style="color:var(--primary);"><?php echo $total; ?></div>
            </div>
        </a>
        <a href="riwayat.php?status=Menunggu" class="quick-card" style="animation: fadeInUp 0.6s ease both 0.7s;">
            <div class="quick-icon" style="background:var(--warning-soft);color:var(--warning); <?php echo $tunggu > 0 ? 'animation: glow 2s infinite;' : ''; ?>">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="quick-label">Menunggu</div>
                <div class="quick-val" style="color:var(--warning);"><?php echo $tunggu; ?></div>
            </div>
        </a>
        <a href="riwayat.php?status=Proses" class="quick-card" style="animation: fadeInUp 0.6s ease both 0.8s;">
            <div class="quick-icon" style="background:var(--info-soft);color:var(--info);">
                <i class="fas fa-spinner-third fa-spin-slow"></i>
            </div>
            <div>
                <div class="quick-label">Diproses</div>
                <div class="quick-val" style="color:var(--info);"><?php echo $proses; ?></div>
            </div>
        </a>
    </div>

    <!-- ── Notifikasi balasan terbaru ── -->
    <?php if ($last_resp): ?>
    <div class="notif-card">
        <div class="notif-icon"><i class="fas fa-envelope-open-text"></i></div>
        <div style="flex:1;">
            <div style="font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:#92400e; margin-bottom:6px;">
                <span style="background:#fde68a; padding: 2px 8px; border-radius: 4px;">Update Terbaru</span>
            </div>
            <div style="font-weight:700; margin-bottom:6px; font-size:1rem; color:#1c1917;">
                "<?php echo htmlspecialchars($last_resp['judul']); ?>"
            </div>
            <div style="font-size:.9rem; color:#57534e; line-height:1.6;">
                <?php echo htmlspecialchars(mb_substr($last_resp['respon'], 0, 150)) . (mb_strlen($last_resp['respon']) > 150 ? '...' : ''); ?>
            </div>
        </div>
        <div style="font-size:.8rem; color:#a8a29e; white-space:nowrap; text-align:right; font-weight: 600;">
            <i class="fas fa-calendar-day" style="margin-right: 4px;"></i> <?php echo date('d M Y', strtotime($last_resp['tanggal_respon'])); ?>
        </div>
    </div>
    <?php
endif; ?>

    <!-- ── 2 Kolom: Tabel Laporan + Ringkasan Status ── -->
    <div style="display:grid; grid-template-columns:1.8fr 1fr; gap:24px; align-items:start;">

        <!-- Laporan Terbaru -->
        <div class="card" style="animation: scaleIn 0.6s ease both 1s; border-radius: 20px;">
            <div class="card-header" style="margin-bottom:20px;">
                <div>
                    <div class="card-title">Aktivitas Laporan</div>
                    <div class="card-subtitle">Riwayat pengajuan terkini</div>
                </div>
                <a href="riwayat.php" class="btn btn-ghost btn-sm" style="border-radius: 10px;">Semua <i class="fas fa-arrow-right" style="margin-left: 6px;"></i></a>
            </div>

            <?php if (mysqli_num_rows($latest) > 0): ?>
            <div style="display:flex; flex-direction:column; gap:0;">
                <?php while ($row = mysqli_fetch_assoc($latest)):
        if ($row['status'] === 'Menunggu') {
            $bc = 'badge-warning';
            $bi = 'fa-clock';
        }
        elseif ($row['status'] === 'Proses') {
            $bc = 'badge-primary';
            $bi = 'fa-spinner';
        }
        else {
            $bc = 'badge-success';
            $bi = 'fa-circle-check';
        }
?>
                <div style="display:flex;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid #f1f5f9; transition: background 0.2s;" class="report-row">
                    <div style="width:42px;height:42px;border-radius:12px;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;">
                        <i class="fas fa-file-lines"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;font-size:.95rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; color: var(--text);" title="<?php echo htmlspecialchars($row['judul']); ?>">
                            <?php echo htmlspecialchars($row['judul']); ?>
                        </div>
                        <div style="font-size:.8rem;color:var(--text-muted);margin-top:2px; font-weight: 500;">
                            <span style="color: var(--primary);"><?php echo htmlspecialchars($row['nama_kategori']); ?></span> · <i class="fas fa-calendar-day fa-xs" style="margin-right:2px; opacity:0.5;"></i> <?php echo date('d M Y', strtotime($row['tanggal'])); ?>
                        </div>
                    </div>
                    <span class="badge <?php echo $bc; ?>" style="flex-shrink:0; border-radius: 8px; padding: 6px 12px;">
                        <i class="fas <?php echo $bi; ?>" style="margin-right: 4px;"></i> <?php echo $row['status']; ?>
                    </span>
                </div>
                <?php
    endwhile; ?>
            </div>
            <?php
else: ?>
            <div style="text-align:center;padding:48px 20px;color:var(--text-muted);">
                <div style="width:80px;height:80px;background:var(--bg);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-inbox" style="font-size:2rem; opacity:.3;"></i>
                </div>
                <p style="font-weight:700;margin-bottom:6px; color:var(--text);">Belum ada laporan</p>
                <p style="font-size:.85rem;margin-bottom:20px;">Anda belum pernah mengirimkan laporan pengaduan.</p>
                <a href="lapor.php" class="btn btn-primary" style="border-radius: 12px;"><i class="fas fa-plus"></i> Buat Laporan Pertama</a>
            </div>
            <?php
endif; ?>
        </div>

        <!-- Ringkasan Status -->
        <div style="display:flex;flex-direction:column;gap:20px;">
            <!-- Progress bars card -->
            <div class="card" style="animation: scaleIn 0.6s ease both 1.1s; border-radius: 20px;">
                <div class="card-title" style="margin-bottom:20px; font-weight: 800;">📊 Statistik Saya</div>

                <?php
$pct_t = $total > 0 ? round($tunggu / $total * 100) : 0;
$pct_p = $total > 0 ? round($proses / $total * 100) : 0;
$pct_s = $total > 0 ? round($selesai / $total * 100) : 0;
$stats_bar = [
    ['label' => 'Selesai', 'val' => $selesai, 'pct' => $pct_s, 'color' => 'var(--success)', 'bg' => 'var(--success-soft)'],
    ['label' => 'Proses', 'val' => $proses, 'pct' => $pct_p, 'color' => 'var(--info)', 'bg' => 'var(--info-soft)'],
    ['label' => 'Menunggu', 'val' => $tunggu, 'pct' => $pct_t, 'color' => 'var(--warning)', 'bg' => 'var(--warning-soft)'],
];
foreach ($stats_bar as $sb): ?>
                <div style="margin-bottom:18px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;font-size:.85rem; font-weight: 700;">
                        <span><?php echo $sb['label']; ?></span>
                        <span style="color:<?php echo $sb['color']; ?>;"><?php echo $sb['val']; ?> (<?php echo $sb['pct']; ?>%)</span>
                    </div>
                    <div style="height:10px;background:#f1f5f9;border-radius:50px;overflow:hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                        <div style="height:100%;width:<?php echo $sb['pct']; ?>%;background:<?php echo $sb['color']; ?>;border-radius:50px;transition:width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                    </div>
                </div>
                <?php
endforeach; ?>
            </div>

            <!-- Tips card -->
            <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1.5px solid #bae6fd;border-radius:20px;padding:24px; animation: scaleIn 0.6s ease both 1.2s;">
                <div style="font-size:.8rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#0369a1;margin-bottom:12px; display:flex; align-items:center;">
                    <div style="width:28px; height:28px; background:white; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-right:10px; color:#0ea5e9;">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    Tips Pengaduan
                </div>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:12px;">
                    <li style="font-size:.85rem;color:#0c4a6e;display:flex;gap:10px;align-items:flex-start; line-height: 1.5;">
                        <i class="fas fa-camera" style="color:#0ea5e9;margin-top:3px;flex-shrink:0;"></i>
                        Sertakan foto detail agar tim sarana lebih cepat memahami masalah.
                    </li>
                    <li style="font-size:.85rem;color:#0c4a6e;display:flex;gap:10px;align-items:flex-start; line-height: 1.5;">
                        <i class="fas fa-pen-to-square" style="color:#0ea5e9;margin-top:3px;flex-shrink:0;"></i>
                        Gunakan judul yang singkat namun sangat deskriptif.
                    </li>
                </ul>
            </div>
        </div>
    </div>

</main>

<style>
.report-row:hover {
    background: rgba(79, 70, 229, 0.03);
    cursor: default;
}
.fa-spin-slow {
    animation: fa-spin 3s linear infinite;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
