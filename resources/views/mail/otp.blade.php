<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi Email</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 40px 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }
        .code-box {
            text-align: center;
            background: #f8f9ff;
            border: 1px dashed #c7d2fe;
            border-radius: 8px;
            padding: 24px;
            margin: 24px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #4338ca;
            font-family: 'SF Mono', 'Courier New', monospace;
        }
        .info {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin: 16px 0;
        }
        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email</h1>
        </div>

        <p class="info" style="text-align:center">
            Gunakan kode berikut untuk memverifikasi alamat email Anda.
        </p>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
        </div>

        <p class="info">
            Kode ini berlaku selama <strong>5 menit</strong>. Jika Anda tidak meminta kode ini, abaikan email ini.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Platform Beasiswa. All rights reserved.
        </div>
    </div>
</body>
</html>
