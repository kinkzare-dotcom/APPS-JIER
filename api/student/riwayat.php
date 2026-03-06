<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/functions.php';

check_role('siswa');

$user_id = $_SESSION['user_id'];
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$where = "WHERE a.id_user = '$user_id'";
if ($filter_status)
    $where .= " AND a.status='$filter_status'";

$result = mysqli_query($conn, "SELECT a.*, k.nama_kategori
    FROM aspirasi a JOIN kategori k ON a.id_kategori=k.id_kategori
    $where ORDER BY a.tanggal DESC");

$total_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_user=$user_id"))['c'];
$total_show = mysqli_num_rows($result);
?>

<style>
.flash { display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:500; }
.flash.success { background:var(--success-soft);color:#065f46;border:1px solid #a7f3d0; }
.flash.error   { background:var(--danger-soft); color:#7f1d1d; border:1px solid #fecaca; }

.status-filter { display:flex;gap:8px;flex-wrap:wrap; }
.sf-tab { padding:7px 16px;border-radius:50px;font-size:.82rem;font-weight:600;text-decoration:none;border:1.5px solid var(--border);color:var(--text-muted);background:var(--white);transition:var(--transition); }
.sf-tab:hover { border-color:var(--primary);color:var(--primary);background:var(--primary-soft); }
.sf-tab.active { border-color:var(--primary);color:var(--primary);background:var(--primary-soft); }

/* detail slide panel */
.detail-panel { display:none;background:var(--bg);border-radius:12px;margin-top:12px;padding:16px;border:1px solid var(--border); }
.detail-panel.open { display:block; }
</style>

<div class="mesh-gradient"></div>

<main class="main-content" style="animation: scaleIn 0.6s ease both;">
    <div class="top-bar">
        <div class="top-bar-left">
            <h1>Riwayat Laporan Saya</h1>
            <p>Pantau status tindak lanjut dari laporan yang telah Anda kirim</p>
        </div>
        <div class="top-bar-right">
            <a href="lapor.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Laporan Baru
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

    <!-- Filter & Summary -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div class="status-filter">
            <a href="riwayat.php" class="sf-tab <?php echo !$filter_status ? 'active' : ''; ?>">
                Semua <span style="background:var(--primary);color:white;border-radius:50px;padding:1px 7px;font-size:.7rem;margin-left:4px;"><?php echo $total_all; ?></span>
            </a>
            <a href="?status=Menunggu" class="sf-tab <?php echo $filter_status === 'Menunggu' ? 'active' : ''; ?>"
               style="<?php echo $filter_status === 'Menunggu' ? 'border-color:var(--warning);color:#92400e;background:var(--warning-soft);' : ''; ?>">
                ⏳ Menunggu
            </a>
            <a href="?status=Proses" class="sf-tab <?php echo $filter_status === 'Proses' ? 'active' : ''; ?>"
               style="<?php echo $filter_status === 'Proses' ? 'border-color:var(--info);color:#1e40af;background:var(--info-soft);' : ''; ?>">
                🔄 Diproses
            </a>
            <a href="?status=Selesai" class="sf-tab <?php echo $filter_status === 'Selesai' ? 'active' : ''; ?>"
               style="<?php echo $filter_status === 'Selesai' ? 'border-color:var(--success);color:#065f46;background:var(--success-soft);' : ''; ?>">
                ✅ Selesai
            </a>
        </div>
        <div style="font-size:.85rem;color:var(--text-muted);">
            Menampilkan <strong><?php echo $total_show; ?></strong> laporan
        </div>
    </div>

    <div class="card">
        <?php if ($total_show > 0): ?>
        <div style="display:flex; flex-direction:column; gap:0;" id="laporan-list">
        <?php
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)):
        $id_asp = $row['id_aspirasi'];
        if ($row['status'] === 'Menunggu') {
            $bc = 'badge-warning';
            $bi = 'fa-clock';
            $accColor = 'var(--warning)';
        }
        elseif ($row['status'] === 'Proses') {
            $bc = 'badge-primary';
            $bi = 'fa-spinner';
            $accColor = 'var(--info)';
        }
        else {
            $bc = 'badge-success';
            $bi = 'fa-circle-check';
            $accColor = 'var(--success)';
        }

        // Get latest response
        $resp = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT ub.respon, ub.tanggal_respon, u.nama as nama_admin
                 FROM umpan_balik ub JOIN users u ON ub.id_user=u.id_user
                 WHERE ub.id_aspirasi=$id_asp ORDER BY ub.tanggal_respon DESC LIMIT 1"));
        $has_foto = $row['foto'] && file_exists('../uploads/' . $row['foto']);
?>
        <div style="padding:18px 0; border-bottom:1px solid #f1f5f9;" class="laporan-item">
            <div style="display:flex;align-items:flex-start;gap:14px;">
                <!-- Icon -->
                <div style="width:42px;height:42px;border-radius:12px;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;margin-top:2px;">
                    <i class="fas fa-file-lines"></i>
                </div>

                <!-- Content -->
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:700;font-size:.9rem;color:var(--text);margin-bottom:3px;">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </div>
                            <div style="font-size:.78rem;color:var(--text-muted);">
                                <span style="background:var(--primary-soft);color:var(--primary);padding:2px 8px;border-radius:50px;font-weight:600;margin-right:6px;"><?php echo htmlspecialchars($row['nama_kategori']); ?></span>
                                <i class="fas fa-calendar fa-xs"></i> <?php echo date('d M Y', strtotime($row['tanggal'])); ?>
                                <?php if ($has_foto): ?>
                                <span style="margin-left:6px;"><i class="fas fa-image fa-xs" style="color:var(--success);"></i> Foto</span>
                                <?php
        endif; ?>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                            <span class="badge <?php echo $bc; ?>"><i class="fas <?php echo $bi; ?>"></i> <?php echo $row['status']; ?></span>
                            <button onclick="toggleDetail(<?php echo $id_asp; ?>)"
                                style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.8rem;padding:4px 8px;border-radius:6px;border:1px solid var(--border);background:var(--bg);"
                                id="btn-<?php echo $id_asp; ?>">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Expandable detail -->
                    <div class="detail-panel" id="detail-<?php echo $id_asp; ?>">
                        <div style="font-size:.82rem;line-height:1.7;color:#475569;margin-bottom:10px;">
                            <?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?>
                        </div>
                        <?php if ($resp): ?>
                        <div style="background:white;border-radius:10px;padding:12px 14px;border:1px solid var(--border);margin-top:10px;">
                            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--primary);margin-bottom:6px;">
                                <i class="fas fa-reply" style="margin-right:5px;"></i>Balasan Admin — <?php echo htmlspecialchars($resp['nama_admin']); ?>
                                <span style="float:right;font-weight:400;text-transform:none;color:var(--text-muted);"><?php echo date('d M Y', strtotime($resp['tanggal_respon'])); ?></span>
                            </div>
                            <p style="font-size:.875rem;color:#334155;line-height:1.65;"><?php echo nl2br(htmlspecialchars($resp['respon'])); ?></p>
                        </div>
                        <?php
        else: ?>
                        <div style="font-size:.82rem;color:var(--text-muted);font-style:italic;margin-top:8px;">
                            <i class="fas fa-hourglass-half" style="margin-right:5px;"></i>Menunggu balasan dari admin...
                        </div>
                        <?php
        endif; ?>
                        <?php if ($has_foto): ?>
                        <div style="margin-top:12px;">
                            <img src="../uploads/<?php echo htmlspecialchars($row['foto']); ?>"
                                 alt="Foto bukti"
                                 style="max-width:260px;border-radius:10px;border:1px solid var(--border);cursor:zoom-in;"
                                 onclick="window.open(this.src,'_blank')">
                        </div>
                        <?php
        endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endwhile; ?>
        </div>

        <?php
else: ?>
        <div style="text-align:center;padding:60px 24px;color:var(--text-muted);">
            <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:16px;display:block;opacity:.25;"></i>
            <p style="font-weight:700;font-size:1.1rem;margin-bottom:6px;color:var(--text);">Belum ada laporan</p>
            <p style="font-size:.875rem;margin-bottom:20px;">
                <?php echo $filter_status ? "Tidak ada laporan dengan status \"$filter_status\"." : 'Anda belum pernah mengirimkan laporan.'; ?>
            </p>
            <a href="lapor.php" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Laporan Pertama</a>
        </div>
        <?php
endif; ?>
    </div>
</main>

<script>
function toggleDetail(id) {
    const panel = document.getElementById('detail-' + id);
    const btn   = document.getElementById('btn-' + id);
    const isOpen = panel.classList.contains('open');
    panel.classList.toggle('open', !isOpen);
    btn.querySelector('i').style.transform = isOpen ? '' : 'rotate(180deg)';
    btn.querySelector('i').style.transition = 'transform .3s ease';
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
