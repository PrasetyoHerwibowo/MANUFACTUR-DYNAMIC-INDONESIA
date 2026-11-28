<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Aktivasi Akun</title>
    <style>
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 40px 16px;
        }
        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #1e293b;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }
        .header {
            background: linear-gradient(135deg, #090e17 0%, #1e293b 100%);
            padding: 32px 28px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .logo-title {
            color: #ef4444;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 6px;
            font-weight: 500;
        }
        .body-content {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 12px;
        }
        .intro-text {
            color: #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .code-box-wrapper {
            text-align: center;
            margin: 28px 0;
        }
        .code-box {
            display: inline-block;
            background: #0f172a;
            border: 2px dashed #ef4444;
            border-radius: 12px;
            padding: 18px 32px;
            letter-spacing: 8px;
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            font-family: 'Courier New', Courier, monospace;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.4);
        }
        .code-hint {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 10px;
        }
        .alert-box {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 12.5px;
            color: #fca5a5;
            line-height: 1.5;
            margin-top: 24px;
        }
        .footer {
            background: #0b1120;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo-title">PT Manufactur Dynamic Indonesia</h1>
            <div class="subtitle">Platform Manufaktur Mesin Pengolahan Kopi & Kakao</div>
        </div>
        <div class="body-content">
            <div class="greeting">Halo, {{ $user->name }}!</div>
            <p class="intro-text">
                Terima kasih telah mendaftar di aplikasi mobile <strong>PT Manufactur Dynamic Indonesia</strong>. Untuk menjaga keamanan akun dan menjamin validitas email Anda, silakan masukkan <strong>10 digit kode verifikasi</strong> acak berikut pada aplikasi:
            </p>

            <div class="code-box-wrapper">
                <div class="code-box">{{ $activationCode }}</div>
                <div class="code-hint">Kode acak 10 digit (angka & huruf). Berlaku selama 24 jam.</div>
            </div>

            <div class="alert-box">
                <strong>Peringatan Keamanan:</strong> Jangan berikan kode ini kepada siapapun, termasuk pihak yang mengatasnamakan PT Manufactur Dynamic Indonesia. Akun Anda belum dapat digunakan sebelum kode ini dimasukkan.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PT Manufactur Dynamic Indonesia. Hak cipta dilindungi undang-undang.<br>
            Surabaya / Jawa Timur &bull; Customer Care: +62 812-3456-7890
        </div>
    </div>
</body>
</html>
