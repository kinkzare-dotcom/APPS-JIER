<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengaduan Sarana Sekolah</title>
    <meta name="description" content="Sistem Pengaduan Sarana Sekolah – sistem pelaporan kerusakan yang cepat, transparan, dan bisa dipantau real-time.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
    /* ============================================================
       RESET & TOKENS
    ============================================================ */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        --indigo-600: #4f46e5;
        --indigo-500: #6366f1;
        --indigo-400: #818cf8;
        --indigo-100: #e0e7ff;
        --pink-500:   #ec4899;
        --slate-900:  #0f172a;
        --slate-800:  #1e293b;
        --slate-700:  #334155;
        --slate-500:  #64748b;
        --slate-200:  #e2e8f0;
        --slate-100:  #f1f5f9;
        --white:      #ffffff;

        --radius-md: 14px;
        --radius-lg: 22px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,.08);
        --shadow-lg: 0 20px 40px rgba(0,0,0,.10);
        --transition: all .4s cubic-bezier(.4, 0, .2, 1);
    }

    /* ── Utilities Animations ── */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    @keyframes pulse-soft {
        0%, 100% { transform: scale(1); opacity: 0.1; }
        50% { transform: scale(1.05); opacity: 0.15; }
    }

    html { scroll-behavior: smooth; }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--white);
        color: var(--slate-900);
        line-height: 1.65;
        overflow-x: hidden;
    }

    h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.2; }
    a { text-decoration: none; color: inherit; transition: var(--transition); }
    ul { list-style: none; }
    img { display: block; max-width: 100%; }

    /* ── Layout helpers ── */
    .container   { max-width: 1160px; margin: 0 auto; padding: 0 28px; }
    .text-center { text-align: center; }
    .section     { padding: 96px 0; }

    /* ── Buttons ── */
    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 13px 26px; border-radius: 50px;
        font-weight: 600; font-size: .95rem; cursor: pointer;
        transition: var(--transition); border: none;
    }
    .btn-primary {
        background: var(--indigo-600); color: var(--white);
        box-shadow: 0 8px 20px rgba(79,70,229,.3);
    }
    .btn-primary:hover {
        background: #4338ca;
        box-shadow: 0 12px 28px rgba(79,70,229,.4);
        transform: translateY(-3px) scale(1.02);
    }
    .btn-outline {
        background: transparent; color: var(--slate-700);
        border: 1.5px solid var(--slate-200);
    }
    .btn-outline:hover {
        background: var(--slate-100);
        border-color: var(--slate-300);
        transform: translateY(-3px);
    }

    /* chip / badge */
    .chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--indigo-100); color: var(--indigo-600);
        padding: 5px 14px; border-radius: 50px;
        font-size: .78rem; font-weight: 700; letter-spacing: .06em;
        text-transform: uppercase; margin-bottom: 20px;
    }

    /* ============================================================
       NAVBAR
    ============================================================ */
    #navbar {
        position: fixed; top: 0; left: 0; width: 100%; z-index: 999;
        padding: 20px 0;
        transition: var(--transition);
        animation: fadeIn 0.8s ease both;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    
    #navbar.scrolled {
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid rgba(0,0,0,.06);
        padding: 12px 0;
        box-shadow: var(--shadow-sm);
    }
    .nav-inner {
        display: flex; align-items: center; justify-content: space-between;
    }
    .logo {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.35rem; font-weight: 800;
        color: var(--indigo-600);
        display: flex; align-items: center; gap: 10px;
    }
    .logo-icon {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--indigo-600), var(--pink-500));
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1rem;
        box-shadow: 0 6px 14px rgba(79,70,229,.35);
    }
    .nav-actions { display: flex; align-items: center; gap: 12px; }

    /* ============================================================
       HERO
    ============================================================ */
    .hero {
        min-height: 100vh;
        display: flex; align-items: center;
        padding: 120px 0 80px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(ellipse 80% 60% at 60% -10%, #eef2ff, transparent),
            radial-gradient(ellipse 60% 50% at 0% 60%, #fdf2f8, transparent),
            var(--white);
    }
    /* decorative blobs */
    .blob {
        position: absolute; border-radius: 50%;
        filter: blur(80px); pointer-events: none; z-index: 0;
        animation: pulse-soft 8s ease-in-out infinite;
    }
    .blob-1 { width: 550px; height: 550px; background: rgba(99,102,241,.12); top: -120px; right: -80px; }
    .blob-2 { width: 400px; height: 400px; background: rgba(236,72,153,.08); bottom: -60px; left: -80px; animation-delay: -4s; }

    .hero-content { position: relative; z-index: 1; max-width: 760px; }
    .hero-title {
        font-size: clamp(2.4rem, 5vw, 4rem);
        font-weight: 900;
        color: var(--slate-900);
        margin-bottom: 22px;
        letter-spacing: -.03em;
    }
    .hero-title .highlight {
        background: linear-gradient(135deg, var(--indigo-600) 0%, var(--pink-500) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero-subtitle {
        font-size: 1.15rem; color: var(--slate-500);
        max-width: 560px; margin-bottom: 40px; line-height: 1.75;
    }
    .hero-actions { 
        display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 56px;
    }

    /* stats bar */
    .stats-bar {
        display: flex; flex-wrap: wrap; gap: 36px; align-items: center;
        padding-top: 36px;
        border-top: 1px solid var(--slate-200);
    }
    .stat-item {}
    .stat-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.6rem; font-weight: 800; color: var(--slate-900);
        line-height: 1;
    }
    .stat-label { font-size: .82rem; color: var(--slate-500); margin-top: 4px; }

    /* hero illustration placeholder */
    .hero-visual {
        position: absolute; right: 0; top: 50%;
        transform: translateY(-50%);
        width: 45%; max-width: 560px;
        pointer-events: none; z-index: 1;
        display: flex; flex-direction: column; gap: 16px; padding-right: 40px;
    }
    .mock-card {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 20px 24px;
        box-shadow: var(--shadow-md);
        display: flex; align-items: flex-start; gap: 16px;
        animation: float 5s ease-in-out infinite;
    }
    .mock-card:nth-child(2) { animation-delay: .8s; margin-left: 40px; }
    .mock-card:nth-child(3) { animation-delay: 1.6s; }

    .mock-icon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: .95rem;
    }
    .mock-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .mock-icon.green  { background: #f0fdf4; color: #22c55e; }
    .mock-icon.orange { background: #fff7ed; color: #f97316; }
    .mock-text h4  { font-size: .9rem; font-weight: 600; color: var(--slate-800); }
    .mock-text p   { font-size: .78rem; color: var(--slate-500); margin-top: 2px; }
    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: .7rem; font-weight: 600; padding: 3px 9px;
        border-radius: 50px; margin-top: 8px;
    }
    .status-pill.done    { background: #f0fdf4; color: #16a34a; }
    .status-pill.process { background: #eff6ff; color: #2563eb; }
    .status-pill.pending { background: #fff7ed; color: #ea580c; }
    .status-pill i { font-size: .6rem; }

    /* ============================================================
       FEATURES
    ============================================================ */
    .section-heading { margin-bottom: 56px; }
    .section-heading h2 { font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 800; margin-bottom: 14px; }
    .section-heading p  { color: var(--slate-500); font-size: 1.05rem; max-width: 520px; }
    .section-heading.center { text-align: center; }
    .section-heading.center p { margin: 0 auto; }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }
    .feature-card {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-lg);
        padding: 36px 32px;
        transition: var(--transition);
        position: relative; overflow: hidden;
    }
    .feature-card::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(99,102,241,.04), transparent);
        opacity: 0; transition: var(--transition);
        border-radius: inherit;
    }
    .feature-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--indigo-400); }
    .feature-card:hover::after { opacity: 1; }

    .f-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 22px;
        transition: var(--transition);
    }
    .f-icon.purple { background: #ede9fe; color: var(--indigo-600); }
    .f-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .f-icon.green  { background: #f0fdf4; color: #16a34a; }
    .f-icon.pink   { background: #fdf2f8; color: var(--pink-500); }
    .f-icon.orange { background: #fff7ed; color: #f97316; }
    .f-icon.teal   { background: #f0fdfa; color: #0d9488; }
    .feature-card:hover .f-icon { transform: rotate(8deg) scale(1.1); }

    .feature-card h3 { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; }
    .feature-card p  { color: var(--slate-500); font-size: .92rem; line-height: 1.7; }

    /* ============================================================
       HOW IT WORKS
    ============================================================ */
    .how-section { background: var(--slate-100); }
    .steps { display: flex; flex-wrap: wrap; gap: 32px; justify-content: center; }
    .step {
        flex: 1; min-width: 200px; max-width: 260px; text-align: center;
    }
    .step-num {
        width: 56px; height: 56px; border-radius: 50%;
        background: linear-gradient(135deg, var(--indigo-600), var(--indigo-500));
        color: white; font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.4rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 8px 18px rgba(79,70,229,.3);
    }
    .step h4 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
    .step p  { font-size: .875rem; color: var(--slate-500); }
    .step-divider { display: none; }

    /* ============================================================
       FAQ
    ============================================================ */
    .faq-list { max-width: 760px; margin: 0 auto; }
    .faq-item {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        margin-bottom: 12px;
        overflow: hidden;
        transition: var(--transition);
    }
    .faq-item:hover { border-color: var(--indigo-400); }
    .faq-item.open { border-color: var(--indigo-400); box-shadow: 0 4px 16px rgba(99,102,241,.1); }

    .faq-btn {
        width: 100%; padding: 20px 24px;
        background: none; border: none; cursor: pointer;
        display: flex; justify-content: space-between; align-items: center;
        text-align: left; font-size: .98rem; font-weight: 600;
        color: var(--slate-800); gap: 16px;
        transition: var(--transition);
    }
    .faq-item.open .faq-btn { color: var(--indigo-600); }

    .faq-icon {
        width: 30px; height: 30px; flex-shrink: 0; border-radius: 50%;
        background: var(--slate-100); color: var(--slate-500);
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; transition: var(--transition);
    }
    .faq-item.open .faq-icon { background: var(--indigo-600); color: white; transform: rotate(45deg); }

    .faq-body {
        max-height: 0; overflow: hidden;
        transition: max-height .4s ease, padding .4s ease;
        padding: 0 24px; color: var(--slate-500); font-size: .92rem; line-height: 1.75;
    }
    .faq-item.open .faq-body { max-height: 300px; padding-bottom: 22px; }

    /* ============================================================
       CTA BANNER
    ============================================================ */
    .cta-section {
        background: linear-gradient(135deg, var(--slate-900) 0%, var(--indigo-600) 100%);
        padding: 80px 0; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .cta-section h2 {
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        color: white; margin-bottom: 16px; position: relative;
    }
    .cta-section p  { color: rgba(255,255,255,.65); font-size: 1.05rem; margin-bottom: 36px; position: relative; }
    .cta-section .btn-white {
        background: white; color: var(--indigo-600);
        font-weight: 700; padding: 15px 32px; border-radius: 50px;
        box-shadow: 0 10px 24px rgba(0,0,0,.2);
        position: relative;
    }
    .cta-section .btn-white:hover { transform: translateY(-3px); box-shadow: 0 18px 36px rgba(0,0,0,.25); }

    /* ============================================================
       FOOTER
    ============================================================ */
    footer {
        background: var(--slate-900); color: white;
        padding: 72px 0 32px;
    }
    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.4fr;
        gap: 48px; margin-bottom: 56px;
    }
    .footer-brand p { color: rgba(255,255,255,.45); font-size: .9rem; line-height: 1.8; margin-top: 16px; max-width: 280px; }
    .footer-col h4 { font-size: .875rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 20px; }
    .footer-col ul { display: flex; flex-direction: column; gap: 10px; }
    .footer-col a  { color: rgba(255,255,255,.55); font-size: .9rem; display: inline-flex; align-items: center; gap: 6px; }
    .footer-col a:hover { color: white; padding-left: 4px; }

    .social-links { display: flex; gap: 10px; margin-top: 4px; }
    .social-btn {
        width: 38px; height: 38px; border-radius: 10px;
        background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
        display: flex; align-items: center; justify-content: center;
        color: rgba(255,255,255,.55); transition: var(--transition);
    }
    .social-btn:hover { background: var(--indigo-600); border-color: var(--indigo-600); color: white; transform: translateY(-2px); }

    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,.07);
        padding-top: 28px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        font-size: .85rem; color: rgba(255,255,255,.35);
    }
    .footer-bottom-links { display: flex; gap: 20px; }
    .footer-bottom-links a { color: rgba(255,255,255,.35); }
    .footer-bottom-links a:hover { color: white; }

    /* ============================================================
       RESPONSIVE
    ============================================================ */
    @media (max-width: 900px) {
        .hero-visual { display: none; }
        .hero-content { max-width: 100%; text-align: center; margin: 0 auto; display: flex; flex-direction: column; align-items: center; }
        .hero-subtitle { margin-left: auto; margin-right: auto; }
        .hero-actions { justify-content: center; }
        .stats-bar { justify-content: center; }
        .footer-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .container { padding: 0 20px; }
        
        /* Navbar Responsiveness */
        #navbar { padding: 12px 0; }
        .nav-inner { gap: 10px; }
        .logo { font-size: .95rem; }
        .logo-icon { width: 32px; height: 32px; font-size: .8rem; }
        .nav-actions .btn-outline { display: none; } /* Hanya tampilkan tombol utama di HP */
        .nav-actions .btn { padding: 8px 14px; font-size: .8rem; }
        
        /* Hero Responsiveness */
        .hero { padding: 100px 0 50px; }
        .hero-title { font-size: 2.2rem; line-height: 1.25; margin-bottom: 16px; }
        .hero-subtitle { font-size: 1rem; margin-bottom: 28px; }
        .hero-actions { flex-direction: column; width: 100%; gap: 12px; }
        .hero-actions .btn { width: 100%; justify-content: center; padding: 14px 20px; }
        
        /* Layout Adjustments */
        .section { padding: 60px 0; }
        .section-heading { margin-bottom: 40px; }
        .section-heading h2 { font-size: 1.6rem; }
        .feature-card { padding: 24px 20px; }
        
        .footer-grid { grid-template-columns: 1fr; gap: 32px; }
        .footer-bottom { flex-direction: column; text-align: center; gap: 16px; }
        .stats-bar { gap: 20px; flex-direction: column; text-align: center; }
        .steps { flex-direction: column; align-items: center; }
        
        .blob-1 { width: 300px; height: 300px; right: -50px; }
        .blob-2 { width: 250px; height: 250px; left: -50px; }
    }
    </style>
</head>
<body>

<!-- ============================================================
     NAVBAR
============================================================ -->
<nav id="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-bullhorn"></i></div>
            Pengaduan Sarana Sekolah
        </a>
        <div class="nav-actions">
            <a href="login.php" class="btn btn-outline" style="padding:10px 22px;">Masuk</a>
            <a href="login.php" class="btn btn-primary" style="padding:10px 22px;">Buat Laporan <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</nav>

<!-- ============================================================
     HERO
============================================================ -->
<section class="hero">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="hero-content">
            <span class="chip" data-aos="fade-down" data-aos-duration="1000"><i class="fas fa-circle-check"></i> UKK RPL 2026</span>
            <h1 class="hero-title" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                Sarana Rusak?<br>
                <span class="highlight">Laporkan Sekarang.</span>
            </h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                Sistem Pengaduan Sarana Sekolah menghubungkan siswa dengan unit pemeliharaan
                secara digital — cepat, transparan, dan bisa dipantau real-time.
            </p>
            <div class="hero-actions" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                <a href="login.php" class="btn btn-primary" style="padding:15px 32px;font-size:1rem;">
                    <i class="fas fa-plus-circle"></i> Buat Laporan
                </a>
                <a href="#features" class="btn btn-outline" style="padding:15px 32px;font-size:1rem;">
                    Pelajari Lebih <i class="fas fa-chevron-down"></i>
                </a>
            </div>

            <div class="stats-bar" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                <div class="stat-item">
                    <div class="stat-num">120+</div>
                    <div class="stat-label">Laporan Terselesaikan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">&lt;24 Jam</div>
                    <div class="stat-label">Rata-rata Respons</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">98%</div>
                    <div class="stat-label">Tingkat Kepuasan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating cards illustration -->
    <div class="hero-visual" data-aos="fade-left" data-aos-duration="1500" data-aos-delay="500">
        <div class="mock-card">
            <div class="mock-icon blue"><i class="fas fa-chair"></i></div>
            <div class="mock-text">
                <h4>Kursi Kelas 10A Rusak</h4>
                <p>Dilaporkan · 2 jam yang lalu</p>
                <span class="status-pill process"><i class="fas fa-circle-dot"></i> Diproses</span>
            </div>
        </div>
        <div class="mock-card">
            <div class="mock-icon orange"><i class="fas fa-faucet-drip"></i></div>
            <div class="mock-text">
                <h4>Keran Kamar Mandi Bocor</h4>
                <p>Dilaporkan · 1 hari lalu</p>
                <span class="status-pill pending"><i class="fas fa-circle-dot"></i> Menunggu</span>
            </div>
        </div>
        <div class="mock-card">
            <div class="mock-icon green"><i class="fas fa-lightbulb"></i></div>
            <div class="mock-text">
                <h4>Lampu Lab Komputer Mati</h4>
                <p>Dilaporkan · 3 hari lalu</p>
                <span class="status-pill done"><i class="fas fa-circle-check"></i> Selesai</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES
============================================================ -->
<section class="section" id="features">
    <div class="container">
        <div class="section-heading center" data-aos="fade-up" data-aos-duration="800">
            <span class="chip"><i class="fas fa-sparkles"></i> Unggulan</span>
            <h2>Semua yang Kamu Butuhkan</h2>
            <p>Dirancang untuk kemudahan pelaporan dan pengelolaan fasilitas sekolah secara efisien.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="f-icon purple"><i class="fas fa-bolt"></i></div>
                <h3>Respons Instan</h3>
                <p>Laporan langsung masuk ke sistem admin dalam hitungan detik, mempercepat waktu penanganan kerusakan.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="f-icon blue"><i class="fas fa-layer-group"></i></div>
                <h3>Kategorisasi Cerdas</h3>
                <p>Laporan dikelompokkan otomatis berdasarkan jenis kerusakan dan area lokasi, memudahkan prioritas perbaikan.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="f-icon green"><i class="fas fa-chart-line"></i></div>
                <h3>Tracking Real-time</h3>
                <p>Pantau status laporan mulai dari pengajuan, verifikasi, proses, hingga selesai dikerjakan secara langsung.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="f-icon pink"><i class="fas fa-shield-halved"></i></div>
                <h3>Aman & Terpercaya</h3>
                <p>Data laporan terenkripsi dan hanya dapat diakses oleh pengguna yang berwenang sesuai role masing-masing.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="f-icon orange"><i class="fas fa-message-lines"></i></div>
                <h3>Tanggapan Admin</h3>
                <p>Admin dapat memberikan komentar dan update progress langsung pada setiap laporan yang masuk.</p>
            </div>
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="f-icon teal"><i class="fas fa-chart-bar"></i></div>
                <h3>Laporan & Statistik</h3>
                <p>Admin mendapatkan ringkasan data laporan dalam bentuk grafik dan tabel untuk pengambilan keputusan.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     HOW IT WORKS
============================================================ -->
<section class="section how-section">
    <div class="container">
        <div class="section-heading center" data-aos="fade-up">
            <span class="chip"><i class="fas fa-list-check"></i> Cara Kerja</span>
            <h2>3 Langkah Mudah</h2>
            <p>Melapor tidak pernah semudah ini. Hanya butuh beberapa menit.</p>
        </div>

        <div class="steps">
            <div class="step" data-aos="fade-up" data-aos-delay="100">
                <div class="step-num">1</div>
                <h4>Login ke Akun</h4>
                <p>Masuk menggunakan akun siswa yang telah terdaftar di sistem sekolah.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="200">
                <div class="step-num">2</div>
                <h4>Isi Form Laporan</h4>
                <p>Pilih kategori, tulis deskripsi kerusakan, dan lampirkan foto jika ada.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="300">
                <div class="step-num">3</div>
                <h4>Pantau Progress</h4>
                <p>Cek status laporan secara real-time dan terima tanggapan dari admin.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FAQ
============================================================ -->
<section class="section" id="faq">
    <div class="container">
        <div class="section-heading center" data-aos="fade-up">
            <span class="chip"><i class="fas fa-circle-question"></i> FAQ</span>
            <h2>Pertanyaan Umum</h2>
            <p>Jawaban untuk hal-hal yang sering ditanyakan tentang Sistem Pengaduan Sarana Sekolah.</p>
        </div>

        <div class="faq-list">
            <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                <button class="faq-btn">
                    Bagaimana cara melaporkan kerusakan sarana?
                    <span class="faq-icon"><i class="fas fa-plus"></i></span>
                </button>
                <div class="faq-body">
                    Login ke akun Siswa, klik tombol <strong>Buat Laporan</strong>, isi kategori dan deskripsi kerusakan, unggah foto bila perlu, lalu kirimkan. Laporan langsung diterima oleh admin.
                </div>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                <button class="faq-btn">
                    Apakah saya bisa melihat tanggapan admin?
                    <span class="faq-icon"><i class="fas fa-plus"></i></span>
                </button>
                <div class="faq-body">
                    Ya. Setiap laporan yang sudah diproses memiliki kolom tanggapan admin. Kamu bisa melihatnya langsung dari halaman <strong>Riwayat Laporan</strong> di dashboard siswa.
                </div>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                <button class="faq-btn">
                    Berapa lama proses perbaikan setelah laporan terkirim?
                    <span class="faq-icon"><i class="fas fa-plus"></i></span>
                </button>
                <div class="faq-body">
                    Verifikasi laporan dijamin maksimal <strong>24 jam</strong> setelah dikirim. Waktu perbaikan aktual tergantung pada tingkat kerusakan dan ketersediaan teknisi.
                </div>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                <button class="faq-btn">
                    Siapa saja yang bisa mengakses sistem ini?
                    <span class="faq-icon"><i class="fas fa-plus"></i></span>
                </button>
                <div class="faq-body">
                    Sistem Pengaduan Sarana Sekolah memiliki dua peran: <strong>Siswa</strong> (dapat membuat dan memantau laporan) dan <strong>Admin</strong> (dapat mengelola seluruh laporan, kategori, dan pengguna).
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA
============================================================ -->
<section class="cta-section">
    <div class="container" style="position:relative;">
        <h2 data-aos="zoom-in">Siap Melaporkan Kerusakan?</h2>
        <p data-aos="zoom-in" data-aos-delay="100">Bergabung dan bantu sekolah kita menjadi tempat belajar yang lebih baik.</p>
        <a href="login.php" class="btn btn-white" data-aos="zoom-in" data-aos-delay="200">
            <i class="fas fa-arrow-right-to-bracket"></i> Masuk & Buat Laporan
        </a>
    </div>
</section>

<!-- ============================================================
     FOOTER
============================================================ -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="index.php" class="logo" style="color:white; margin-bottom:0;">
                    <div class="logo-icon"><i class="fas fa-bullhorn"></i></div>
                    Pengaduan Sarana Sekolah
                </a>
                <p>Digitalisasi pengaduan sarana sekolah untuk pendidikan yang lebih baik dan lingkungan belajar yang nyaman.</p>
                <div class="social-links" style="margin-top:20px;">
                    <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="login.php"><i class="fas fa-chevron-right fa-xs"></i> Login Siswa</a></li>
                    <li><a href="login.php"><i class="fas fa-chevron-right fa-xs"></i> Admin Gate</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Support</h4>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right fa-xs"></i> Pusat Bantuan</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right fa-xs"></i> Panduan Pengguna</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right fa-xs"></i> Kontak Sekolah</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Info</h4>
                <ul>
                    <li><a href="#features"><i class="fas fa-chevron-right fa-xs"></i> Fitur Unggulan</a></li>
                    <li><a href="#faq"><i class="fas fa-chevron-right fa-xs"></i> FAQ</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 UKK RPL · Sistem Pengaduan Sarana Sekolah</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================
     SCRIPTS
============================================================ -->
<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
/* ── Initialize AOS ── */
AOS.init({
    duration: 800,
    easing: 'ease-in-out-cubic',
    once: true, // Animasi hanya berjalan sekali saat di-scroll
    offset: 50,
});

/* ── Navbar scroll effect ── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
}, { passive: true });

/* ── FAQ accordion ── */
document.querySelectorAll('.faq-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');

        // close all
        document.querySelectorAll('.faq-item.open').forEach(o => o.classList.remove('open'));

        // toggle current
        if (!isOpen) item.classList.add('open');
    });
});

/* ── Smooth scroll for anchor links ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});
</script>

</body>
</html>
