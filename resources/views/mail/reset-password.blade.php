<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        .info {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin: 16px 0;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            background-color: #4338ca;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
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
            <h1>Reset Password</h1>
        </div>

        <p class="info" style="text-align:center">
            Klik tombol di bawah untuk mereset password akun Anda.
        </p>

        <div class="btn-wrapper">
            <a class="btn" href="{{ $url }}">Reset Password</a>
        </div>

        <p class="info">
            Link ini berlaku selama <strong>60 menit</strong>. Jika Anda tidak meminta reset password, abaikan email ini.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} Platform Beasiswa. All rights reserved.
        </div>
    </div>
</body>
</html>
