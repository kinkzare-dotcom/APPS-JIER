<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

$id = isset($_GET['id']) ? sanitize($_GET['id']) : '';
if (!$id) {
    header("Location: /admin/aspirasi.php");
    exit;
}

// Handle Update & Response
if (isset($_POST['submit_response'])) {
    $status = sanitize($_POST['status']);
    $respon = sanitize($_POST['respon']);
    $admin_id = $_SESSION['user_id'];

    mysqli_begin_transaction($conn);
    try {
        mysqli_query($conn, "UPDATE aspirasi SET status = '$status' WHERE id_aspirasi = '$id'");
        mysqli_query($conn, "INSERT INTO umpan_balik (id_aspirasi, id_user, respon) VALUES ('$id', '$admin_id', '$respon')");
        mysqli_commit($conn);
        $_SESSION['flash'] = ['message' => 'Tanggapan berhasil dikirim!', 'type' => 'success'];
    }
    catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['flash'] = ['message' => 'Gagal mengirim tanggapan.', 'type' => 'error'];
    }
    header("Location: /admin/detail_aspirasi.php?id=$id");
    exit;
}

// Get Data
$query = "SELECT a.*, u.nama as nama_siswa, k.nama_kategori
          FROM aspirasi a
          JOIN users u ON a.id_user = u.id_user
          JOIN kategori k ON a.id_kategori = k.id_kategori
          WHERE a.id_aspirasi = '$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: /admin/aspirasi.php");
    exit;
}

// Get Responses
$responses = mysqli_query($conn, "SELECT ub.*, u.nama as nama_admin
                                 FROM umpan_balik ub
                                 JOIN users u ON ub.id_user = u.id_user
                                 WHERE ub.id_aspirasi = '$id'
                                 ORDER BY ub.tanggal_respon ASC");

// Badge helper
function statusBadge($status)
{
    if ($status === 'Menunggu')
        return ['badge-warning', 'fa-clock', 'Menunggu'];
    if ($status === 'Proses')
        return ['badge-primary', 'fa-spinner', 'Proses'];
    return ['badge-success', 'fa-circle-check', 'Selesai'];
}
[$badgeClass, $badgeIcon, $badgeLabel] = statusBadge($data['status']);

$foto_data = $data['foto'] ?: null;
$foto_exists = !empty($foto_data);

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* ── Form styles ── */
.form-group   { margin-bottom: 18px; }
.form-label   { display: block; font-size: .8rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px; }
.form-control {
    width: 100%; padding: 11px 14px;
    border: 1.5px solid var(--border); border-radius: 10px;
    font-size: .9rem; font-family: inherit; color: var(--text);
    background: var(--white); transition: var(--transition);
}
.form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
textarea.form-control { resize: vertical; min-height: 120px; line-height: 1.6; }
select.form-control   { cursor: pointer; }

/* ── Flash messages ── */
.flash { display: flex; align-items: center; gap: 12px; padding: 14px 18px; border-radius: 12px; margin-bottom: 22px; font-size: .9rem; font-weight: 500; }
.flash.success { background: var(--success-soft); color: #065f46; border: 1px solid #a7f3d0; }
.flash.error   { background: var(--danger-soft);  color: #7f1d1d; border: 1px solid #fecaca; }

/* ── Info row ── */
.info-row { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: .9rem; }
.info-row:last-child { border-bottom: none; }
.info-row i { width: 18px; text-align: center; color: var(--primary); font-size: .85rem; }
.info-row .info-val { font-weight: 600; color: var(--text); }

/* ── Photo viewer ── */
.photo-wrap {
    position: relative; border-radius: 14px; overflow: hidden;
    border: 2px solid var(--border); background: #f8fafc;
    cursor: zoom-in; transition: var(--transition);
}
.photo-wrap:hover { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,.1); }
.photo-wrap img { width: 100%; display: block; max-height: 340px; object-fit: contain; background: #f8fafc; padding: 8px; }
.photo-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0); display: flex; align-items: center; justify-content: center;
    transition: var(--transition);
}
.photo-overlay i { color: white; font-size: 2rem; opacity: 0; transform: scale(.8); transition: var(--transition); }
.photo-wrap:hover .photo-overlay { background: rgba(0,0,0,.25); }
.photo-wrap:hover .photo-overlay i { opacity: 1; transform: scale(1); }

.photo-actions { display: flex; gap: 8px; margin-top: 10px; }

/* ── Lightbox ── */
#lightbox {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.92); align-items: center; justify-content: center;
    flex-direction: column; gap: 16px; padding: 24px;
}
#lightbox.show { display: flex; }
#lightbox img {
    max-width: 92vw; max-height: 86vh;
    border-radius: 12px; object-fit: contain;
    box-shadow: 0 30px 80px rgba(0,0,0,.6);
    animation: zoomIn .25s cubic-bezier(.22,1,.36,1);
}
@keyframes zoomIn { from { opacity:0; transform:scale(.92); } to { opacity:1; transform:scale(1); } }
#lightbox-close {
    position: absolute; top: 20px; right: 20px;
    width: 40px; height: 40px; border-radius: 50%;
    background: rgba(255,255,255,.15); color: white;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; border: 1px solid rgba(255,255,255,.2); font-size: 1.1rem;
    transition: background .2s;
}
#lightbox-close:hover { background: rgba(255,255,255,.3); }
#lightbox-caption { color: rgba(255,255,255,.7); font-size: .85rem; text-align: center; }

