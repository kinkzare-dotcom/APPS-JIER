<?php
if (is_dir('/tmp'))
    session_save_path('/tmp');
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: /admin/dashboard.php");
    }
    else {
        header("Location: /student/dashboard.php");
    }
    exit;
}

$error = '';
$success = '';

// Handle Login
if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $source_role = isset($_POST['source_role']) ? sanitize($_POST['source_role']) : '';

        // Role Validation
        if ($source_role !== '' && $user['role'] !== $source_role) {
            $error = "Akses ditolak. Akun Anda terdaftar sebagai " . ucfirst($user['role']) . ".";
        }
        else {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: /admin/dashboard.php");
            }
            else {
                header("Location: /student/dashboard.php");
            }
            exit;
        }
    }
    else {
        $error = "Username atau password salah. Silakan coba lagi.";
    }
}

// Handle Registration
if (isset($_POST['register'])) {
    $nama = sanitize($_POST['nama']);
    $username = sanitize($_POST['username_reg']);
    $password = md5($_POST['password_reg']);
    $role = 'siswa';

    // Check if username exists
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username sudah digunakan. Silakan pilih username lain.";
    }
    else {
        $insert_query = "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$password', '$role')";
        if (mysqli_query($conn, $insert_query)) {
            $success = "Akun berhasil dibuat! Silakan login.";
        }
        else {
            $error = "Gagal membuat akun. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register – Sistem Pengaduan Sarana Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --indigo-900: #1e1b4b;
            --indigo-800: #312e81;
            --indigo-600: #4f46e5;
            --indigo-500: #6366f1;
            --indigo-400: #818cf8;
            --indigo-300: #a5b4fc;
            --pink-500:   #ec4899;
            --bg:         #0d0b1e;
            --card-bg:    rgba(255,255,255,0.04);
            --card-border:rgba(255,255,255,0.10);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Background orbs ── */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }
        body::before {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(79,70,229,.35), transparent 70%);
            top: -150px; left: -100px;
        }
        body::after {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(236,72,153,.25), transparent 70%);
            bottom: -100px; right: -100px;
        }

        /* Floating particles */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .particle {
            position: absolute;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(165,180,252,.5);
            animation: drift linear infinite;
        }
        @keyframes drift {
            0%   { transform: translateY(110vh) rotate(0deg); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: .6; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        /* ── Card ── */
        .card {
            position: relative; z-index: 10;
            width: 100%; max-width: 460px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            padding: 40px 40px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 0 0 1px rgba(255,255,255,.05) inset,
                0 32px 80px rgba(0,0,0,.55),
                0 0 60px rgba(79,70,229,.12);
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) both;
            transition: height 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 0 0 1px rgba(255,255,255,.1) inset,
                0 40px 100px rgba(0,0,0,.6),
                0 0 80px rgba(79,70,229,.15);
        }
        .card::before {
            content: '';
            position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.05), transparent);
            transform: skewX(-25deg);
            transition: 0.75s;
            pointer-events: none;
        }
        .card:hover::before { left: 150%; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Logo / Branding ── */
        .brand {
            display: flex; flex-direction: column; align-items: center;
            margin-bottom: 30px;
        }
        .brand-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--indigo-600), var(--pink-500));
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
            box-shadow: 0 12px 30px rgba(79,70,229,.45);
            margin-bottom: 12px;
            animation: pulseIcon 3s ease infinite;
        }
        @keyframes pulseIcon {
            0%,100% { box-shadow: 0 12px 30px rgba(79,70,229,.45); }
            50%      { box-shadow: 0 16px 40px rgba(236,72,153,.45); }
        }
        .brand h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem; font-weight: 800;
            color: #fff; letter-spacing: -.5px;
        }
        .brand p {
            margin-top: 4px; font-size: .85rem;
            color: rgba(255,255,255,.45); text-align: center;
        }

        /* ── Transitions ── */
        .view-content { animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ── Role Selection ── */
        .role-label {
            font-size: .75rem; font-weight: 600; letter-spacing: .08em;
            color: rgba(255,255,255,.35); text-transform: uppercase;
            margin-bottom: 14px; text-align: center;
        }

        .role-grid { display: flex; flex-direction: column; gap: 12px; }

        .role-card {
            display: flex; align-items: center; gap: 18px;
            padding: 18px 24px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: 16px;
            cursor: pointer;
            transition: all .25s;
        }
        .role-card:hover {
            background: rgba(99,102,241,.12);
            border-color: rgba(129,140,248,.4);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(99,102,241,.2);
        }
        .role-card-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
        }
        .role-card-icon.siswa  { background: rgba(99,102,241,.2);  color: var(--indigo-300); }
        .role-card-icon.admin  { background: rgba(236,72,153,.2);  color: #f9a8d4; }
        .role-card-info h3 { color: #fff; font-size: 0.95rem; font-weight: 600; }
        .role-card-info p  { color: rgba(255,255,255,.4); font-size: .8rem; margin-top: 2px; }
        .role-card-arrow { margin-left: auto; color: rgba(255,255,255,.25); font-size: .85rem; transition: transform .25s; }
        .role-card:hover .role-card-arrow { transform: translateX(4px); color: var(--indigo-400); }

        /* ── Common Form Styles ── */
        .form-title { margin-bottom: 20px; }
        .form-title h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fff; font-size: 1.25rem; font-weight: 700;
        }
        .form-title p { color: rgba(255,255,255,.4); font-size: .85rem; margin-top: 4px; }

        /* Alerts */
        .alert {
            display: flex; align-items: flex-start; gap: 12px;
            border-radius: 12px;
            padding: 12px 16px; font-size: .85rem; margin-bottom: 20px;
            animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
        }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        .alert-error { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.25); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25); color: #86efac; animation: fadeIn 0.4s ease both; }

        /* Input group */
        .input-group { margin-bottom: 16px; }
        .input-group label {
            display: block;
            font-size: .75rem; font-weight: 600;
            letter-spacing: .07em; text-transform: uppercase;
            color: rgba(255,255,255,.4); margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i.input-icon {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.25); font-size: 0.9rem;
            pointer-events: none; transition: color .25s;
        }
        .input-wrap input:focus ~ i.input-icon { color: var(--indigo-400); }
        .form-input {
            width: 100%;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px;
            padding: 12px 44px 12px 44px;
            color: #fff; font-size: .95rem;
            transition: all .25s;
        }
        .form-input::placeholder { color: rgba(255,255,255,.2); }
        .form-input:focus {
            outline: none;
            border-color: var(--indigo-500);
            background: rgba(99,102,241,.08);
            box-shadow: 0 0 0 4px rgba(99,102,241,.18);
        }
        .toggle-pass {
            position: absolute; right: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,.25); cursor: pointer; font-size: .9rem;
            transition: color .25s;
        }
        .toggle-pass:hover { color: var(--indigo-400); }

        /* Submit button */
        .btn-submit {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--indigo-600), var(--indigo-500));
            border: none; border-radius: 12px;
            color: #fff; font-size: 0.95rem; font-weight: 600;
            cursor: pointer; letter-spacing: .02em;
            box-shadow: 0 10px 24px rgba(79,70,229,.35);
            transition: all .25s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-top: 6px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(79,70,229,.45);
            filter: brightness(1.1);
        }
        .btn-submit:active { transform: translateY(0); }

        /* Footer links */
        .form-footer {
            display: flex; flex-direction: column; gap: 12px;
            margin-top: 24px; text-align: center;
        }
        .link-text {
            font-size: .85rem; color: rgba(255,255,255,.4);
        }
        .link-text a, .btn-text {
            color: var(--indigo-400); text-decoration: none; font-weight: 600; cursor: pointer; transition: color .2s;
        }
        .link-text a:hover, .btn-text:hover { color: var(--indigo-300); text-decoration: underline; }

        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-size: .85rem;
            color: rgba(255,255,255,.3); cursor: pointer;
            transition: color .25s;
        }
        .back-link:hover { color: rgba(255,255,255,.6); }

        /* Footer */
        .card-footer {
            margin-top: 24px; padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,.07);
            text-align: center;
            font-size: .8rem; color: rgba(255,255,255,.25);
        }
        .card-footer a { color: rgba(255,255,255,.4); text-decoration: none; transition: color .2s; }
        .card-footer a:hover { color: #fff; }
    </style>
</head>
<body>

    <div class="particles" id="particles"></div>

    <div class="card" id="loginCard">

        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-bullhorn"></i></div>
            <h1>Sistem Pengaduan</h1>
            <p>Sarana Sekolah</p>
        </div>

        <!-- Step 1: Role Selection -->
        <div id="selectionArea" class="view-content">
            <p class="role-label">Masuk sebagai</p>
            <div class="role-grid">
                <div class="role-card" onclick="switchView('login', 'siswa')">
                    <div class="role-card-icon siswa"><i class="fas fa-graduation-cap"></i></div>
                    <div class="role-card-info">
                        <h3>Siswa</h3>
                        <p>Laporkan masalah & pantau progres</p>
                    </div>
                    <i class="fas fa-chevron-right role-card-arrow"></i>
                </div>
                <div class="role-card" onclick="switchView('login', 'admin')">
                    <div class="role-card-icon admin"><i class="fas fa-shield-halved"></i></div>
                    <div class="role-card-info">
                        <h3>Administrator</h3>
                        <p>Kelola laporan & umpan balik</p>
                    </div>
                    <i class="fas fa-chevron-right role-card-arrow"></i>
                </div>
            </div>

            <div class="card-footer">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
            </div>
        </div>

        <!-- Step 2: Login Form -->
        <div id="formArea" class="view-content" style="display: none;">
            <div class="form-title">
                <h2 id="loginTitle">Login</h2>
                <p>Masukkan kredensial akun Anda</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php
endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-circle-check"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
            <?php
endif; ?>

            <form action="" method="POST" autocomplete="off">
                <div class="input-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <input type="text" name="username" class="form-input" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="passInput" class="form-input" placeholder="Password" required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye-slash toggle-pass" onclick="togglePassword('passInput', this)"></i>
                    </div>
                </div>

                <input type="hidden" name="source_role" id="sourceRoleInput" value="siswa">

                <button type="submit" name="login" class="btn-submit">
                    <i class="fas fa-arrow-right-to-bracket"></i> Masuk Sekarang
                </button>
            </form>

            <div class="form-footer">
                <p class="link-text" id="regLinkArea">Belum punya akun? <span class="btn-text" onclick="switchView('register')">Daftar sebagai Siswa</span></p>
                <div class="back-link" onclick="switchView('selection')">
                    <i class="fas fa-rotate-left"></i> Ganti peran akses
                </div>
            </div>
        </div>

        <!-- Step 3: Registration Form -->
        <div id="registerArea" class="view-content" style="display: none;">
            <div class="form-title">
                <h2>Daftar Siswa</h2>
                <p>Buat akun baru untuk melapor</p>
            </div>

            <form action="" method="POST" autocomplete="off">
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <div class="input-wrap">
                        <input type="text" name="nama" class="form-input" placeholder="Contoh: Budi Santoso" required>
                        <i class="fas fa-id-card input-icon"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <input type="text" name="username_reg" class="form-input" placeholder="Pilih username unik" required>
                        <i class="fas fa-at input-icon"></i>
                    </div>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password_reg" id="passInputReg" class="form-input" placeholder="Pilih password kuat" required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye-slash toggle-pass" onclick="togglePassword('passInputReg', this)"></i>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Buat Akun Sekarang
                </button>
            </form>

            <div class="form-footer">
                <p class="link-text">Sudah punya akun? <span class="btn-text" onclick="switchView('login', 'siswa')">Login di sini</span></p>
                <div class="back-link" onclick="switchView('selection')">
                    <i class="fas fa-rotate-left"></i> Kembali ke pilihan
                </div>
            </div>
        </div>

    </div>

    <script>
        // Particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 28; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.cssText = `
                left: ${Math.random() * 100}%;
                width: ${Math.random() * 3 + 2}px;
                height: ${Math.random() * 3 + 2}px;
                animation-duration: ${Math.random() * 18 + 12}s;
                animation-delay: ${Math.random() * 12}s;
                opacity: ${Math.random() * .6 + .2};
            `;
            container.appendChild(p);
        }

        let currentRole = 'siswa';

        function switchView(view, role = null) {
            const selectionArea = document.getElementById('selectionArea');
            const formArea = document.getElementById('formArea');
            const registerArea = document.getElementById('registerArea');
            const loginTitle = document.getElementById('loginTitle');
            const regLinkArea = document.getElementById('regLinkArea');

            // Hide all
            selectionArea.style.display = 'none';
            formArea.style.display = 'none';
            registerArea.style.display = 'none';

            if (view === 'selection') {
                selectionArea.style.display = 'block';
            } else if (view === 'login') {
                formArea.style.display = 'block';
                if (role) {
                    currentRole = role;
                    document.getElementById('sourceRoleInput').value = role;
                }
                
                if (currentRole === 'siswa') {
                    loginTitle.textContent = 'Login Siswa';
                    regLinkArea.style.display = 'block';
                } else {
                    loginTitle.textContent = 'Login Admin';
                    regLinkArea.style.display = 'none';
                }
            } else if (view === 'register') {
                registerArea.style.display = 'block';
            }
        }

        function togglePassword(id, el) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                el.classList.remove('fa-eye-slash');
                el.classList.add('fa-eye');
            } else {
                input.type = 'password';
                el.classList.remove('fa-eye');
                el.classList.add('fa-eye-slash');
            }
        }

        // Auto-open views based on state
        <?php if ($error || $success): ?>
            switchView('login', 'siswa');
        <?php
endif; ?>
    </script>
    <?php if (function_exists('display_swal'))
    display_swal(); ?>
</body>
</html>
