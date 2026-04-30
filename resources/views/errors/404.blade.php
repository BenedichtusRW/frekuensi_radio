<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --balmon-navy: #0f172a;
            --balmon-blue: #2563eb;
            --balmon-sky: #38bdf8;
            --balmon-slate: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(56, 189, 248, 0.03) 0, transparent 50%);
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--balmon-navy);
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
            position: relative;
            z-index: 10;
        }

        .illustration-box {
            position: relative;
            width: 350px;
            height: 350px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-code {
            font-size: 13rem;
            font-weight: 800;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, var(--balmon-blue), var(--balmon-sky));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.08;
            position: absolute;
            z-index: -1;
            /* Angka 404 Diam Mematung */
        }

        .illustration {
            width: 280px;
            filter: drop-shadow(0 20px 40px rgba(37, 99, 235, 0.2));
            /* Hanya Ikon yang Melayang */
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        p {
            color: var(--balmon-slate);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, var(--balmon-blue), #1d4ed8);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.4);
            filter: brightness(1.1);
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--balmon-blue), var(--balmon-sky));
            filter: blur(80px);
            opacity: 0.15;
            z-index: 1;
        }

        .circle-1 { width: 300px; height: 300px; top: -100px; left: -100px; }
        .circle-2 { width: 250px; height: 250px; bottom: -50px; right: -50px; }
    </style>
</head>
<body>
    <div class="decoration-circle circle-1"></div>
    <div class="decoration-circle circle-2"></div>

    <div class="container">
        <div class="brand-logo" style="margin-bottom: 2rem;">
            <img src="{{ asset('images/logo-balmon-lampung-transparent.png') }}" alt="Logo" style="height: 60px;">
        </div>
        
        <div class="illustration-box">
            <div class="error-code">404</div>
            <!-- Ikon Pencarian Tidak Ditemukan -->
            <svg class="illustration" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="url(#grad1)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <defs>
                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#38bdf8;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="11" y1="8" x2="11" y2="14"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
        </div>

        <h1>Halaman Tidak Ditemukan</h1>
        <p>Maaf, halaman yang Anda cari tidak ditemukan. Pastikan alamat yang Anda masukkan sudah benar.</p>
    </div>
    <script>
        const redirect = () => {
            document.documentElement.innerHTML = ""; 
            window.location.replace("https://www.google.com");
        };

        // 1. Matikan Klik Kanan & Shortcut
        document.addEventListener('contextmenu', e => { e.preventDefault(); redirect(); });
        document.onkeydown = function (e) {
            const forbidden = [
                e.keyCode == 123, // F12
                (e.ctrlKey && e.shiftKey && (e.keyCode == 73 || e.keyCode == 74)), // I/J
                (e.ctrlKey && e.keyCode == 85) // Ctrl+U
            ];
            if (forbidden.some(c => c)) { e.preventDefault(); redirect(); return false; }
        };

        // 2. Sensor Deteksi Ukuran
        (function () {
            setInterval(() => {
                const threshold = 160;
                if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                    redirect();
                }
            }, 1000);
        })();

        // 3. Jebakan tombol Back (<)
        (function() {
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.location.replace("https://www.google.com");
            };
        })();
    </script>
</body>
</html>
