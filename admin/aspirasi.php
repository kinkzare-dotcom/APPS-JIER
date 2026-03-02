<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

// Update Status
if (isset($_POST['update_status'])) {
    $id = sanitize($_POST['id_aspirasi']);
    $status = sanitize($_POST['status']);
    mysqli_query($conn, "UPDATE aspirasi SET status = '$status' WHERE id_aspirasi = '$id'");
    $_SESSION['flash'] = ['message' => 'Status berhasil diperbarui!', 'type' => 'success'];
    header("Location: aspirasi.php" . (isset($_GET['status']) ? '?status=' . $_GET['status'] : ''));
    exit;
}

// Filter
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$where = "WHERE 1=1";
if ($filter_status)
    $where .= " AND a.status = '$filter_status'";
if ($search)
    $where .= " AND (a.judul LIKE '%$search%' OR u.nama LIKE '%$search%')";

$result = mysqli_query($conn, "SELECT a.*, u.nama as nama_siswa, k.nama_kategori
    FROM aspirasi a
    JOIN users u ON a.id_user = u.id_user
    JOIN kategori k ON a.id_kategori = k.id_kategori
    $where ORDER BY a.tanggal DESC");

$total_count = mysqli_num_rows($result);

// Count per status
$count_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi"))['c'];
$count_tunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE status='Menunggu'"))['c'];
$count_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE status='Proses'"))['c'];
$count_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE status='Selesai'"))['c'];

require_once '../includes/header.php';
?>

<style>
.form-group   { margin-bottom: 16px; }
.form-label   { display:block; font-size:.78rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--text-muted); margin-bottom:7px; }
.form-control { width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:.9rem; font-family:inherit; color:var(--text); background:var(--white); transition:var(--transition); }
.form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.12); }
.flash { display:flex; align-items:center; gap:12px; padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:.9rem; font-weight:500; }
.flash.success { background:var(--success-soft); color:#065f46; border:1px solid #a7f3d0; }
.flash.error   { background:var(--danger-soft);  color:#7f1d1d; border:1px solid #fecaca; }

.filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.filter-tab  { padding:7px 16px; border-radius:50px; font-size:.82rem; font-weight:600; text-decoration:none; border:1.5px solid var(--border); color:var(--text-muted); background:var(--white); transition:var(--transition); }
.filter-tab:hover, .filter-tab.active { border-color:var(--primary); color:var(--primary); background:var(--primary-soft); }
.filter-tab .count { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; background:currentColor; color:white; border-radius:50%; font-size:.65rem; margin-left:6px; opacity:.85; }
</style>

<div class="mesh-gradient"></div>

<main class="main-content" style="animation: scaleIn 0.6s ease both;">
    <div class="top-bar">
        <div class="top-bar-left">
            <h1>Data Pengaduan</h1>
            <p>Kelola semua laporan pengaduan yang masuk dari siswa</p>
        </div>
        <div class="top-bar-right">
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.85rem;"></i>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari judul / siswa..." class="form-control" style="padding-left:36px;width:240px;">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                <?php if ($search || $filter_status): ?>
                <a href="aspirasi.php" class="btn btn-ghost btn-sm"><i class="fas fa-xmark"></i> Reset</a>
                <?php
endif; ?>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
    <div class="flash <?php echo $_SESSION['flash']['type']; ?>">
        <i class="fas fa-circle-check"></i> <?php echo $_SESSION['flash']['message'];
    unset($_SESSION['flash']); ?>
    </div>
    <?php
endif; ?>

    <!-- Filter tabs -->
    <div class="filter-tabs">
        <a href="aspirasi.php" class="filter-tab <?php echo !$filter_status ? 'active' : ''; ?>">
            Semua <span class="count" style="background:var(--text-muted);"><?php echo $count_all; ?></span>
        </a>
        <a href="?status=Menunggu" class="filter-tab <?php echo $filter_status == 'Menunggu' ? 'active' : ''; ?>" style="<?php echo $filter_status == 'Menunggu' ? 'border-color:var(--warning);color:#92400e;background:var(--warning-soft);' : ''; ?>">
            ⏳ Menunggu <span class="count" style="background:var(--warning);"><?php echo $count_tunggu; ?></span>
        </a>
        <a href="?status=Proses" class="filter-tab <?php echo $filter_status == 'Proses' ? 'active' : ''; ?>" style="<?php echo $filter_status == 'Proses' ? 'border-color:var(--info);color:#1e40af;background:var(--info-soft);' : ''; ?>">
            🔄 Proses <span class="count" style="background:var(--info);"><?php echo $count_proses; ?></span>
        </a>
        <a href="?status=Selesai" class="filter-tab <?php echo $filter_status == 'Selesai' ? 'active' : ''; ?>" style="<?php echo $filter_status == 'Selesai' ? 'border-color:var(--success);color:#065f46;background:var(--success-soft);' : ''; ?>">
            ✅ Selesai <span class="count" style="background:var(--success);"><?php echo $count_selesai; ?></span>
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="margin-bottom:18px;">
            <div>
                <div class="card-title">Daftar Pengaduan</div>
                <div class="card-subtitle"><?php echo $total_count; ?> data ditemukan<?php echo $filter_status ? " · Filter: <strong>$filter_status</strong>" : ''; ?><?php echo $search ? " · Pencarian: <strong>$search</strong>" : ''; ?></div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Judul Laporan</th>
                        <th>Tanggal</th>
                        <th>Foto</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
$no = 1;
while ($row = mysqli_fetch_assoc($result)):
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
                <tr>
                    <td class="text-muted" style="font-size:.8rem;"><?php echo $no++; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--primary-soft);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;flex-shrink:0;">
                                <?php echo strtoupper(substr($row['nama_siswa'], 0, 1)); ?>
                            </div>
                            <span style="font-weight:500;font-size:.875rem;"><?php echo htmlspecialchars($row['nama_siswa']); ?></span>
                        </div>
                    </td>
                    <td>
                        <span style="background:var(--primary-soft);color:var(--primary);padding:3px 10px;border-radius:50px;font-size:.75rem;font-weight:600;">
                            <?php echo htmlspecialchars($row['nama_kategori']); ?>
                        </span>
                    </td>
                    <td style="max-width:220px;">
                        <span style="font-weight:600;font-size:.875rem;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?php echo htmlspecialchars($row['judul']); ?>">
                            <?php echo htmlspecialchars($row['judul']); ?>
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.82rem;white-space:nowrap;">
                        <?php echo date('d M Y', strtotime($row['tanggal'])); ?>
                    </td>
                    <td>
                        <?php if ($row['foto'] && file_exists('../uploads/' . $row['foto'])): ?>
                            <span class="badge badge-success"><i class="fas fa-image"></i> Ada</span>
                        <?php
    else: ?>
                            <span class="badge" style="background:#f1f5f9;color:var(--text-muted);">—</span>
                        <?php
    endif; ?>
                    </td>
                    <td><span class="badge <?php echo $bc; ?>"><i class="fas <?php echo $bi; ?>"></i> <?php echo $row['status']; ?></span></td>
                    <td style="text-align:right;">
                        <a href="detail_aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php
endwhile; ?>
                <?php if ($total_count === 0): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <i class="fas fa-clipboard-list" style="font-size:2rem;margin-bottom:12px;display:block;opacity:.3;"></i>
                        Tidak ada data yang ditemukan.
                    </td>
                </tr>
                <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
