<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Pemulihan Kata Sandi</title>
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
            margin: 32px 0;
        }
        .code-box {
            display: inline-block;
            background: #0f172a;
            border: 2px dashed #ef4444;
            border-radius: 12px;
            padding: 16px 32px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 8px;
            color: #ef4444;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.15);
        }
        .security-badge {
            display: inline-block;
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 14px;
        }
        .info-card {
            background: rgba(15, 23, 42, 0.6);
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px;
            margin-top: 28px;
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .footer {
            background: #090e17;
            padding: 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="logo-title">PT Manufactur Dynamic Indonesia</h1>
            <div class="subtitle">Sistem Pemulihan Akun & Kata Sandi B2B</div>
        </div>

        <div class="body-content">
            <div class="greeting">Halo, {{ $user->name }}</div>
            <div class="intro-text">
                Kami menerima permintaan pemulihan kata sandi untuk akun Anda di aplikasi Manufaktur. Gunakan kode verifikasi rahasia 10 digit di bawah ini untuk melanjutkan pembuatan kata sandi baru:
            </div>

            <div class="code-box-wrapper">
                <div class="code-box">{{ $resetCode }}</div>
                <div>
                    <span class="security-badge">
                        ⏱️ Berlaku selama {{ $expiresInMinutes }} Menit
                    </span>
                </div>
            </div>

            <div class="info-card">
                <strong>Pemberitahuan Keamanan:</strong><br>
                Demi keamanan akun manufaktur Anda, jangan pernah membagikan kode rahasia ini kepada pihak manapun termasuk pihak yang mengatasnamakan PT Manufactur Dynamic Indonesia. Jika Anda tidak merasa melakukan permintaan pemulihan ini, akun Anda tetap aman dan abaikan email ini.
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} PT Manufactur Dynamic Indonesia. Seluruh hak cipta dilindungi.<br>
            Jl. Raya Industri Manufaktur No. 88, Jawa Timur, Indonesia.
        </div>
    </div>
</body>
</html>
