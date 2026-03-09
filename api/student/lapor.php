<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check_role('siswa');

if (isset($_POST['submit_laporan'])) {
    $id_user = $_SESSION['user_id'];
    $id_kategori = sanitize($_POST['id_kategori']);
    $judul = sanitize($_POST['judul']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $foto = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['foto']['size'] <= 3 * 1024 * 1024) {
            $foto = time() . '_' . $_SESSION['user_id'] . '.' . $ext;

            // Read file and convert to Base64 (for Vercel read-only filesystem)
            $tmp_file = $_FILES['foto']['tmp_name'];
            $file_data = file_get_contents($tmp_file);
            $foto = 'data:image/' . $ext . ';base64,' . base64_encode($file_data);
        }
        else {
            $_SESSION['flash'] = ['message' => 'Format foto tidak valid (maks 3MB, JPG/PNG/WEBP).', 'type' => 'error'];
            header("Location: /student/lapor.php");
            exit;
        }
    }

    $q = "INSERT INTO aspirasi (id_user,id_kategori,judul,deskripsi,foto) VALUES ('$id_user','$id_kategori','$judul','$deskripsi','$foto')";
    if (mysqli_query($conn, $q)) {
        redirect('student/riwayat.php', 'Laporan berhasil terkirim! Proses akan dimulai segera.', 'success');
    }
    else {
        $_SESSION['flash'] = ['message' => 'Terjadi kesalahan. Coba lagi.', 'type' => 'error'];
        header("Location: /student/lapor.php");
        exit;
    }
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
$kat_list = [];
while ($k = mysqli_fetch_assoc($kategori)) {
    $kat_list[] = $k;
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.form-group   { margin-bottom:20px; }
.form-label   { display:block;font-size:.8rem;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--text-muted);margin-bottom:8px; }
.form-control { width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;font-family:inherit;color:var(--text);background:var(--white);transition:var(--transition); }
.form-control:focus { outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.12); }
textarea.form-control { resize:vertical;min-height:130px;line-height:1.6; }
select.form-control { cursor:pointer; }
.flash { display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:500; }
.flash.success { background:var(--success-soft);color:#065f46;border:1px solid #a7f3d0; }
.flash.error   { background:var(--danger-soft); color:#7f1d1d; border:1px solid #fecaca; }

/* Drop zone */
.drop-zone {
    border:2px dashed var(--border); border-radius:12px;
    padding:32px 20px; text-align:center;
    cursor:pointer; transition:var(--transition);
    background:var(--bg); position:relative;
}
.drop-zone:hover, .drop-zone.drag-over { border-color:var(--primary);background:var(--primary-soft); }
.drop-zone input[type="file"] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
.drop-zone .dz-icon { font-size:2rem; color:var(--text-muted); margin-bottom:10px; display:block; }
.drop-zone .dz-text { font-size:.875rem; color:var(--text-muted); }
.drop-zone .dz-text strong { color:var(--primary); }
#preview-wrap { display:none;margin-top:14px; }
#preview-wrap img { max-width:100%;max-height:200px;border-radius:10px;border:1px solid var(--border);object-fit:contain; }
</style>

<main class="main-content">
    <div class="top-bar">
        <div class="top-bar-left">
            <div style="display:flex;align-items:center;gap:12px;">
                <a href="dashboard.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
                <div>
                    <h1>Buat Laporan Baru</h1>
                    <p>Laporkan masalah sarana atau prasarana sekolah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash messages handled by SweetAlert2 -->

    <div style="display:grid; grid-template-columns:1.8fr 1fr; gap:22px; align-items:start;">

        <!-- Form Card -->
        <div class="card">
            <div class="card-header" style="margin-bottom:24px;">
                <div>
                    <div class="card-title"><i class="fas fa-paper-plane" style="color:var(--primary);margin-right:8px;"></i>Formulir Pengaduan</div>
                    <div class="card-subtitle">Isi semua informasi dengan lengkap dan jelas</div>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" id="form-laporan">

                <div class="form-group">
                    <label class="form-label" for="judul">Judul Laporan <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="judul" id="judul" class="form-control"
                           placeholder="Contoh: AC Kelas 12 RPL 1 Tidak Dingin" required maxlength="150">
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:5px;">Maksimal 150 karakter</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="id_kategori">Kategori <span style="color:var(--danger);">*</span></label>
                    <select name="id_kategori" id="id_kategori" class="form-control" required>
                        <option value="">— Pilih kategori yang sesuai —</option>
                        <?php foreach ($kat_list as $k): ?>
                        <option value="<?php echo $k['id_kategori']; ?>"><?php echo htmlspecialchars($k['nama_kategori']); ?></option>
                        <?php
endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="deskripsi">Deskripsi Masalah <span style="color:var(--danger);">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control"
                              placeholder="Jelaskan secara detail: letak masalah, kapan terjadi, seberapa parah, dan informasi penting lainnya..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Bukti <span style="color:var(--text-muted);font-weight:400;text-transform:none;letter-spacing:0;">(Opsional)</span></label>
                    <div class="drop-zone" id="dropZone">
                        <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                        <i class="fas fa-cloud-arrow-up dz-icon"></i>
                        <div class="dz-text">
                            <strong>Klik untuk pilih foto</strong> atau seret ke sini<br>
                            <span style="font-size:.78rem;">Format JPG, PNG, WEBP · Maks. 3 MB</span>
                        </div>
                    </div>
                    <div id="preview-wrap">
                        <img id="preview-img" src="" alt="Preview">
                        <button type="button" onclick="clearPhoto()" style="display:block;margin-top:8px;background:none;border:none;color:var(--danger);font-size:.8rem;cursor:pointer;">
                            <i class="fas fa-trash"></i> Hapus foto
                        </button>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:10px;">
                    <button type="submit" name="submit_laporan" class="btn btn-primary" style="flex:1;justify-content:center;padding:13px;">
                        <i class="fas fa-paper-plane"></i> Kirim Laporan
                    </button>
                    <a href="dashboard.php" class="btn btn-ghost" style="padding:13px 20px;">Batal</a>
                </div>
            </form>
        </div>

        <!-- Panduan card -->
        <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:24px;">
            <div class="card">
                <div class="card-title" style="margin-bottom:14px;"><i class="fas fa-circle-info" style="color:var(--primary);margin-right:8px;"></i>Panduan Pengaduan</div>
                <ol style="padding-left:18px;display:flex;flex-direction:column;gap:10px;">
                    <li style="font-size:.865rem;line-height:1.6;color:#475569;">
                        <strong>Judul singkat & jelas</strong><br>
                        Sebutkan objek dan masalahnya. Contoh: "Proyektor Kelas X TKJ Mati."
                    </li>
                    <li style="font-size:.865rem;line-height:1.6;color:#475569;">
                        <strong>Pilih kategori yang tepat</strong><br>
                        Kategori membantu admin mengarahkan laporan ke petugas yang sesuai.
                    </li>
                    <li style="font-size:.865rem;line-height:1.6;color:#475569;">
                        <strong>Deskripsi lengkap</strong><br>
                        Sebutkan lokasi, waktu kejadian, dan tingkat keparahan masalah.
                    </li>
                    <li style="font-size:.865rem;line-height:1.6;color:#475569;">
                        <strong>Sertakan foto (jika ada)</strong><br>
                        Foto mempercepat proses verifikasi dan tindak lanjut.
                    </li>
                </ol>
            </div>

            <div style="background:linear-gradient(135deg,#ecfdf5,#d1fae5);border:1.5px solid #a7f3d0;border-radius:14px;padding:18px 20px;">
                <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#065f46;margin-bottom:10px;">
                    <i class="fas fa-shield-halved" style="margin-right:6px;"></i>Laporan Anda Aman
                </div>
                <p style="font-size:.85rem;color:#064e3b;line-height:1.7;">
                    Identitas pelapor hanya diketahui oleh administrator. Laporan ditangani secara profesional dan transparan.
                </p>
            </div>
        </div>

    </div>
</main>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-wrap').style.display = 'block';
            document.querySelector('.dz-icon').style.display = 'none';
            document.querySelector('.dz-text').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function clearPhoto() {
    document.getElementById('foto').value = '';
    document.getElementById('preview-wrap').style.display = 'none';
    document.querySelector('.dz-icon').style.display = 'block';
    document.querySelector('.dz-text').style.display = 'block';
}
// Drag-over styling
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));
dz.addEventListener('drop', () => dz.classList.remove('drag-over'));
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
