<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

// Add
if (isset($_POST['add_kat'])) {
    $nama = sanitize($_POST['nama_kategori']);
    if ($nama) {
        mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama')");
        $_SESSION['flash'] = ['message' => "Kategori '$nama' berhasil ditambahkan!", 'type' => 'success'];
    }
    header("Location: kategori.php");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $used = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM aspirasi WHERE id_kategori='$id'"))['c'];
    if ($used > 0) {
        $_SESSION['flash'] = ['message' => "Tidak dapat dihapus! Kategori ini digunakan oleh $used pengaduan.", 'type' => 'error'];
    }
    else {
        $kat_name = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori='$id'"))['nama_kategori'] ?? '';
        mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");
        $_SESSION['flash'] = ['message' => "Kategori '$kat_name' berhasil dihapus!", 'type' => 'success'];
    }
    header("Location: kategori.php");
    exit;
}

$kategories = mysqli_query($conn, "SELECT k.*, COUNT(a.id_aspirasi) as jml FROM kategori k LEFT JOIN aspirasi a ON k.id_kategori=a.id_kategori GROUP BY k.id_kategori ORDER BY k.nama_kategori ASC");
$total_kat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM kategori"))['c'];

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
</style>

<main class="main-content">
    <div class="top-bar">
        <div class="top-bar-left">
            <h1>Manajemen Kategori</h1>
            <p>Kelola jenis-jenis kategori pengaduan sarana</p>
        </div>
        <div class="top-bar-right">
            <div class="stat-card" style="--card-accent:var(--primary);padding:12px 20px;flex-direction:row;gap:12px;align-items:center;min-width:unset;">
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);width:36px;height:36px;border-radius:10px;font-size:.85rem;">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div class="stat-label" style="margin-bottom:0;">Total Kategori</div>
                    <div class="stat-num" style="font-size:1.4rem;color:var(--primary);"><?php echo $total_kat; ?></div>
                </div>
            </div>
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

    <div style="display:grid; grid-template-columns:1fr 2fr; gap:22px; align-items:start;">

        <!-- Form -->
        <div class="card" style="position:sticky;top:24px;">
            <div class="card-header" style="margin-bottom:20px;">
                <div>
                    <div class="card-title"><i class="fas fa-plus-circle" style="color:var(--primary);margin-right:8px;"></i>Tambah Kategori</div>
                    <div class="card-subtitle">Buat jenis pengaduan baru</div>
                </div>
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Lampu Rusak, AC Bermasalah..." required>
                </div>
                <button type="submit" name="add_kat" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </button>
            </form>

            <div style="margin-top:20px;padding:14px;background:var(--warning-soft);border-radius:10px;border-left:3px solid var(--warning);">
                <p style="font-size:.82rem;color:#92400e;line-height:1.6;">
                    <i class="fas fa-triangle-exclamation" style="margin-right:6px;"></i>
                    <strong>Perhatian:</strong> Kategori yang sudah digunakan oleh pengaduan <strong>tidak dapat dihapus</strong>.
                </p>
            </div>
        </div>

        <!-- List -->
        <div class="card">
            <div class="card-header" style="margin-bottom:18px;">
                <div>
                    <div class="card-title">Daftar Kategori</div>
                    <div class="card-subtitle"><?php echo $total_kat; ?> kategori tersedia</div>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Pengaduan</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$no = 1;
$colors = ['#4f46e5', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6', '#f97316'];
mysqli_data_seek($kategories, 0);
while ($k = mysqli_fetch_assoc($kategories)):
    $color = $colors[($k['id_kategori'] - 1) % count($colors)];
?>
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;"><?php echo $no++; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:8px;background:<?php echo $color; ?>20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-tag" style="color:<?php echo $color; ?>;font-size:.75rem;"></i>
                                </div>
                                <span style="font-weight:600;"><?php echo htmlspecialchars($k['nama_kategori']); ?></span>
                            </div>
                        </td>
                        <td>
                            <?php if ($k['jml'] > 0): ?>
                            <span style="font-weight:700;color:var(--primary);"><?php echo $k['jml']; ?></span>
                            <span class="text-muted" style="font-size:.8rem;"> pengaduan</span>
                            <?php
    else: ?>
                            <span class="text-muted" style="font-size:.82rem;">Belum ada pengaduan</span>
                            <?php
    endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <?php if ($k['jml'] == 0): ?>
                            <a href="?delete=<?php echo $k['id_kategori']; ?>"
                               class="btn btn-sm" style="background:var(--danger-soft);color:var(--danger);"
                               onclick="return confirm('Yakin hapus kategori "<?php echo htmlspecialchars($k['nama_kategori']); ?>"?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <?php
    else: ?>
                            <span class="badge badge-warning" style="font-size:.75rem;">
                                <i class="fas fa-lock"></i> Digunakan
                            </span>
                            <?php
    endif; ?>
                        </td>
                    </tr>
                    <?php
endwhile; ?>
                    <?php if ($total_kat === 0): ?>
                    <tr><td colspan="4" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <i class="fas fa-tags" style="font-size:2rem;margin-bottom:12px;display:block;opacity:.3;"></i>
                        Belum ada kategori. Tambahkan yang pertama!
                    </td></tr>
                    <?php
endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
