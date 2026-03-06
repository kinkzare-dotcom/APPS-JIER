<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('admin');

// Add User
if (isset($_POST['add_user'])) {
    $nama = sanitize($_POST['nama']);
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);
    $role = sanitize($_POST['role']);
    // Check duplicate
    $dup = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_user FROM users WHERE username='$username'"));
    if ($dup) {
        $_SESSION['flash'] = ['message' => 'Username sudah digunakan!', 'type' => 'error'];
    }
    else {
        mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama','$username','$password','$role')");
        $_SESSION['flash'] = ['message' => "User '$nama' berhasil ditambahkan!", 'type' => 'success'];
    }
    header("Location: users.php");
    exit;
}

// Delete User
if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
        $_SESSION['flash'] = ['message' => 'User berhasil dihapus!', 'type' => 'success'];
    }
    else {
        $_SESSION['flash'] = ['message' => 'Tidak dapat menghapus akun sendiri.', 'type' => 'error'];
    }
    header("Location: users.php");
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama ASC");
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users"))['c'];
$total_admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role='admin'"))['c'];
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users WHERE role='siswa'"))['c'];

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
            <h1>Manajemen Pengguna</h1>
            <p>Kelola akun siswa dan administrator sistem</p>
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

    <!-- Summary cards -->
    <div class="stat-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:24px;">
        <div class="stat-card" style="--card-accent:var(--primary);">
            <div class="stat-card-top">
                <div><div class="stat-label">Total Pengguna</div><div class="stat-num" style="color:var(--primary);"><?php echo $total_users; ?></div></div>
                <div class="stat-icon" style="background:var(--primary-soft);color:var(--primary);"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="stat-card" style="--card-accent:var(--info);">
            <div class="stat-card-top">
                <div><div class="stat-label">Administrator</div><div class="stat-num" style="color:var(--info);"><?php echo $total_admin; ?></div></div>
                <div class="stat-icon" style="background:var(--info-soft);color:var(--info);"><i class="fas fa-shield-halved"></i></div>
            </div>
        </div>
        <div class="stat-card" style="--card-accent:var(--success);">
            <div class="stat-card-top">
                <div><div class="stat-label">Siswa</div><div class="stat-num" style="color:var(--success);"><?php echo $total_siswa; ?></div></div>
                <div class="stat-icon" style="background:var(--success-soft);color:var(--success);"><i class="fas fa-graduation-cap"></i></div>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 2fr; gap:22px; align-items:start;">

        <!-- Form Tambah User -->
        <div class="card" style="position:sticky;top:24px;">
            <div class="card-header" style="margin-bottom:20px;">
                <div>
                    <div class="card-title"><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px;"></i>Tambah Pengguna</div>
                    <div class="card-subtitle">Daftarkan akun baru ke sistem</div>
                </div>
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap pengguna" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username login" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="siswa">👨‍🎓 Siswa</option>
                        <option value="admin">🛡️ Admin</option>
                    </select>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </button>
            </form>
        </div>

        <!-- Tabel User -->
        <div class="card">
            <div class="card-header" style="margin-bottom:18px;">
                <div>
                    <div class="card-title">Daftar Pengguna</div>
                    <div class="card-subtitle"><?php echo $total_users; ?> akun terdaftar</div>
                </div>
            </div>
            <div style="overflow-x:auto;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pengguna</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
while ($u = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;"><?php echo $no++; ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:<?php echo $u['role'] === 'admin' ? 'var(--primary-soft)' : 'var(--success-soft)'; ?>;color:<?php echo $u['role'] === 'admin' ? 'var(--primary)' : 'var(--success)'; ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                    <?php echo strtoupper(substr($u['nama'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:.875rem;"><?php echo htmlspecialchars($u['nama']); ?></div>
                                    <?php if ($u['id_user'] == $_SESSION['user_id']): ?>
                                    <div style="font-size:.7rem;color:var(--primary);font-weight:600;">Akun Anda</div>
                                    <?php
    endif; ?>
                                </div>
                            </div>
                        </td>
                        <td><code style="font-size:.82rem;background:var(--bg);padding:3px 8px;border-radius:6px;"><?php echo htmlspecialchars($u['username']); ?></code></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge badge-primary"><i class="fas fa-shield-halved"></i> Admin</span>
                            <?php
    else: ?>
                            <span class="badge badge-success"><i class="fas fa-graduation-cap"></i> Siswa</span>
                            <?php
    endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <?php if ($u['id_user'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?php echo $u['id_user']; ?>" class="btn btn-sm" style="background:var(--danger-soft);color:var(--danger);" onclick="return confirm('Yakin ingin menghapus akun <?php echo htmlspecialchars($u['nama']); ?>?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                            <?php
    else: ?>
                            <span style="font-size:.78rem;color:var(--text-muted);">—</span>
                            <?php
    endif; ?>
                        </td>
                    </tr>
                    <?php
endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
