<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Jalur Khusus | Velosha</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #2e343b;
            color: #0a353e;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #b8bfbe;
            border-radius: 1px;
            padding: 32px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
        }

        .code {
            display: inline-block;
            background: #0a353e;
            color: #e58f42;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.18em;
            padding: 6px 10px;
            border-radius: 1px;
        }

        h1 {
            margin-top: 18px;
            font-size: 24px;
            font-weight: 800;
        }

        .lead {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.6;
            color: #556066;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 700;
            border: 1px solid transparent;
            border-radius: 1px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #b2590f;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #a0520c;
        }

        .btn-ghost {
            background: transparent;
            color: #0a353e;
            border-color: #b8bfbe;
        }

        .btn-ghost:hover {
            background: #f1f4f4;
        }

        .btn-danger {
            margin-top: 10px;
            background: transparent;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .btn-danger:hover {
            background: #fef2f2;
        }

        .hint {
            margin-top: 18px;
            font-size: 12px;
            color: #5f6a70;
        }
    </style>
</head>
<body>
    <main class="card">
        <span class="code">403</span>
        <h1>Akses Jalur Khusus</h1>
        <p class="lead">
            Halaman ini tidak dapat dibuka oleh akun Anda. Kemungkinan halaman tersebut
            khusus peran lain, atau resource bukan milik Anda.
        </p>

        <div class="actions">
            <a class="btn btn-ghost" href="#"
               onclick="history.length > 1 ? history.back() : (window.location.href = '/'); return false;">Kembali</a>

            @auth
                @if (auth()->user()->canVisit('/dashboard'))
                    <a class="btn btn-primary" href="/dashboard">Ke Dashboard Saya</a>
                @elseif (auth()->user()->canVisit('/admin'))
                    <a class="btn btn-primary" href="/admin">Ke Panel Admin</a>
                @else
                    <a class="btn btn-primary" href="/">Ke Beranda</a>
                @endif
            @else
                <a class="btn btn-primary" href="/login">Masuk</a>
            @endauth
        </div>

        @auth
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="btn btn-danger">Keluar &amp; Ganti Akun</button>
            </form>
            <div class="hint">Butuh bantuan? Hubungi penyelenggara lomba.</div>
        @endauth
    </main>
</body>
</html>
