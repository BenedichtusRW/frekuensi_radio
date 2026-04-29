<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Balmon Lampung - Portal Monitoring')</title>

    <!-- Favicon Balmon Lampung -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-balmon-lampung-transparent.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-balmon-lampung-transparent.png') }}">

    <!-- Livewire Styles (SPA Navigation) -->
    @livewireStyles

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" onerror="this.style.display='none'">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ANTI-INSPECT PROTECTION (HANYA UNTUK NON-SUPER ADMIN) -->
    @if(!auth()->check() || auth()->user()->role !== 'super_admin')
    <script>
        const redirect = () => window.location.href = "{{ url('/404-not-found') }}";

        // 1. Matikan Klik Kanan & LANGSUNG TENDANG KE 404
        document.addEventListener('contextmenu', e => {
            e.preventDefault();
            redirect();
        });

        // 2. Matikan Shortcut Keyboard & LANGSUNG TENDANG KE 404
        document.onkeydown = function(e) {
            const forbidden = [
                e.keyCode == 123, // F12
                (e.ctrlKey && e.shiftKey && e.keyCode == 73), // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode == 74), // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode == 85) // Ctrl+U
            ];
            
            if (forbidden.some(condition => condition)) {
                e.preventDefault();
                redirect();
                return false;
            }
        };

        // 3. SENSOR JEBAKAN TINGKAT TINGGI
        (function() {
            // Teknik 1: Deteksi Ukuran Jendela
            const checkSize = () => {
                const threshold = 100;
                if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                    redirect();
                }
            };

            // Teknik 2: Console Poisoning (Getter Trap)
            const devtools = new Image();
            Object.defineProperty(devtools, 'id', {
                get: function() {
                    redirect();
                }
            });

            // Teknik 3: Debugger Loop
            const checkDebugger = () => {
                const start = performance.now();
                debugger;
                if (performance.now() - start > 100) {
                    redirect();
                }
            };

            setInterval(() => {
                checkSize();
                console.log(devtools);
                console.clear();
                checkDebugger();
            }, 1000);
        })();
    </script>
    @endif

    <style>
        :root {
            --balmon-navy: #0f172a;
            --balmon-blue: #2563eb;
            --balmon-sky: #38bdf8;
            --balmon-emerald: #10b981;
            --balmon-slate: #64748b;
            --balmon-ink: #0b2239;
            --balmon-background: #f1f5f9;
            /* Slate-100 for better depth contrast against white cards */
        }

        html {
            /* KUNCI PASAK: Ruang ini akan selalu ada agar Jam & Tombol TIDAK PERNAH BERGERAK. */
            scrollbar-gutter: stable !important;
            background-color: #f8fafc !important;
            overflow-x: hidden;
            /* CRITICAL FIX: Do NOT use height: 100%, it causes scroll-to-top bug on modals */
            min-height: 100vh;
        }

        html::-webkit-scrollbar { width: 8px; }
        html::-webkit-scrollbar-track { background: transparent !important; }
        html::-webkit-scrollbar-thumb { background-color: #475569; border-radius: 10px; border: 2px solid #f8fafc; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(56, 189, 248, 0.03) 0, transparent 50%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.012'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-attachment: scroll; /* Default for mobile */
            color: #1e293b;
            overflow-x: hidden;
            /* CRITICAL FIX */
            min-height: 100vh;
        }

        @media (min-width: 992px) {
            body {
                background-attachment: fixed; /* Fixed only for desktop */
            }
        }

        /* Docked Sidebar Transformation */
        .sidebar-desktop {
            width: 240px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            height: 100vh;
            background: #ffffff !important;
            color: #0f172a;
            z-index: 1050;
            border-radius: 0;
            /* Seamless Docked Style */
            border: none;
            border-right: 1px solid #e5e7eb;
            box-shadow: none;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-wrapper {
            padding: 2rem 1.25rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
        }

        .balmon-logo {
            max-height: 48px;
            width: 48px;
            object-fit: contain;
            display: block;
            margin-bottom: 1rem;
            border-radius: 50%;
            padding: 4px;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .brand-subtitle {
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #64748b;
            /* Slate-500 */
            margin-bottom: 0.2rem;
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .sidebar-nav {
            padding: 1.25rem 1.5rem;
        }

        .nav-heading {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
        }

        /* Remove Blue Focus Box on Buttons */
        .btn:focus,
        .btn-close:focus,
        .nav-link:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .sidebar-desktop .nav-link,
        .offcanvas .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.85rem 1.25rem;
            color: #64748b;
            border-radius: 16px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 0.4rem;
            position: relative;
            text-decoration: none;
            /* Force hardware acceleration for content */
            transform: translateZ(0);
        }

        .sidebar-desktop .nav-link:hover,
        .offcanvas .nav-link:hover {
            color: #2563eb;
            background: #f8fafc;
        }

        .sidebar-desktop .nav-link.active,
        .offcanvas .nav-link.active {
            color: #4f46e5 !important;
            /* Indigo-600 */
            background: rgba(79, 70, 229, 0.05) !important;
            /* bg-indigo-50/50 Glassmorphism */
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            border-top-right-radius: 16px;
            /* Keep existing nav-link radius */
            border-bottom-right-radius: 16px;
            font-weight: 700;
        }

        /* Rounded indicator bar refinement */
        .sidebar-desktop .nav-link.active::before,
        .offcanvas .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.75rem;
            bottom: 0.75rem;
            width: 4px;
            background: #4f46e5;
            border-radius: 0 10px 10px 0;
            /* rounded-full style indicator bar */
        }

        .sidebar-desktop .nav-link i,
        .sidebar-desktop .nav-link svg,
        .offcanvas .nav-link i,
        .offcanvas .nav-link svg {
            display: inline-block !important;
            transition: all 0.2s ease;
            flex-shrink: 0;
            color: inherit;
            width: 18px !important;
            height: 18px !important;
            vertical-align: middle;
        }

        .sidebar-desktop .nav-link.active i,
        .sidebar-desktop .nav-link.active svg,
        .offcanvas .nav-link.active i,
        .offcanvas .nav-link.active svg {
            color: #4f46e5 !important;
        }

        /* Header Optimization: Fixed only on Desktop, Scroll on Mobile for speed */
        .app-topbar {
            background: #fff;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            margin: 0;
            border-radius: 0;
            border-bottom: 1px solid #e5e7eb;
            z-index: 1000;
            position: relative; /* Changed from fixed for mobile speed */
        }

        @media (min-width: 992px) {
            .app-topbar {
                position: fixed;
                top: 0;
                right: 0;
                left: 240px;
                padding: 0 2rem;
            }
        }

        .breadcrumb-item {
            font-size: 0.78rem;
            /* Reduced from 0.85rem */
            font-weight: 600;
            color: #64748b;
        }

        .breadcrumb-item.active {
            color: #0f172a;
        }

        .breadcrumb-divider {
            color: #cbd5e1;
            font-size: 0.7rem;
            margin: 0 0.5rem;
        }

        .breadcrumb-item.active {
            color: #0f172a;
            font-weight: 700;
        }

        .topbar-clock {
            font-weight: 700;
            color: #475569;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-variant-numeric: tabular-nums;
            min-width: 180px;
            justify-content: flex-end;
            /* OBAT MANJUR: Matikan transisi agar tidak goyang saat detik berganti atau modal buka */
            transition: none !important;
        }

        .main-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 0; /* Remove compensation because header is not fixed anymore */
        }

        @media (min-width: 992px) {
            .main-content {
                padding-top: 64px;
            }
        }

        .content-body {
            flex: 1;
            padding: 1rem;
        }

        @media (min-width: 992px) {
            .content-body {
                padding: 1.5rem;
            }
        }

        .app-footer {
            padding: 1rem 2rem;
            background: #ffffff;
            color: #94a3b8;
            /* Slate-400 */
            font-size: 0.7rem;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            border-radius: 0;
            box-shadow: none;
            margin-top: auto;
            position: relative;
            z-index: 10;
        }

        .footer-credit {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            margin-top: 0.25rem;
            filter: grayscale(1);
            opacity: 0.8;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 240px;
            }
        }

        /* Ensure Hamburger Toggle is Always Clickable */
        [data-bs-toggle="offcanvas"] {
            position: relative;
            z-index: 1100 !important;
        }

        /* Modern Optimized Sidebar - High Performance Hardware Accelerated */
        .offcanvas.offcanvas-start {
            width: 280px !important;
            height: 100% !important;
            background: #ffffff !important;
            border: none !important;
            border-right: 1px solid rgba(0, 0, 0, 0.05) !important;
            border-radius: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            
            /* ZERO-CPU POLICY: Hardware Acceleration & Isolation */
            transform: translate3d(-100%, 0, 0) !important;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            perspective: 1000px;
            will-change: transform;
            contain: layout paint;
            z-index: 2000 !important;
            
            /* High-Performance Transition Curve - Eliminasi Double-Step Animation */
            transition: transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1.0) !important;
        }

        .offcanvas.offcanvas-start.show {
            transform: translate3d(0, 0, 0) !important;
        }

        /* Hardware Level Lock on Main Content - Instant Snap Navigation */
        .main-content {
            will-change: auto; 
        }

        .offcanvas-header {
            background: transparent !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 1.5rem;
        }

        .offcanvas-body {
            background: transparent !important;
        }

        .offcanvas .nav-link {
            border-radius: 12px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1.25rem;
        }

        .offcanvas .nav-link.active {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(79, 70, 229, 0.05)) !important;
        }

        /* (Removed logout overlay CSS) */

        /* Mobile Optimization: Less Boxy, More Flow */
        @media (max-width: 991.98px) {
            .app-topbar {
                background: #ffffff !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
            }
            .content-body {
                padding: 1rem;
            }
            .card {
                border-radius: 20px !important;
                border: 1px solid #eef2f6 !important;
                box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.05) !important;
            }
            /* Remove pattern background on mobile for speed */
            body {
                background-image: none !important;
                background-color: #f8fafc !important;
            }
            /* GPU-accelerated table scroll on mobile */
            .table-responsive,
            .mosfet-table-wrap {
                -webkit-overflow-scrolling: touch;
                /* will-change: auto — avoid scroll-position yang boros GPU layer di mobile */
                will-change: auto;
            }
            /* Kill all heavy effects on mobile scroll containers */
            .table-responsive *,
            .mosfet-table-wrap * {
                box-shadow: none !important;
            }

            /* RAM RELIEF: Hide heavy charts while sidebar is in motion */
            body.sidebar-moving canvas,
            body.sidebar-moving .chart-card {
                display: none !important;
            }
        }

        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 1.5rem;
            border-top: 1px solid #f8fafc;
        }

        .version-tag {
            font-size: 0.65rem;
            color: #cbd5e1;
            /* Slate-300 */
            font-weight: 500;
            text-align: center;
            letter-spacing: 0.05em;
        }

        /* --- SINGLE MANUAL BACKDROP (Prevents Stacking/Darkening) --- */
        #mobileBackdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5); /* Semi-transparent Slate-900 */
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
            pointer-events: none;
        }
        #mobileBackdrop.visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto; /* Blocks interaction with content beneath */
        }

        /* Close Button Hint Animation */
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .hint-red {
            animation: pulse-red 0.6s ease-out !important;
            background-color: rgba(239, 68, 68, 0.2) !important;
            border-radius: 50% !important;
            outline: 2px solid #ef4444 !important;
        }

        /* Lighten mobile sidebar to reduce lag */
        @media (max-width: 991.98px) {
            .offcanvas {
                box-shadow: none !important;
                border-right: 1px solid #e2e8f0 !important;
                background: #ffffff !important;
            }
        }

        .offcanvas { z-index: 1050 !important; }

        /* Anti-Reflow Body Lock */
        body.modal-open,
        body.offcanvas-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        .modal {
            padding-right: 0 !important;
            overflow: hidden !important;
        }

        .modal-dialog {
            margin: 1.75rem auto;
            max-width: 90%;
            height: calc(100% - 3.5rem);
            display: flex;
            align-items: center;
        }

        .modal-content {
            border-radius: 1.5rem;
            max-height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: #ffffff;
        }

        .modal-body {
            overflow-y: auto !important;
            padding: 2rem;
            flex: 1;
        }

        /* Batang geser tunggal di dalam kotak putih */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        .modal-body::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 10px;
        }
        .modal-body::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        /* Ensure fixed elements don't shift */
        .app-topbar, .sidebar-desktop {
            transition: none !important;
        }

        /* Logout Button Style */
        .btn-logout {
            width: 100%;
            background: #fff;
            border: 1px solid #fee2e2;
            color: #ef4444;
            padding: 0.6rem;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
        }

        .btn-logout:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
            transform: translateY(-1px);
        }

        .btn-logout i {
            width: 16px;
            height: 16px;
        }

        /* Icon Stroke Weight Normalization */
        .nav-link svg,
        .metric-card svg {
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            vector-effect: non-scaling-stroke;
        }


        /* Mobile Shield Overlay */
        #mobile-shield {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 999999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        #mobile-shield .shield-icon {
            width: 120px;
            height: 120px;
            background: #f8fafc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: #6366f1;
        }

        #mobile-shield h2 {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        #mobile-shield p {
            color: #64748b;
            max-width: 300px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Detect Mobile/Tablet Devices */
        @media (max-width: 1024px) {
            #mobile-shield {
                display: flex;
            }
            /* Hide main content to save browser resources */
            body > *:not(#mobile-shield) {
                display: none !important;
            }
        }

        /* 
         * ANTI-JUMP SYSTEM: Mencegah loncatan ke atas saat klik modal
         */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }
        
        /* Memastikan container utama tidak reset scroll */
        .h-screen.overflow-hidden {
            display: flex !important;
        }
        .btn-confirm-cancel:hover { color: #64748b; }
        
        /* Utility Classes for Premium Modals */
        .bg-blue-50 { background-color: #eff6ff !important; }
        .border-blue-100 { border-color: #dbeafe !important; }
        .text-blue-900 { color: #1e3a8a !important; }
        .text-blue-600 { color: #2563eb !important; }
        .rounded-5 { border-radius: 2rem !important; }

        /* --- PREMIUM MODAL SYSTEM --- */
        .modal-premium-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: center;
            z-index: 10000; opacity: 0; pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-premium-overlay.active { opacity: 1; pointer-events: auto; }
        
        /* Ensure SweetAlert2 always appears above premium modals */
        .swal2-container { z-index: 100000 !important; }
        
        .modal-premium-container {
            width: 90%; max-width: 380px;
            transform: scale(0.92); transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-premium-overlay.active .modal-premium-container { transform: scale(1); }
        
        .modal-premium-content {
            background: white; padding: 2rem; border-radius: 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            text-align: center;
        }

        /* --- Global Confirm Modal Refinement --- */
        .confirm-icon-circle {
            width: 60px; height: 60px; background: #fff1f2;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 1.25rem;
        }
        .confirm-title { font-weight: 800; color: #0f172a; margin-bottom: 0.4rem; font-size: 1.1rem; }
        .confirm-msg { color: #64748b; font-size: 0.825rem; line-height: 1.5; margin-bottom: 1.75rem; }
        .confirm-actions { display: flex; flex-direction: column; gap: 0.5rem; }
        .btn-premium-ok { 
            background: #0f172a; color: white; border: none; 
            padding: 0.8rem; border-radius: 1rem; font-weight: 700;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .btn-premium-ok:hover { background: #000; transform: translateY(-2px); }
        .btn-premium-cancel {
            background: transparent; color: #94a3b8; border: none;
            padding: 0.4rem; font-size: 0.78rem; font-weight: 600; text-decoration: none;
        }
        .btn-premium-cancel:hover { color: #64748b; }

        /* SweetAlert Premium Customization */
        .swal2-backdrop-show {
            backdrop-filter: blur(8px) !important;
            background: rgba(15, 23, 42, 0.4) !important;
        }
        .swal-premium-popup {
            border-radius: 2.5rem !important;
            padding: 2rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            max-width: 420px !important;
        }
        .swal-premium-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin-bottom: 0.5rem !important;
        }
        .swal-premium-html {
            font-size: 0.85rem !important;
            color: #64748b !important;
            line-height: 1.6 !important;
        }
        .swal-premium-input {
            border-radius: 1rem !important;
            font-size: 0.95rem !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            margin-top: 1rem !important;
        }
        .swal-premium-confirm {
            border-radius: 1rem !important;
            font-weight: 700 !important;
            padding: 0.75rem 2rem !important;
            font-size: 0.85rem !important;
        }
        .swal-premium-cancel {
            border-radius: 1rem !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
        }

        /* Global Avatar View Overlay (WhatsApp Style) */
        .avatar-view-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px); z-index: 10000;
            display: none; align-items: center; justify-content: center;
            opacity: 0; transition: all 0.25s ease; cursor: pointer;
        }
        .avatar-view-overlay.active { display: flex; opacity: 1; }
        .avatar-wa-card {
            width: min(320px, 90vw); background: #ffffff; border-radius: 0;
            overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transform: scale(0.85); transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative; cursor: default;
        }
        .avatar-view-overlay.active .avatar-wa-card { transform: scale(1); }
        .avatar-wa-header {
            position: absolute; top: 0; left: 0; width: 100%; padding: 12px 16px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.5), transparent);
            color: #ffffff; font-weight: 600; font-size: 0.95rem; z-index: 2;
            display: flex; justify-content: space-between; align-items: center;
        }
        .avatar-wa-img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; display: block; }
        .avatar-wa-actions {
            height: 48px; display: flex; align-items: center; justify-content: space-around;
            background: #ffffff; border-top: 1px solid #f1f5f9;
        }
        .wa-action-btn { background: none; border: none; color: #6366f1; padding: 8px; cursor: pointer; transition: opacity 0.2s; }
        .wa-action-btn:hover { opacity: 0.7; }

        /* Global Avatar Utility */
        .avatar-circle, .avatar-circle-sm, .avatar-wa-img {
            width: 38px; height: 38px; border-radius: 12px; object-fit: cover;
            cursor: pointer; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            
            /* ANTI-COLONG PROTECTION */
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            pointer-events: auto; /* Ensure click still works */
        }
        .avatar-circle-sm { width: 24px; height: 24px; border-radius: 8px; border-width: 1.5px; }
        .avatar-wa-img { width: 100%; height: auto; aspect-ratio: 1/1; border: none; border-radius: 0; box-shadow: none; cursor: default; }
        
        .avatar-circle:hover, .avatar-circle-sm:hover {
            transform: scale(1.15) rotate(2deg);
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        .avatar-placeholder {
            width: 38px; height: 38px; border-radius: 12px;
            background: #f8fafc; display: flex; align-items: center; justify-content: center;
            color: #64748b; font-weight: 800; font-size: 0.85rem;
            border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: default;
        }
    </style>
</head>

<body class="{{ $bodyClass ?? '' }}">
    {{-- Mobile Shield Overlay --}}
    <div id="mobile-shield">
        <div class="shield-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
        </div>
        <h2>Desktop Only Access</h2>
        <p>Silahkan anda akses website ini melalui device laptop atau komputer untuk pengalaman terbaik.</p>
        <div style="margin-top: 2rem; font-size: 0.7rem; color: #cbd5e1; letter-spacing: 1px; font-weight: 700;">
            BALMON LAMPUNG PORTAL
        </div>
    </div>


    
    @php
        $logoRelativePath = 'images/logo-balmon-lampung.jpg';
        $hasLogo = file_exists(public_path($logoRelativePath));
    @endphp

    <!-- DESKTOP FLOATING SIDEBAR -->
    @if(!request()->routeIs('profile.complete'))
    <aside class="sidebar-desktop d-none d-lg-flex flex-column">
        <div class="brand-wrapper">
            @if ($hasLogo)
                <img src="{{ asset($logoRelativePath) }}" alt="Logo Balmon Lampung" class="balmon-logo">
            @endif
            <div class="brand-subtitle">KOMDIGI - BALMON Lampung</div>
            <div class="brand-title">Portal Monitoring</div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-heading">Navigasi Utama</div>
            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}" wire:navigate.hover
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <x-icon icon="dashboard" width="18" height="18" />
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('monitoring.index') }}" wire:navigate.hover
                    class="nav-link {{ request()->routeIs('monitoring.index') || request()->routeIs('monitoring.edit') ? 'active' : '' }}">
                    <x-icon icon="daftar_laporan" width="18" height="18" />
                    <span>Daftar Laporan</span>
                </a>

                <a href="{{ route('settings') }}" wire:navigate.hover
                    class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                    <x-icon icon="pengaturan" width="18" height="18" />
                    <span>Pengaturan</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" onsubmit="handleLogout(event)">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
            <div class="version-tag">
                Production Release v1.0
            </div>
        </div>
    </aside>
    @endif

    <div class="main-content" style="{{ request()->routeIs('profile.complete') ? 'margin-left: 0 !important;' : '' }}">
        <!-- TOP NAVBAR -->
        @if(!request()->routeIs('profile.complete'))
        <header class="app-topbar sticky-top">
            <div class="container-fluid d-flex align-items-center justify-content-between px-0">
                <!-- Left Section: Breadcrumbs -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn d-lg-none p-0 border-0" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#sidebarMobile">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </button>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 align-items-center">
                            <li class="breadcrumb-item">Balmon Lampung</li>
                            <li class="breadcrumb-divider">/</li>
                            <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                        </ol>
                    </nav>
                </div>

                <!-- Right Section: Clock & Profile -->
                <div class="d-flex align-items-center gap-4">
                    <div class="topbar-clock d-none d-md-flex">
                        <span id="topbarClock"></span>
                    </div>

                    @auth
                    <a href="{{ auth()->user()->role === 'super_admin' ? 'javascript:void(0)' : 'javascript:void(0)' }}" 
                       onclick="{{ auth()->user()->role === 'super_admin' ? '/* No modal for SA */' : 'showProfileModal()' }}" 
                       class="text-decoration-none d-flex align-items-center gap-3 ps-4 border-start border-slate-100 transition-all profile-topbar-link {{ auth()->user()->role === 'super_admin' ? 'cursor-default' : '' }}">
                        <div class="text-end d-none d-sm-block">
                            <div id="user-name-display" class="fw-bold text-slate-800" style="font-size: 0.85rem; line-height: 1.2;">{{ auth()->user()->name }}</div>
                            <div class="text-slate-400 fw-medium" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ auth()->user()->role === 'super_admin' ? 'Administrator' : 'Petugas Admin' }}
                            </div>
                        </div>
                        @if(auth()->user()->role !== 'super_admin')
                        <div class="position-relative">
                            <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6366f1&color=fff' }}" 
                                 alt="Profile" class="rounded-circle shadow-sm border border-2 border-white" 
                                 style="width: 38px; height: 38px; object-fit: cover;">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle" style="width: 10px; height: 10px;"></span>
                        </div>
                        @endif
                    </a>

                    <style>
                        .profile-topbar-link { opacity: 1; transition: transform 0.2s ease, opacity 0.2s ease; }
                        .profile-topbar-link:hover { transform: translateY(-1px); opacity: 0.85; }
                    </style>
                    @endauth
                </div>
            </div>
        </header>
        @endif

        <main class="content-body">
            @yield('content')
        </main>

    </div>

    @auth
    <!-- COMPACT PROFILE MODAL (GLASSMORPHISM) -->
    <div id="profile-modal" class="modal-premium-overlay">
        <div class="modal-premium-container">
            <div class="modal-premium-content card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <h5 class="fw-800 text-slate-900 mb-1">Update Profil</h5>
                        <p class="text-slate-500" style="font-size: 0.75rem;">Perbarui informasi publik Anda.</p>
                    </div>

                    <form id="form-modal-profile" onsubmit="handleProfileUpdate(event)">
                        @csrf
                        @if(auth()->user()->role !== 'super_admin')
                        <div class="mb-3 text-center">
                            <div class="position-relative d-inline-block">
                                <img src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6366f1&color=fff' }}" 
                                     id="modal-profile-preview" class="rounded-circle shadow-sm border border-4 border-white" 
                                     style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                     onclick="viewFullAvatar(this.src, '{{ addslashes(auth()->user()->name) }}')">
                                <button type="button" class="btn btn-primary rounded-circle position-absolute bottom-0 end-0 p-0 shadow-lg border-white border-2 d-flex align-items-center justify-content-center" 
                                        onclick="document.getElementById('modal-input-photo').click()" style="width: 32px; height: 32px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                </button>
                                <input type="file" name="profile_photo" id="modal-input-photo" class="d-none" accept="image/*" onchange="previewModalPhoto(this)">
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label text-slate-400 fw-bold small text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control bg-light border-0 rounded-3 py-2 px-3 small" value="{{ auth()->user()->name }}" required style="font-size: 0.85rem;">
                        </div>
                        
                        @if(auth()->user()->role !== 'super_admin')
                        <div class="mb-3">
                            <label class="form-label text-slate-400 fw-bold small text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 0.5px;">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0 rounded-3 py-2 px-3 small" value="{{ auth()->user()->email }}" required style="font-size: 0.85rem;">
                        </div>
                        @else
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        @endif
                        
                        <div class="bg-blue-50 p-3 rounded-4 border border-blue-100 mb-3">
                            <label class="form-label text-blue-900 fw-bold small mb-1" style="font-size: 0.75rem;">Konfirmasi Sandi</label>
                            <div class="position-relative">
                                <input type="password" name="current_password" class="form-control bg-white border-0 rounded-3 shadow-sm py-2 px-3 with-toggle" required placeholder="Sandi saat ini" style="font-size: 0.85rem;">
                                <button type="button" class="password-toggle" onclick="togglePassword(this)" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); border: none; background: transparent; padding: 0; outline: none; box-shadow: none;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btn-save-modal-profile" class="btn btn-primary rounded-3 fw-bold py-2 shadow-sm border-0" style="background: #2563eb; color: white;">
                                Simpan Perubahan
                            </button>
                            <button type="button" class="btn btn-link text-slate-400 text-decoration-none small py-1" onclick="hideProfileModal()" style="font-size: 0.75rem;">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function showProfileModal() {
            document.getElementById('profile-modal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function hideProfileModal() {
            document.getElementById('profile-modal').classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        function previewModalPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => document.getElementById('modal-profile-preview').src = e.target.result;
                reader.readAsDataURL(input.files[0]);
            }
        }
        async function handleProfileUpdate(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-modal-profile');
            const originalText = btn.textContent;
            btn.disabled = true; btn.textContent = 'Menyimpan...';
            
            try {
                const formData = new FormData(e.target);
                const response = await fetch('{{ route("security.update") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
                const data = await response.json();
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Profil berhasil diperbarui!',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#10b981'
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: data.message || 'Gagal memperbarui profil.',
                        background: '#ffffff',
                        color: '#0f172a',
                        iconColor: '#ef4444'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal terhubung ke server.',
                    background: '#ffffff',
                    color: '#0f172a',
                    iconColor: '#ef4444'
                });
            }
            finally { btn.disabled = false; btn.textContent = originalText; }
        }

        // Close on click outside
        window.onclick = function(event) {
            const modal = document.getElementById('profile-modal');
            if (event.target == modal) hideProfileModal();
        }
    </script>
    @endauth

    <footer class="app-footer">
            <div class="footer-copyright">
                &copy; {{ date('Y') }} Balai Monitor Spektrum Frekuensi Radio Kelas II Lampung. All rights reserved.
            </div>
            <div class="footer-credit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display: inline-block; margin-right: 0.4rem;"><rect x="7" y="15" width="10" height="7"/><path d="M12 15V9a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2v-5a2 2 0 0 0-2-2h-4"/></svg>
                Monitoring Dev Team bersama Team Magang Politeknik Negeri Lampung
            </div>
        </footer>
    </div>

    <!-- Single Permanent Backdrop -->
    <div id="mobileBackdrop"></div>

    <!-- Mobile Sidebar (Offcanvas) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMobile" aria-labelledby="sidebarMobileLabel" data-bs-backdrop="false">
        <div class="offcanvas-header">
            <div class="brand-wrapper p-0 border-0">
                <div class="brand-subtitle">KOMDIGI - BALMON Lampung</div>
                <div class="brand-title">Portal Monitoring</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body">
            <div class="p-2">
                <nav class="nav flex-column">
                    <a href="{{ route('dashboard') }}" wire:navigate.hover
                        class="nav-link sidebar-link-delayed {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <x-icon icon="dashboard" width="18" height="18" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('monitoring.index') }}" wire:navigate.hover
                        class="nav-link sidebar-link-delayed {{ request()->routeIs('monitoring.index') || request()->routeIs('monitoring.edit') ? 'active' : '' }}">
                        <x-icon icon="daftar_laporan" width="18" height="18" />
                        <span>Daftar Laporan</span>
                    </a>
                    <a href="{{ route('settings') }}" wire:navigate.hover
                        class="nav-link sidebar-link-delayed {{ request()->routeIs('settings') ? 'active' : '' }}">
                        <x-icon icon="pengaturan" width="18" height="18" />
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 10000);
        });

        // Re-run for Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 10000);
        });
    </script>
    <script>
        // =================================================================
        // GLOBAL RE-INITIALIZATION FUNCTION
        // Called after: DOMContentLoaded, AJAX pagination, livewire:navigated
        // =================================================================
        // =================================================================
        // GLOBAL CUSTOM CONFIRMATION DIALOG
        // =================================================================
        window.confirmSistem = function(title, message, onConfirm) {
            const overlay = document.getElementById('globalConfirmOverlay');
            const titleEl = document.getElementById('confirmTitle');
            const msgEl = document.getElementById('confirmMsg');
            const btnOk = document.getElementById('btnConfirmOk');
            const btnCancel = document.getElementById('btnConfirmCancel');

            titleEl.textContent = title;
            msgEl.textContent = message;

            const close = () => overlay.classList.remove('active');
            
            btnOk.onclick = () => { close(); onConfirm(); };
            btnCancel.onclick = () => { close(); };
            
            overlay.classList.add('active');
        };

        window.reInitializePageComponents = function () {
            // 1. Bootstrap Icons (no initialization needed - works with CSS classes)

            // 2. Pagination Sanitization (Prevent Native Jumping)
            document.querySelectorAll('.pagination a, .page-link').forEach(link => {
                if (link.href && link.href !== 'javascript:void(0)') {
                    link.dataset.ajaxUrl = link.href;
                    link.href = 'javascript:void(0)';
                }
            });

            // 3. Double-Submit Protection
            document.querySelectorAll('form').forEach(form => {
                if (form.dataset.submitListener) return;

                form.addEventListener('submit', function (e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.dataset.noDisable) {
                        setTimeout(() => {
                            if (!this.checkValidity()) return;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
                        }, 50);
                    }
                });
                form.dataset.submitListener = "true";
            });
        };

        // =================================================================
        // AJAX PAGINATION ENGINE (Re-initializable)
        // Uses document-level event delegation so it survives DOM swaps.
        // =================================================================
        window.initPagination = function () {
            // Re-sanitize pagination links on the fresh DOM
            window.reInitializePageComponents();

            // Guard: only attach the document-level click handler ONCE
            if (window._paginationClickBound) return;
            window._paginationClickBound = true;

            document.addEventListener('click', function (e) {
                const link = e.target.closest('.pagination a, .page-link');
                if (!link) return;

                // Re-query the container FRESH on every click
                const mainContainer = document.getElementById('tabel-frekuensi');
                if (!mainContainer) return;

                const url = link.dataset.ajaxUrl || link.href;
                if (!url || url === '#' || url.includes('javascript:')) return;
                if (!mainContainer.contains(link)) return;

                e.preventDefault();

                // Lock viewport position & height
                const savedScrollPos = window.scrollY;
                mainContainer.style.minHeight = mainContainer.offsetHeight + 'px';
                mainContainer.style.pointerEvents = 'none';

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        // Ambil elemen tabel lama (harus diambil ulang agar tidak referensi usang)
                        const currentContainer = document.getElementById('tabel-frekuensi');
                        
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const newTable = doc.getElementById('tabel-frekuensi');

                        if (newTable && currentContainer) {
                            // Ganti elemen lama secara utuh dengan elemen baru
                            currentContainer.replaceWith(newTable);
                            
                            // Keep URL clean - don't update browser address bar for pagination
                            // history.pushState(null, '', url);
                            window.scrollTo(0, savedScrollPos);
                            
                            // Re-init script (seperti Lucide icon, dll)
                            if (typeof window.reInitializePageComponents === 'function') {
                                window.reInitializePageComponents();
                            }
                        }
                    })
                    .catch(err => {
                        console.error('AJAX Pagination Error:', err);
                        const currentContainer = document.getElementById('tabel-frekuensi');
                        if (currentContainer) {
                            currentContainer.style.minHeight = '';
                            currentContainer.style.pointerEvents = 'auto';
                        }
                        window.location.href = url;
                    });
            });

            // Handle back/forward button (also only once)
            // With SPA (Livewire Navigate), use Livewire to navigate to the URL instead of full reload
            if (!window._popstateBound) {
                window._popstateBound = true;
                window.addEventListener('popstate', function (e) {
                    // Get the current URL from browser history
                    const url = window.location.pathname + window.location.search;
                    
                    // If Livewire is available, use it for SPA navigation
                    if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                        Livewire.navigate(url, {
                            replace: true,
                            skipBrowser: true // Prevent double history entry
                        }).catch(function () {
                            // Fallback to reload if Livewire navigate fails
                            window.location.reload();
                        });
                    } else {
                        // Fallback for non-Livewire pages
                        window.location.reload();
                    }
                });
            }
        };

        // =================================================================
        // CLOCK WIDGET (starts once, survives SPA navigation)
        // =================================================================
        window.initClock = function () {
            const clockEl = document.getElementById('topbarClock');
            if (clockEl && !clockEl.dataset.clockRunning) {
                const updateClock = () => {
                    const now = new Date();
                    const options = {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'short',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    };
                    clockEl.textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
                };
                updateClock();
                setInterval(updateClock, 1000);
                clockEl.dataset.clockRunning = 'true';
            }
        };

        // =================================================================
        // BOOTSTRAP — First Load
        // =================================================================
        document.addEventListener('DOMContentLoaded', function () {
            window.reInitializePageComponents();
            window.initPagination();
            window.initClock();
        });

        // =================================================================
        // LIVEWIRE SPA — Re-initialize after wire:navigate page swap
        // =================================================================
        document.addEventListener('livewire:navigated', function () {
            window.reInitializePageComponents();
            window.initPagination();
            window.initClock();

            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    bootstrap.Tooltip.getOrCreateInstance(el);
                });
            }
        });
    </script>
    @yield('scripts')

    <!-- Livewire Scripts (SPA Navigation Engine) -->
    @livewireScripts
    <script>
        // =====================================================
        // NATIVE SIDEBAR MANAGEMENT (Manual Single Backdrop)
        // =====================================================

        function toggleBackdrop(show) {
            const backdrop = document.getElementById('mobileBackdrop');
            if (!backdrop) return;
            if (show) {
                backdrop.classList.add('visible');
                document.body.style.overflow = 'hidden';
            } else {
                backdrop.classList.remove('visible');
                document.body.style.overflow = '';
            }
        }

        function cleanUpBackdrops() {
            // Remove any rogue Bootstrap-generated backdrops
            document.querySelectorAll('.offcanvas-backdrop, .modal-backdrop').forEach(el => el.remove());
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.body.classList.remove('modal-open', 'offcanvas-open');
        }

        // Initialize listeners
        const onShowSidebar = () => {
            toggleBackdrop(true);
            document.body.classList.add('sidebar-moving');
        };
        const onHiddenSidebar = () => {
            toggleBackdrop(false);
            cleanUpBackdrops();
            document.body.classList.remove('sidebar-moving');
        };

        // Initialize listeners
        function initSidebarLogic() {
            const el = document.getElementById('sidebarMobile');
            const backdrop = document.getElementById('mobileBackdrop');
            if (!el || !backdrop) return;

            // Remove old listeners to avoid duplicates (Crucial for Livewire SPA)
            el.removeEventListener('show.bs.offcanvas', onShowSidebar);
            el.removeEventListener('hidden.bs.offcanvas', onHiddenSidebar);

            // Add fresh listeners
            el.addEventListener('show.bs.offcanvas', onShowSidebar);
            el.addEventListener('hidden.bs.offcanvas', onHiddenSidebar);

            // SEQUENTIAL EXECUTION: Event-Driven Navigation Logic
            el.querySelectorAll('.sidebar-link-delayed').forEach(link => {
                link.onclick = function(e) {
                    e.preventDefault();
                    const url = this.href;
                    
                    const instance = bootstrap.Offcanvas.getInstance(el);
                    if (instance && el.classList.contains('show')) {
                        // TRUE SEQUENTIAL NAVIGATION: Wait for close + 100ms breath
                        const navigateCallback = () => {
                            setTimeout(() => {
                                if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                                    Livewire.navigate(url);
                                } else {
                                    window.location.href = url;
                                }
                            }, 100); // Navigation Breath
                        };
                        
                        el.addEventListener('hidden.bs.offcanvas', navigateCallback, { once: true });
                        instance.hide();
                    } else {
                        if (typeof Livewire !== 'undefined' && Livewire.navigate) {
                            Livewire.navigate(url);
                        } else {
                            window.location.href = url;
                        }
                    }
                };
            });

            // Backdrop Click Hint Logic
            backdrop.onclick = function() {
                const closeBtn = el.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.classList.remove('hint-red');
                    void closeBtn.offsetWidth; // Force reflow
                    closeBtn.classList.add('hint-red');
                    setTimeout(() => closeBtn.classList.remove('hint-red'), 600);
                }
            };
            
            el.dataset.sidebarInitialized = 'true';
        }

        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('input');
            const icon = btn.querySelector('.eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        document.addEventListener('livewire:navigating', function() {
            const el = document.getElementById('sidebarMobile');
            if (el) {
                const instance = bootstrap.Offcanvas.getInstance(el);
                if (instance) instance.hide();
            }
            toggleBackdrop(false);
            cleanUpBackdrops();
        });

        document.addEventListener('livewire:navigated', function() {
            cleanUpBackdrops();
            initSidebarLogic();

            // Trigger chart visibility if on dashboard
            if (window.initDashboardCharts) window.initDashboardCharts();
        });

        async function handleLogout(e) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = btn ? btn.innerHTML : '';
            
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>Keluar...</span>';
            }
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalBtnHtml;
                    }
                    Swal.fire({ icon: 'error', title: 'Gagal Keluar', text: 'Sesi gagal dihapus.' });
                }
            } catch (error) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
                Swal.fire({ icon: 'error', title: 'Kesalahan', text: 'Terjadi kesalahan jaringan.' });
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', initSidebarLogic);
        
        // Close sidebar if window resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                const el = document.getElementById('sidebarMobile');
                if (el && el.classList.contains('show')) {
                    const instance = bootstrap.Offcanvas.getInstance(el);
                    if (instance) instance.hide();
                }
            }
        });
    </script>
    <script>
        // Global Avatar Functions
        window.viewFullAvatar = function(src, name) {
            const overlay = document.getElementById('avatar-view-overlay');
            const img = document.getElementById('avatar-large-img');
            const nameEl = document.getElementById('avatar-wa-name');
            if (!overlay || !img) return;
            img.src = src;
            nameEl.innerText = name || 'Profile';
            overlay.classList.add('active');
            document.body.classList.add('modal-open');
        };
        window.closeFullAvatar = function() {
            const overlay = document.getElementById('avatar-view-overlay');
            if (!overlay) return;
            overlay.classList.remove('active');
            document.body.classList.remove('modal-open');
            // Paksa kursor kembali normal (Anti-Kursor Detektif)
            document.body.style.cursor = 'default';
            setTimeout(() => {
                document.body.style.cursor = '';
            }, 50);
        };
    </script>
    {{-- Script anti-jump dihapus karena akar masalah CSS (height 100%) sudah diperbaiki --}}
    <!-- Global Custom Confirm Modal -->
    <div id="globalConfirmOverlay" class="modal-premium-overlay">
        <div class="modal-premium-container">
            <div class="modal-premium-content">
                <div class="confirm-icon-circle">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div id="confirmTitle" class="confirm-title">Konfirmasi</div>
                <div id="confirmMsg" class="confirm-msg">Apakah Anda yakin ingin melanjutkan tindakan ini?</div>
                <div class="confirm-actions">
                    <button id="btnConfirmOk" class="btn-premium-ok">Ya, Lanjutkan</button>
                    <button id="btnConfirmCancel" class="btn-premium-cancel">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- WHATSAPP STYLE AVATAR VIEW -->
    <div id="avatar-view-overlay" class="avatar-view-overlay" onclick="closeFullAvatar()">
        <div class="avatar-wa-card" onclick="event.stopPropagation()">
            <div class="avatar-wa-header">
                <span id="avatar-wa-name">Profile</span>
            </div>
            <img id="avatar-large-img" src="" alt="Full Avatar" class="avatar-wa-img">
            <div class="avatar-wa-actions">
                <button class="wa-action-btn" title="Tutup" onclick="closeFullAvatar()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>

    @php
        $authUser = auth()->user();
        $isProfileIncomplete = $authUser && $authUser->role !== 'super_admin' && 
            (preg_match('/^Admin\s*\d*$/i', $authUser->name) || !$authUser->profile_photo);
    @endphp

    @if($isProfileIncomplete)
    <div class="complete-profile-overlay active">
        <div class="complete-profile-modal">
            <div class="complete-profile-header">
                <div class="shield-icon-wrap mb-3 mx-auto" style="width: 56px; height: 56px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h4 class="fw-bold" style="color: #0f172a; margin-bottom: 0.5rem;">Verifikasi Identitas</h4>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1.5rem;">Silakan lengkapi identitas Anda untuk dapat mengakses seluruh fitur sistem.</p>
            </div>
            
            <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data" id="complete-profile-form">
                @csrf
                <div class="mb-3 text-start">
                    <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">NAMA LENGKAP</label>
                    <input type="text" name="name" class="form-control premium-input" value="{{ old('name', preg_match('/^Admin\s*\d*$/i', $authUser->name) ? '' : $authUser->name) }}" placeholder="Masukkan nama asli Anda" required>
                    @error('name') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em;">FOTO PROFIL</label>
                    <div class="photo-upload-area" id="photo-upload-area" onclick="document.getElementById('profile_photo').click()">
                        <div id="photo-preview" class="d-none">
                            <img src="" alt="Preview" style="max-width: 100%; max-height: 140px; border-radius: 8px; object-fit: cover;">
                        </div>
                        <div id="photo-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            <p style="margin: 0; font-size: 0.85rem; font-weight: 600; color: #475569;">Pilih foto</p>
                            <p style="margin: 0; font-size: 0.7rem; color: #94a3b8;">Maksimal Kapasitas: 1MB</p>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/*" required onchange="previewProfilePhoto(this)">
                    </div>
                    @error('profile_photo') <span class="text-danger" style="font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3" style="border-radius: 0.75rem; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; box-shadow: 0 4px 12px rgba(37,99,235,0.2);">Simpan Perubahan</button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 fw-bold py-2 d-flex align-items-center justify-content-center" style="border-radius: 0.75rem; background: #fff1f2; border-color: #fecdd3; color: #e11d48; transition: all 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </div>
    <style>
        /* CSS to Blur Main Content */
        .sidebar-desktop, .sidebar-mobile, .topbar, .main-content {
            filter: blur(8px);
            pointer-events: none;
            user-select: none;
        }
        body { overflow: hidden; }
        
        .complete-profile-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .complete-profile-modal {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            text-align: center;
            animation: modalPopUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes modalPopUp {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .premium-input {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-weight: 500;
        }
        .premium-input:focus {
            background: #fff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .photo-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            background: #f8fafc;
            transition: all 0.2s;
        }
        .photo-upload-area:hover {
            border-color: #94a3b8;
            background: #f1f5f9;
        }
    </style>
    <script>
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').classList.remove('d-none');
                    document.getElementById('photo-placeholder').classList.add('d-none');
                    document.getElementById('photo-preview').querySelector('img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endif
</body>
</html>