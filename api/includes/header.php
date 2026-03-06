<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Sistem Pengaduan Sarana Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* SweetAlert2 Premium Customization */
        .swal2-popup {
            border-radius: 24px !important;
            padding: 2rem !important;
            font-family: 'Inter', sans-serif !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        .swal2-title {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .swal2-styled.swal2-confirm {
            background-color: var(--primary) !important;
            border-radius: 12px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3) !important;
        }
    /* ============================================================
       DESIGN TOKENS
    ============================================================ */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
        --primary:      #4f46e5;
        --primary-dark: #4338ca;
        --primary-soft: #eef2ff;
        --primary-mid:  rgba(79,70,229,.12);
        --success:      #10b981;
        --success-soft: #ecfdf5;
        --warning:      #f59e0b;
        --warning-soft: #fffbeb;
        --danger:       #ef4444;
        --danger-soft:  #fef2f2;
        --info:         #3b82f6;
        --info-soft:    #eff6ff;
        --sidebar-w:    260px;
        --bg:           #f8fafc;
        --white:        #ffffff;
        --border:       #e2e8f0;
        --text:         #0f172a;
        --text-muted:   #64748b;
        --radius:       14px;
        --shadow:       0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md:    0 4px 16px rgba(0,0,0,.08);
        --transition:   all .3s cubic-bezier(.4, 0, .2, 1);
    }

    /* ── Animations ── */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes glow {
        0%, 100% { box-shadow: 0 0 5px rgba(79, 70, 229, 0.2); }
        50% { box-shadow: 0 0 20px rgba(79, 70, 229, 0.4); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    .mesh-gradient {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(236, 72, 153, 0.05) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
        z-index: -1; pointer-events: none;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        color: var(--text);
        line-height: 1.6;
    }
    h1,h2,h3,h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    a { text-decoration: none; color: inherit; }
    ul { list-style: none; }

    /* ============================================================
       LAYOUT
    ============================================================ */
    .dashboard-wrap {
        display: flex;
        min-height: 100vh;
    }

    /* ── SIDEBAR ── */
    .sidebar {
        width: var(--sidebar-w);
        background: var(--white);
        border-right: 1px solid var(--border);
        position: fixed;
        top: 0; left: 0;
        height: 100vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        z-index: 100;
        transition: var(--transition);
        animation: slideInLeft 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }
    .sidebar::-webkit-scrollbar { width: 4px; }
    .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .sidebar-brand {
        padding: 24px 20px 20px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; gap: 12px;
    }
    .brand-icon {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, var(--primary), #818cf8);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1rem; flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(79,70,229,.3);
    }
    .brand-text { line-height: 1.2; }
    .brand-text strong {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: .95rem; font-weight: 800;
        color: var(--primary);
        display: block;
    }
    .brand-text small { font-size: .72rem; color: var(--text-muted); }

    .sidebar-section {
        padding: 16px 12px 4px;
        font-size: .68rem; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase;
        color: #94a3b8;
    }

    .nav-link {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 14px;
        border-radius: 10px;
        margin: 2px 8px;
        font-size: .9rem; font-weight: 500;
        color: var(--text-muted);
        transition: var(--transition);
        position: relative;
    }
    .nav-link i { width: 18px; text-align: center; font-size: .9rem; flex-shrink: 0; }
    .nav-link:hover {
        background: var(--primary-soft);
        color: var(--primary);
    }
    .nav-link.active {
        background: var(--primary-mid);
        color: var(--primary);
        font-weight: 600;
    }
    .nav-link.active::before {
        content: '';
        position: absolute; left: 0; top: 6px; bottom: 6px;
        width: 3px; border-radius: 0 3px 3px 0;
        background: var(--primary);
        animation: scaleIn 0.3s ease both;
    }

    .sidebar-spacer { flex: 1; }
    .sidebar-footer {
        padding: 12px 8px;
        border-top: 1px solid var(--border);
    }
    .nav-link.danger { color: var(--danger); }
    .nav-link.danger:hover { background: var(--danger-soft); color: var(--danger); }

    /* ── MAIN ── */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-w);
        padding: 32px;
        min-height: 100vh;
        background: var(--bg);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }

    /* ── TOP BAR ── */
    .top-bar {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 32px;
        flex-wrap: wrap; gap: 16px;
    }
    .top-bar-left h1 {
        font-size: 1.5rem; font-weight: 800; color: var(--text);
        animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) 0.1s both;
    }
    .top-bar-left p {
        font-size: .875rem; color: var(--text-muted); margin-top: 2px;
    }
    .top-bar-right { display: flex; align-items: center; gap: 12px; }

    .avatar-btn {
        display: flex; align-items: center; gap: 10px;
        background: var(--white); border: 1px solid var(--border);
        border-radius: 50px; padding: 6px 14px 6px 6px;
        font-size: .875rem; font-weight: 500; cursor: pointer;
        transition: var(--transition);
    }
    .avatar-btn:hover { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
    .avatar-circle {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, var(--primary), #818cf8);
        border-radius: 50%; color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }

    .btn-icon {
        width: 38px; height: 38px;
        background: var(--white); border: 1px solid var(--border);
        border-radius: 10px; display: flex; align-items: center; justify-content: center;
        color: var(--text-muted); cursor: pointer; transition: var(--transition);
        font-size: .9rem;
    }
    .btn-icon:hover { border-color: var(--primary); color: var(--primary); }

    /* ── CARDS ── */
    .card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 28px;
        box-shadow: var(--shadow);
    }
    .card-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 22px;
    }
    .card-title { font-size: 1rem; font-weight: 700; }
    .card-subtitle { font-size: .82rem; color: var(--text-muted); margin-top: 2px; }

    /* stat cards */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        display: flex; flex-direction: column; gap: 16px;
        transition: var(--transition);
        position: relative; overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute; top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--card-accent, var(--primary));
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
    .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }
    .stat-num {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2rem; font-weight: 800; line-height: 1;
    }
    .stat-label { font-size: .82rem; color: var(--text-muted); font-weight: 500; }
    .stat-trend {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .75rem; font-weight: 600;
        padding: 3px 8px; border-radius: 50px;
    }

    /* buttons */
    .btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 9px 18px; border-radius: 10px;
        font-size: .875rem; font-weight: 600;
        cursor: pointer; border: none; transition: var(--transition);
    }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-ghost { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--primary-soft); color: var(--primary); border-color: transparent; }
    .btn-danger { background: var(--danger); color: white; }
    .btn-danger:hover { background: #dc2626; }
    .btn-sm { padding: 6px 12px; font-size: .8rem; }

    /* badge */
    .badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 50px;
        font-size: .75rem; font-weight: 600;
    }
    .badge i { font-size: .6rem; }
    .badge-warning  { background: var(--warning-soft);  color: #92400e; }
    .badge-primary  { background: var(--primary-soft);  color: var(--primary); }
    .badge-success  { background: var(--success-soft);  color: #065f46; }
    .badge-danger   { background: var(--danger-soft);   color: #7f1d1d; }

    /* table */
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl th {
        padding: 12px 16px; text-align: left;
        font-size: .75rem; font-weight: 700;
        letter-spacing: .06em; text-transform: uppercase; color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .tbl td {
        padding: 14px 16px;
        font-size: .9rem;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr { transition: var(--transition); }
    .tbl tbody tr:hover { background: var(--bg); }

    /* utilities */
    .text-muted    { color: var(--text-muted); }
    .fw-bold       { font-weight: 700; }
    .d-flex        { display: flex; }
    .align-center  { align-items: center; }
    .gap-8         { gap: 8px; }
    .gap-12        { gap: 12px; }
    .mb-0          { margin-bottom: 0; }
    .mt-auto       { margin-top: auto; }
    .grid-2        { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    @media (max-width: 900px) {
        .sidebar { transform: translateX(-100%); }
        .main-content { margin-left: 0; padding: 20px; }
        .grid-2 { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>
<div class="dashboard-wrap">
    <?php include 'sidebar.php'; ?>
