<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

// Statistics
$total_aspirasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM aspirasi"))['c'];
$menunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM aspirasi WHERE status = 'Menunggu'"))['c'];
$proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM aspirasi WHERE status = 'Proses'"))['c'];
$selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM aspirasi WHERE status = 'Selesai'"))['c'];

// Category distribution for Chart
$cat_query = mysqli_query($conn, "SELECT k.nama_kategori, COUNT(a.id_aspirasi) as count 
                                  FROM kategori k 
                                  LEFT JOIN aspirasi a ON k.id_kategori = a.id_kategori 
                                  GROUP BY k.id_kategori");
$cat_labels = [];
$cat_data = [];
while ($cat_row = mysqli_fetch_assoc($cat_query)) {
    $cat_labels[] = $cat_row['nama_kategori'];
    $cat_data[] = $cat_row['count'];
}

// Recent reports (limit 8)
$latest = mysqli_query($conn, "SELECT a.*, u.nama, k.nama_kategori 
                                FROM aspirasi a 
                                LEFT JOIN users u ON a.id_user = u.id_user 
                                LEFT JOIN kategori k ON a.id_kategori = k.id_kategori
                                ORDER BY a.tanggal DESC LIMIT 8");

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="mesh-gradient"></div>

<main class="main-content">

    <!-- ── Top Bar ── -->
    <div class="top-bar" data-aos="fade-down">
        <div class="top-bar-left">
            <h1 style="font-weight: 800; letter-spacing: -0.02em;">Dashboard Admin</h1>
            <p style="color: var(--text-muted);">Selamat datang kembali, <strong style="color: var(--primary);"><?php echo htmlspecialchars($_SESSION['user_nama']); ?></strong> 👋</p>
        </div>
        <div class="top-bar-right">
            <a href="aspirasi.php" class="btn btn-primary" style="border-radius: 12px; padding: 12px 24px; font-weight: 700;">
                <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i> Semua Pengaduan
            </a>
        </div>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="stat-grid">

        <div class="stat-card" data-aos="fade-up" data-aos-delay="100" style="--card-accent: var(--primary); border: 1px solid rgba(79,70,229,0.1); background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Total Pengaduan</div>
                    <div class="stat-num" style="color:var(--primary); font-size: 2.2rem;"><?php echo $total_aspirasi; ?></div>
                </div>
                <div class="stat-icon" style="background:var(--primary-soft); color:var(--primary); width: 56px; height: 56px; border-radius: 16px;">
                    <i class="fas fa-box-archive"></i>
                </div>
            </div>
            <div class="stat-trend" style="background:var(--primary-soft); color:var(--primary); font-weight: 600; border-radius: 8px;">
                <i class="fas fa-arrow-trend-up"></i> Semua laporan
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="200" style="--card-accent: var(--warning); border: 1px solid rgba(245,158,11,0.1); background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Menunggu Respons</div>
                    <div class="stat-num" style="color:var(--warning); font-size: 2.2rem;"><?php echo $menunggu; ?></div>
                </div>
                <div class="stat-icon" style="background:var(--warning-soft); color:var(--warning); width: 56px; height: 56px; border-radius: 16px; <?php echo $menunggu > 0 ? 'animation: glow 2s infinite;' : ''; ?>">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-trend" style="background:var(--warning-soft); color:#92400e; font-weight: 600; border-radius: 8px;">
                <i class="fas fa-circle-dot"></i> Perlu tanggapan
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="300" style="--card-accent: var(--info); border: 1px solid rgba(59,130,246,0.1); background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Dalam Proses</div>
                    <div class="stat-num" style="color:var(--info); font-size: 2.2rem;"><?php echo $proses; ?></div>
                </div>
                <div class="stat-icon" style="background:var(--info-soft); color:var(--info); width: 56px; height: 56px; border-radius: 16px;">
                    <i class="fas fa-arrows-rotate fa-spin-slow"></i>
                </div>
            </div>
            <div class="stat-trend" style="background:var(--info-soft); color:#1e40af; font-weight: 600; border-radius: 8px;">
                <i class="fas fa-spinner"></i> Sedang dikerjakan
            </div>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="400" style="--card-accent: var(--success); border: 1px solid rgba(16,185,129,0.1); background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
            <div class="stat-card-top">
                <div>
                    <div class="stat-label">Selesai Ditangani</div>
                    <div class="stat-num" style="color:var(--success); font-size: 2.2rem;"><?php echo $selesai; ?></div>
                </div>
                <div class="stat-icon" style="background:var(--success-soft); color:var(--success); width: 56px; height: 56px; border-radius: 16px;">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div class="stat-trend" style="background:var(--success-soft); color:#065f46; font-weight: 600; border-radius: 8px;">
                <i class="fas fa-check"></i> Berhasil diatasi
            </div>
        </div>

    </div>

    <!-- ── Two Column: Charts and Progress ── -->
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; margin-bottom: 24px;">
        
        <!-- Chart Section -->
        <div class="card" data-aos="zoom-in" data-aos-delay="500" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);">
            <div class="card-header">
                <div>
                    <div class="card-title">Statistik Laporan per Kategori</div>
                    <div class="card-subtitle">Visualisasi distribusi pengaduan masuk</div>
                </div>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="card" style="animation: scaleIn 0.6s ease both 0.6s; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);">
            <div class="card-header">
                <div>
                    <div class="card-title">Ringkasan Status</div>
                    <div class="card-subtitle">Persentase pengerjaan laporan</div>
                </div>
            </div>
            <?php
$total = max($total_aspirasi, 1);
$pct_tunggu = round($menunggu / $total * 100);
$pct_proses = round($proses / $total * 100);
$pct_selesai = round($selesai / $total * 100);
?>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.85rem; font-weight: 700;">
                        <span style="color:#92400e; display: flex; align-items: center; gap: 6px;"><i class="fas fa-clock"></i> Menunggu</span>
                        <span><?php echo $pct_tunggu; ?>%</span>
                    </div>
                    <div style="height:10px;background:#f1f5f9;border-radius:50px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo $pct_tunggu; ?>%;background:var(--warning);border-radius:50px;transition:width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.85rem; font-weight: 700;">
                        <span style="color:#1e40af; display: flex; align-items: center; gap: 6px;"><i class="fas fa-spinner"></i> Proses</span>
                        <span><?php echo $pct_proses; ?>%</span>
                    </div>
                    <div style="height:10px;background:#f1f5f9;border-radius:50px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo $pct_proses; ?>%;background:var(--info);border-radius:50px;transition:width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:.85rem; font-weight: 700;">
                        <span style="color:#065f46; display: flex; align-items: center; gap: 6px;"><i class="fas fa-circle-check"></i> Selesai</span>
                        <span><?php echo $pct_selesai; ?>%</span>
                    </div>
                    <div style="height:10px;background:#f1f5f9;border-radius:50px;overflow:hidden;">
                        <div style="height:100%;width:<?php echo $pct_selesai; ?>%;background:var(--success);border-radius:50px;transition:width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recent Reports Table ── -->
    <div class="card" style="animation: fadeInUp 0.8s ease both 0.7s; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); border-radius: 20px;">
        <div class="card-header">
            <div>
                <div class="card-title">Aktivitas Terbaru</div>
                <div class="card-subtitle">Laporan terkini yang memerlukan perhatian Anda</div>
            </div>
            <a href="aspirasi.php" class="btn btn-ghost btn-sm" style="border-radius: 10px;">
                Lihat Semua <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Judul Laporan</th>
                        <th>Pelapor</th>
                        <th>Foto</th>
                        <th>Status</th>
                        <th style="text-align:right; padding-right: 20px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
$no = 1;
while ($row = mysqli_fetch_assoc($latest)):
    $status = $row['status'];
    if ($status === 'Menunggu') {
        $badge = 'badge-warning';
        $icon = 'fa-clock';
    }
    elseif ($status === 'Proses') {
        $badge = 'badge-primary';
        $icon = 'fa-spinner';
    }
    else {
        $badge = 'badge-success';
        $icon = 'fa-circle-check';
    }
?>
                    <tr>
                        <td class="text-muted" style="font-size:.8rem; padding-left: 20px;"><?php echo $no++; ?></td>
                        <td class="text-muted" style="white-space:nowrap;font-size:.82rem;">
                            <i class="fas fa-calendar fa-xs" style="margin-right:6px; opacity: 0.5;"></i>
                            <?php echo date('d M Y', strtotime($row['tanggal'])); ?>
                        </td>
                        <td>
                            <span style="background:var(--primary-soft);color:var(--primary);padding:3px 10px;border-radius:50px;font-size:.75rem;font-weight:600;">
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-weight:700; color:var(--text);"><?php echo htmlspecialchars($row['judul']); ?></span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:10px;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;">
                                    <?php echo strtoupper(substr($row['nama'] ?? 'P', 0, 1)); ?>
                                </div>
                                <span style="font-size:.875rem; font-weight: 500;"><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($row['foto'])): ?>
                                <span class="badge badge-success"><i class="fas fa-image"></i></span>
                            <?php
    else: ?>
                                <span class="text-muted">—</span>
                            <?php
    endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $badge; ?>" style="border-radius: 8px; padding: 6px 12px;">
                                <i class="fas <?php echo $icon; ?>" style="margin-right: 4px;"></i>
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td style="text-align:right; padding-right: 20px; display:flex; gap:8px; justify-content:flex-end;">
                            <a href="detail_aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>" class="btn btn-sm btn-primary" style="border-radius: 8px; padding: 8px 16px;">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <button onclick="confirmDelete(<?php echo $row['id_aspirasi']; ?>)" class="btn btn-sm" style="background:var(--danger-soft);color:var(--danger); border-radius: 8px; padding: 8px 12px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php
endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Gradient for chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.8)');
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                label: 'Jumlah Laporan',
                data: <?php echo json_encode($cat_data); ?>,
                backgroundColor: gradient,
                borderColor: 'v(--primary)',
                borderWidth: 0,
                borderRadius: 10,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: 'rgba(0,0,0,0.03)' },
                    ticks: { font: { family: 'Inter', size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11, weight: 'bold' } }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin menghapus?',
        text: "Laporan yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `aspirasi.php?delete=${id}`;
        }
    });
}
</script>

<style>
.fa-spin-slow {
    animation: fa-spin 3s linear infinite;
}
.tbl tr:hover {
    background: rgba(79, 70, 229, 0.02) !important;
}
.stat-card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-color: var(--card-accent) !important;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
