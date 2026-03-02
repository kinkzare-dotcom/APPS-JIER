<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

$filter_start = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : '';
$filter_end = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : '';
$filter_status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$where = "WHERE 1=1";
if ($filter_start && $filter_end) {
    $where .= " AND a.tanggal BETWEEN '$filter_start 00:00:00' AND '$filter_end 23:59:59'";
}
if ($filter_status) {
    $where .= " AND a.status = '$filter_status'";
}

$result = mysqli_query($conn, "SELECT a.*, u.nama as nama_siswa, k.nama_kategori
    FROM aspirasi a
    JOIN users u ON a.id_user = u.id_user
    JOIN kategori k ON a.id_kategori = k.id_kategori
    $where ORDER BY a.tanggal DESC");

$total = mysqli_num_rows($result);

// Stats in period
$cnt_tunggu = $cnt_proses = $cnt_selesai = 0;
$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
    if ($r['status'] === 'Menunggu')
        $cnt_tunggu++;
    elseif ($r['status'] === 'Proses')
        $cnt_proses++;
    else
        $cnt_selesai++;
}

require_once '../includes/header.php';
?>

<style>
.form-group   { margin-bottom: 16px; }
.form-label   { display:block; font-size:.78rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--text-muted); margin-bottom:7px; }
.form-control { width:100%; padding:10px 14px; border:1.5px solid var(--border); border-radius:10px; font-size:.9rem; font-family:inherit; color:var(--text); background:var(--white); transition:var(--transition); }
.form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.12); }

@media print {
    .sidebar, .top-bar, .no-print { display:none !important; }
    .main-content { margin-left:0 !important; padding:20px !important; }
    .card { box-shadow:none !important; border:none !important; }
    .print-header { display:block !important; }
    .btn { display:none !important; }
}
</style>

<main class="main-content">
    <div class="top-bar no-print">
        <div class="top-bar-left">
            <h1>Laporan & Statistik</h1>
            <p>Filter, cetak, dan ekspor data pengaduan</p>
        </div>
        <div class="top-bar-right">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter card -->
    <div class="card no-print" style="margin-bottom:22px;">
        <div class="card-header" style="margin-bottom:18px;">
            <div>
                <div class="card-title"><i class="fas fa-filter" style="color:var(--primary);margin-right:8px;"></i>Filter Laporan</div>
                <div class="card-subtitle">Saring data berdasarkan periode dan status</div>
            </div>
            <?php if ($filter_start || $filter_end || $filter_status): ?>
            <a href="laporan.php" class="btn btn-ghost btn-sm"><i class="fas fa-xmark"></i> Reset Filter</a>
            <?php
endif; ?>
        </div>
        <form action="" method="GET">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:16px; align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $filter_start; ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $filter_end; ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Menunggu" <?php echo $filter_status === 'Menunggu' ? 'selected' : ''; ?>>⏳ Menunggu</option>
                        <option value="Proses"   <?php echo $filter_status === 'Proses' ? 'selected' : ''; ?>>🔄 Proses</option>
                        <option value="Selesai"  <?php echo $filter_status === 'Selesai' ? 'selected' : ''; ?>>✅ Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-magnifying-glass"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary mini stats -->
    <div class="stat-grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:22px;">
        <div class="stat-card" style="--card-accent:var(--primary);padding:18px;">
            <div class="stat-card-top">
                <div><div class="stat-label">Total Data</div><div class="stat-num" style="color:var(--primary);"><?php echo $total; ?></div></div>
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fas fa-clipboard-list"></i></div>
            </div>
        </div>
        <div class="stat-card" style="--card-accent:var(--warning);padding:18px;">
            <div class="stat-card-top">
                <div><div class="stat-label">Menunggu</div><div class="stat-num" style="color:var(--warning);"><?php echo $cnt_tunggu; ?></div></div>
                <div class="stat-icon" style="background:var(--warning-soft);color:var(--warning);"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="stat-card" style="--card-accent:var(--info);padding:18px;">
            <div class="stat-card-top">
                <div><div class="stat-label">Proses</div><div class="stat-num" style="color:var(--info);"><?php echo $cnt_proses; ?></div></div>
                <div class="stat-icon" style="background:var(--info-soft);color:var(--info);"><i class="fas fa-spinner"></i></div>
            </div>
        </div>
        <div class="stat-card" style="--card-accent:var(--success);padding:18px;">
            <div class="stat-card-top">
                <div><div class="stat-label">Selesai</div><div class="stat-num" style="color:var(--success);"><?php echo $cnt_selesai; ?></div></div>
                <div class="stat-icon" style="background:var(--success-soft);color:var(--success);"><i class="fas fa-circle-check"></i></div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card" id="reportContent">

        <!-- Print header (hidden on screen) -->
        <div class="print-header" style="display:none; text-align:center; margin-bottom:28px; padding-bottom:20px; border-bottom:2px solid #000;">
            <h2 style="font-size:1.4rem; margin-bottom:8px;">LAPORAN PENGADUAN SARANA SEKOLAH</h2>
            <p style="font-size:.9rem;">
                Periode: <?php echo $filter_start ? date('d F Y', strtotime($filter_start)) : 'Semua'; ?>
                <?php echo($filter_start && $filter_end) ? ' s/d ' . date('d F Y', strtotime($filter_end)) : ''; ?>
                <?php echo $filter_status ? " · Status: $filter_status" : ''; ?>
            </p>
            <p style="font-size:.8rem; color:#666; margin-top:4px;">Dicetak pada: <?php echo date('d F Y, H:i'); ?> WIB</p>
        </div>

        <div class="card-header no-print" style="margin-bottom:18px;">
            <div>
                <div class="card-title">Hasil Laporan</div>
                <div class="card-subtitle"><?php echo $total; ?> data
                    <?php echo $filter_start ? "· Dari <strong>" . date('d M Y', strtotime($filter_start)) . "</strong>" : ''; ?>
                    <?php echo $filter_end ? " s/d <strong>" . date('d M Y', strtotime($filter_end)) . "</strong>" : ''; ?>
                    <?php echo $filter_status ? " · Status: <strong>$filter_status</strong>" : ''; ?>
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="tbl" style="font-size:.875rem;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Judul Laporan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($rows) > 0): ?>
                <?php foreach ($rows as $no => $row):
        if ($row['status'] === 'Menunggu') {
            $bc = 'badge-warning';
        }
        elseif ($row['status'] === 'Proses') {
            $bc = 'badge-primary';
        }
        else {
            $bc = 'badge-success';
        }
?>
                <tr>
                    <td class="text-muted"><?php echo $no + 1; ?></td>
                    <td style="white-space:nowrap;"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                    <td style="font-weight:500;"><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><span class="badge <?php echo $bc; ?>"><?php echo $row['status']; ?></span></td>
                </tr>
                <?php
    endforeach; ?>
                <?php
else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <i class="fas fa-file-magnifying-glass" style="font-size:2rem;margin-bottom:12px;display:block;opacity:.3;"></i>
                        Tidak ada data untuk filter yang dipilih.
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