/* ── No photo placeholder ── */
.no-photo {
    border: 2px dashed var(--border); border-radius: 14px;
    padding: 36px 24px; text-align: center;
    background: #f8fafc; color: var(--text-muted);
}
.no-photo i { font-size: 2.5rem; margin-bottom: 12px; opacity: .4; display: block; }
.no-photo p { font-size: .875rem; }

/* ── Response timeline ── */
.timeline { display: flex; flex-direction: column; gap: 0; }
.timeline-item { display: flex; gap: 14px; padding-bottom: 20px; position: relative; }
.timeline-item:not(:last-child)::before {
    content: ''; position: absolute;
    left: 17px; top: 38px; bottom: 0;
    width: 2px; background: var(--border);
}
.timeline-dot {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: var(--primary-soft); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; font-weight: 700; border: 2px solid var(--primary-mid);
}
.timeline-body {
    flex: 1; background: var(--bg); border: 1px solid var(--border);
    border-radius: 12px; padding: 14px 16px;
}
.timeline-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 6px; }
.timeline-name { font-weight: 700; font-size: .875rem; }
.timeline-date { font-size: .78rem; color: var(--text-muted); }
.timeline-text { font-size: .9rem; color: #475569; line-height: 1.65; }

.empty-state { text-align: center; padding: 32px 16px; color: var(--text-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 10px; display: block; opacity: .35; }
.empty-state p { font-size: .875rem; }
</style>

<main class="main-content">

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-left" style="display:flex; align-items:center; gap:14px;">
            <a href="aspirasi.php" class="btn btn-ghost btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div>
                <h1>Detail Pengaduan</h1>
                <p>ID Laporan: #<?php echo str_pad($data['id_aspirasi'], 4, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>
        <div class="top-bar-right">
            <span class="badge <?php echo $badgeClass; ?>" style="font-size:.85rem; padding:8px 16px;">
                <i class="fas <?php echo $badgeIcon; ?>"></i> <?php echo $badgeLabel; ?>
            </span>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash <?php echo $_SESSION['flash']['type']; ?>">
            <i class="fas <?php echo $_SESSION['flash']['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
            <?php echo $_SESSION['flash']['message'];
    unset($_SESSION['flash']); ?>
        </div>
    <?php
endif; ?>

    <div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 24px; align-items: start;">

        <!-- ═══ LEFT PANEL ═══ -->
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Informasi Laporan -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title"><?php echo htmlspecialchars($data['judul']); ?></div>
                        <div class="card-subtitle">
                            <span style="color:var(--primary); font-weight:600;"><?php echo htmlspecialchars($data['nama_kategori']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Meta info -->
                <div style="margin-bottom: 20px;">
                    <div class="info-row">
                        <i class="fas fa-user"></i>
                        <span style="color:var(--text-muted);">Pelapor</span>
                        <span class="info-val" style="margin-left:auto;"><?php echo htmlspecialchars($data['nama_siswa']); ?></span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-calendar"></i>
                        <span style="color:var(--text-muted);">Tanggal</span>
                        <span class="info-val" style="margin-left:auto;"><?php echo date('d F Y, H:i', strtotime($data['tanggal'])); ?> WIB</span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-tag"></i>
                        <span style="color:var(--text-muted);">Kategori</span>
                        <span class="info-val" style="margin-left:auto;"><?php echo htmlspecialchars($data['nama_kategori']); ?></span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div style="background:var(--bg); border-radius:12px; padding:18px; border-left:3px solid var(--primary);">
                    <div style="font-size:.75rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--text-muted); margin-bottom:10px;">
                        <i class="fas fa-align-left" style="margin-right:6px;"></i>Deskripsi Masalah
                    </div>
                    <p style="font-size:.925rem; line-height:1.75; color:#334155;"><?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?></p>
                </div>
            </div>

            <!-- Foto Bukti -->
            <div class="card">
                <div class="card-header" style="margin-bottom:16px;">
                    <div>
                        <div class="card-title"><i class="fas fa-image" style="color:var(--primary); margin-right:8px;"></i>Foto Bukti</div>
                        <div class="card-subtitle">Foto yang dilampirkan oleh siswa</div>
                    </div>
                    <?php if ($foto_exists): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Ada Foto</span>
                    <?php
else: ?>
                    <span class="badge badge-warning"><i class="fas fa-minus"></i> Tidak Ada</span>
                    <?php
endif; ?>
                </div>

                <?php if ($foto_exists): ?>
                    <!-- Photo with overlay & lightbox trigger -->
                    <div class="photo-wrap" onclick="openLightbox('<?php echo $foto_data; ?>', '<?php echo htmlspecialchars($data['judul']); ?>')">
                        <img src="<?php echo $foto_data; ?>" alt="Foto bukti: <?php echo htmlspecialchars($data['judul']); ?>" loading="lazy">
                        <div class="photo-overlay">
                            <i class="fas fa-expand"></i>
                        </div>
                    </div>
                    <div class="photo-actions">
                        <button class="btn btn-primary btn-sm" onclick="openLightbox('<?php echo $foto_data; ?>', '<?php echo htmlspecialchars($data['judul']); ?>')">
                            <i class="fas fa-expand"></i> Perbesar
                        </button>
                        <a href="<?php echo $foto_data; ?>" download="laporan_<?php echo $id; ?>.jpg" class="btn btn-ghost btn-sm">
                            <i class="fas fa-download"></i> Unduh Foto
                        </a>
                        <button class="btn btn-ghost btn-sm" onclick="const w=window.open();w.document.write('<img src=\'<?php echo $foto_data; ?>\' style=\'max-width:100%\'>');">
                            <i class="fas fa-arrow-up-right-from-square"></i> Buka di Tab Baru
                        </button>
                    </div>

                    <!-- Photo metadata -->
                    <div style="margin-top:12px; padding:12px 14px; background:var(--bg); border-radius:10px; font-size:.8rem; color:var(--text-muted); display:flex; gap:16px; flex-wrap:wrap;">
                        <span><i class="fas fa-file-image" style="margin-right:4px;"></i>Base64 Image Data</span>
                        <span><i class="fas fa-weight-hanging" style="margin-right:4px;"></i><?php echo round(strlen($foto_data) / 1024, 1); ?> KB</span>
                    </div>

                <?php
elseif ($data['foto'] && !$foto_exists): ?>
                    <!-- File recorded but missing -->
                    <div class="no-photo">
                        <i class="fas fa-triangle-exclamation" style="color:var(--warning);"></i>
                        <p style="font-weight:600; color:var(--warning); margin-bottom:4px;">File Tidak Ditemukan</p>
                        <p>Foto tercatat dalam database (<code><?php echo htmlspecialchars($data['foto']); ?></code>) namun file tidak ada di server.</p>
                    </div>
                <?php
else: ?>
                    <!-- No photo at all -->
                    <div class="no-photo">
                        <i class="fas fa-image"></i>
                        <p>Siswa tidak melampirkan foto pada laporan ini.</p>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- Form Tanggapan -->
            <div class="card">
                <div class="card-header" style="margin-bottom:20px;">
                    <div>
                        <div class="card-title"><i class="fas fa-reply" style="color:var(--primary); margin-right:8px;"></i>Berikan Tanggapan</div>
                        <div class="card-subtitle">Perbarui status dan tambahkan komentar untuk laporan ini</div>
                    </div>
                </div>
                <form action="" method="POST">
                    <div class="form-group">
                        <label class="form-label">Perbarui Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Menunggu" <?php echo $data['status'] == 'Menunggu' ? 'selected' : ''; ?>>⏳ Menunggu</option>
                            <option value="Proses"   <?php echo $data['status'] == 'Proses' ? 'selected' : ''; ?>>🔄 Proses</option>
                            <option value="Selesai"  <?php echo $data['status'] == 'Selesai' ? 'selected' : ''; ?>>✅ Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Isi Tanggapan</label>
                        <textarea name="respon" class="form-control" placeholder="Tulis tanggapan atau instruksi tindak lanjut..." required></textarea>
                    </div>
                    <button type="submit" name="submit_response" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Tanggapan
                    </button>
                </form>
            </div>
        </div>

        <!-- ═══ RIGHT PANEL ═══ -->
        <div>
            <div class="card" style="position: sticky; top: 24px;">
                <div class="card-header" style="margin-bottom:20px;">
                    <div>
                        <div class="card-title"><i class="fas fa-comments" style="color:var(--primary); margin-right:8px;"></i>Riwayat Tanggapan</div>
                        <div class="card-subtitle">Semua respons yang pernah diberikan</div>
                    </div>
                    <?php
$resp_count_q = mysqli_query($conn, "SELECT COUNT(*) as c FROM umpan_balik WHERE id_aspirasi = '$id'");
$resp_count = mysqli_fetch_assoc($resp_count_q)['c'];
?>
                    <span class="badge badge-primary"><?php echo $resp_count; ?></span>
                </div>

                <?php if ($resp_count > 0): ?>
                <div class="timeline">
                    <?php while ($res = mysqli_fetch_assoc($responses)): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot">
                            <?php echo strtoupper(substr($res['nama_admin'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="timeline-body">
                            <div class="timeline-meta">
                                <span class="timeline-name"><?php echo htmlspecialchars($res['nama_admin']); ?></span>
                                <span class="timeline-date">
                                    <i class="fas fa-clock fa-xs" style="margin-right:3px;"></i>
                                    <?php echo date('d M Y, H:i', strtotime($res['tanggal_respon'])); ?>
                                </span>
                            </div>
                            <p class="timeline-text"><?php echo nl2br(htmlspecialchars($res['respon'])); ?></p>
                        </div>
                    </div>
                    <?php
    endwhile; ?>
                </div>
                <?php
else: ?>
                <div class="empty-state">
                    <i class="fas fa-comment-slash"></i>
                    <p>Belum ada tanggapan yang diberikan untuk laporan ini.</p>
                </div>
                <?php
endif; ?>
            </div>
        </div>

    </div>
</main>

<!-- ── Lightbox ── -->
<div id="lightbox" onclick="closeLightbox(event)">
    <div id="lightbox-close" onclick="closeLightbox()"><i class="fas fa-xmark"></i></div>
    <img id="lightbox-img" src="" alt="">
    <div id="lightbox-caption"></div>
</div>

<script>
function openLightbox(src, caption) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-caption').textContent = caption;
    document.getElementById('lightbox').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
    if (!e || e.target === document.getElementById('lightbox') || e.currentTarget.id === 'lightbox-close') {
        document.getElementById('lightbox').classList.remove('show');
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
