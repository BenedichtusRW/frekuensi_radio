<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Balmon Lampung - Monitoring Frekuensi')</title>

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --balmon-navy: #0f172a;
            --balmon-blue: #2563eb;
            --balmon-sky: #38bdf8;
            --balmon-emerald: #10b981;
            --balmon-slate: #64748b;
            --balmon-ink: #0b2239;
            --balmon-background: #f1f5f9;
            /* Slate-100 for better depth contrast against white elements */
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--balmon-background);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Floating Sidebar Transformation */
        .sidebar-desktop {
            width: 240px;
            position: fixed;
            left: 2rem;
            top: 2rem;
            bottom: 2rem;
            height: calc(100vh - 4rem);
            background: #ffffff !important;
            color: #0f172a;
            z-index: 1050;
            border-radius: 2rem;
            /* Capsule Style */
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 10px 50px -12px rgba(0, 0, 0, 0.12);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .brand-wrapper {
            padding: 1.5rem 1.25rem;
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
            color: #64748b; /* Slate-500 */
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
            padding: 1.25rem 1rem;
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
            transition: all 0.2s ease;
            margin-bottom: 0.4rem;
            position: relative;
            text-decoration: none;
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

        /* Top Navbar Styling (Industrial Integrated) */
        .app-topbar {
            background: #fff;
            height: 64px;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            margin: 0;
            border-radius: 0 0 2rem 2rem;
            border: 1px solid #f1f5f9;
            border-top: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            z-index: 50;
            /* Above all content */
            position: sticky;
            top: 0;
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
            /* Reduced from 0.78rem */
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .main-content {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content-body {
            flex: 1;
            padding: 1.5rem 2rem 2rem 2rem;
        }

        .app-footer {
            padding: 0.75rem 2rem;
            background: #ffffff;
            color: #94a3b8; /* Slate-400 */
            font-size: 0.68rem;
            text-align: center;
            border-top: none;
            border-radius: 2rem 2rem 0 0;
            box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.02);
            margin-top: auto;
            position: relative;
            z-index: 10;
        }

        .footer-credit {
            color: #cbd5e1; /* Slate-300 */
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            margin-top: 0.15rem;
            filter: grayscale(1);
            opacity: 0.8;
            font-size: 0.65rem;
        }

        @media (min-width: 992px) {
            .main-content {
                margin-left: 288px;
                /* 240px width + 2rem left + approx 1rem gap */
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
            color: #cbd5e1; /* Slate-300 */
            font-weight: 500;
            text-align: center;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body>
    @php
        $logoRelativePath = 'images/logo-balmon-lampung.jpg';
        $hasLogo = file_exists(public_path($logoRelativePath));
    @endphp

    <!-- DESKTOP FLOATING SIDEBAR -->
    <aside class="sidebar-desktop d-none d-lg-flex flex-column">
        <div class="brand-wrapper">
            @if ($hasLogo)
                <img src="{{ asset($logoRelativePath) }}" alt="Logo Balmon Lampung" class="balmon-logo">
            @endif
            <div class="brand-subtitle">KOMDIGI - BALMON Lampung</div>
            <div class="brand-title">Frekuensi</div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-heading">Navigasi Utama</div>
            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard" size="18"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('monitoring.index') }}"
                    class="nav-link {{ request()->routeIs('monitoring.index') || request()->routeIs('monitoring.edit') ? 'active' : '' }}">
                    <i data-lucide="file-text" size="18"></i>
                    <span>Daftar Laporan</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="version-tag">
                Production Release v1.0
            </div>
        </div>
    </aside>

    <div class="main-content">
        <!-- TOP NAVBAR -->
        <header class="app-topbar">
            <div class="container-fluid d-flex align-items-center justify-content-between px-0">
                <!-- Left Section: Breadcrumbs -->
                <div class="d-flex align-items-center gap-3">
                    <button class="btn d-lg-none p-0 border-0" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileSidebar">
                        <i data-lucide="menu"></i>
                    </button>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 align-items-center">
                            <li class="breadcrumb-item">Balmon Lampung</li>
                            <li class="breadcrumb-divider">/</li>
                            <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                        </ol>
                    </nav>
                </div>

                <!-- Right Section: Clock -->
                <div class="d-flex align-items-center">
                    <div class="topbar-clock d-none d-md-flex">
                        <i data-lucide="clock" size="14"></i>
                        <span id="topbarClock"></span>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>

        <footer class="app-footer">
            <div class="footer-copyright">
                &copy; {{ date('Y') }} Balai Monitor Spektrum Frekuensi Radio Kelas II Lampung. All rights reserved.
            </div>
            <div class="footer-credit">
                <i data-lucide="cpu" size="12"></i>
                Monitoring Dev Team bersama Team Magang Politeknik Negeri Lampung
            </div>
        </footer>
    </div>

    <!-- Mobile Sidebar -->
    <div class="offcanvas offcanvas-start bg-white" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom border-slate-50 bg-white">
            <div class="brand-title text-slate-900">Menu Utama</div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0 bg-white">
            <div class="p-4">
                <nav class="nav flex-column">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i data-lucide="layout-dashboard" size="18"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('monitoring.index') }}"
                        class="nav-link {{ request()->routeIs('monitoring.index') || request()->routeIs('monitoring.edit') ? 'active' : '' }}">
                        <i data-lucide="file-text" size="18"></i>
                        <span>Daftar Laporan</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Lucide Icons
            lucide.createIcons();

            // Clock Widget
            const clockEl = document.getElementById('topbarClock');
            if (clockEl) {
                const updateClock = () => {
                    const now = new Date();
                    const options = { weekday: 'long', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' };
                    clockEl.textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
                };
                updateClock();
                setInterval(updateClock, 60000); // Update every minute for performance
            }

            // Global Page Component Re-Initialization
            window.reInitializePageComponents = function () {
                // 1. Lucide Icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                // 2. Pagination Sanitization (Prevent Native Jumping)
                document.querySelectorAll('.pagination a, .page-link').forEach(link => {
                    if (link.href && link.href !== 'javascript:void(0)') {
                        link.dataset.ajaxUrl = link.href;
                        link.href = 'javascript:void(0)';
                    }
                });

                // 3. Double-Submit Protection
                document.querySelectorAll('form').forEach(form => {
                    // Avoid duplicate listeners
                    if (form.dataset.submitListener) return;

                    form.addEventListener('submit', function (e) {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn && !submitBtn.dataset.noDisable) {
                            // Delay slightly to allow native validation
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

            // Initial run
            reInitializePageComponents();

            // --- SEAMLESS AJAX PAGINATION ENGINE ---
            const mainContainer = document.getElementById('tabel-frekuensi');
            if (mainContainer) {
                document.addEventListener('click', function (e) {
                    const link = e.target.closest('.pagination a, .page-link');
                    if (!link) return;

                    // URL logic supporting sanitized links
                    const url = link.dataset.ajaxUrl || link.href;
                    if (!url || url === '#' || url.includes('javascript:')) return;

                    // Only intercept if we are on a page that supports AJAX table reload
                    if (!mainContainer.contains(link)) return;

                    e.preventDefault();

                    // --- ULTIMATE VIEWPORT LOCK (Frozen Mode) ---
                    const savedScrollPos = window.scrollY;
                    const currentHeight = mainContainer.offsetHeight;

                    // Lock height strictly to prevent collapse
                    mainContainer.style.minHeight = currentHeight + 'px';

                    // Add loading visual
                    mainContainer.style.opacity = '0.5';
                    mainContainer.style.pointerEvents = 'none';

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTable = doc.getElementById('tabel-frekuensi');

                            if (newTable) {
                                mainContainer.innerHTML = newTable.innerHTML;

                                // Sync URL
                                history.pushState(null, '', url);

                                // DOUBLE FORCE SCROLL (Instant + Delay)
                                window.scrollTo(0, savedScrollPos);
                                setTimeout(() => {
                                    window.scrollTo(0, savedScrollPos);
                                }, 10);

                                // Re-init interactions
                                window.reInitializePageComponents();
                            }

                            // Release height lock and visuals
                            mainContainer.style.minHeight = '';
                            mainContainer.style.opacity = '1';
                            mainContainer.style.pointerEvents = 'auto';
                        })
                        .catch(err => {
                            console.error('AJAX Pagination Error:', err);
                            mainContainer.style.minHeight = '';
                            window.location.href = url; // Fallback to normal load
                        });
                });

                // Handle back/forward button
                window.addEventListener('popstate', function () {
                    window.location.reload();
                });
            }
        });
    </script>
    @yield('scripts')
</body>

</html>