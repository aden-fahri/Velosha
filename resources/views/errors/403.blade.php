<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak | Velosha</title>
    <link rel="stylesheet" href="/fonts/bricolage/bricolage.css">
    <link rel="stylesheet" href="/fonts/inter/inter.css">
    <style>
        :root {
            --paper: #FBF4E6;
            --paper2: #F4ECE1;
            --ink: #10282E;
            --card: #FFFFFF;
            --orange: #DE7119;
            --orange-l: #F6B777;
            --mustard: #F4BE3F;
            --red: #C23B22;
            --red-l: #F8D7D0;
            --mute: #5A7175;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: var(--paper);
            color: var(--ink);
            font-family: 'Inter', system-ui, sans-serif;
            position: relative;
        }
        .dots {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--ink) 1.2px, transparent 1.2px);
            background-size: 18px 18px;
            opacity: 0.12;
            pointer-events: none;
        }
        .card {
            width: 100%;
            max-width: 480px;
            background: var(--card);
            border: 3px solid var(--ink);
            border-radius: 18px;
            padding: 36px 32px;
            box-shadow: 8px 8px 0 var(--ink);
            position: relative;
            z-index: 10;
            text-align: center;
        }
        .logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            text-decoration: none;
            color: var(--ink);
        }
        .logo-img { height: 32px; width: auto; }
        .logo-txt { font-family: 'Bricolage Grotesque', sans-serif; font-size: 22px; font-weight: 800; }
        .badge {
            display: inline-block;
            background: var(--red-l);
            color: var(--red);
            border: 2px solid var(--ink);
            box-shadow: 2.5px 2.5px 0 var(--ink);
            border-radius: 8px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 16px;
        }
        h1 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 32px;
            font-weight: 900;
            line-height: 1.15;
            color: var(--ink);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        .lead {
            font-size: 14px;
            color: var(--mute);
            line-height: 1.6;
            margin-bottom: 28px;
            font-weight: 500;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            font-size: 13px;
            font-weight: 800;
            border-radius: 12px;
            border: 2.5px solid var(--ink);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-primary {
            background: var(--orange);
            color: #ffffff;
            box-shadow: 4px 4px 0 var(--ink);
        }
        .btn-primary:hover {
            transform: translate(1px, 1px);
            box-shadow: 3px 3px 0 var(--ink);
            background: var(--orange-l);
            color: var(--ink);
        }
        .btn-secondary {
            background: var(--paper);
            color: var(--ink);
            box-shadow: 2px 2px 0 var(--ink);
        }
        .btn-secondary:hover {
            background: #ffffff;
            transform: translate(1px, 1px);
        }
    </style>
</head>
<body>
    <div class="dots"></div>
    <div class="card">
        <a href="/" class="logo-wrap">
            <img src="/images/logo-velosha.png" alt="Velosha" class="logo-img">
            <span class="logo-txt">Velosha</span>
        </a>

        <div>
            <span class="badge">403 &mdash; Akses Dibatasi</span>
            <h1>Jalur Ini Dikhususkan</h1>
            <p class="lead">
                {{ $exception->getMessage() ?: 'Halaman ini memerlukan hak akses khusus (Peserta, Penyelenggara, atau Operator). Silakan masuk dengan akun yang sesuai.' }}
            </p>
        </div>

        <div class="actions">
            <a href="/" class="btn btn-primary">
                &larr; Kembali ke Beranda
            </a>
            @auth
                <form method="POST" action="/logout">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-secondary" style="width: 100%;">
                        Ganti / Keluar Akun
                    </button>
                </form>
            @else
                <a href="/login" class="btn btn-secondary">
                    Masuk ke Akun Lain
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
