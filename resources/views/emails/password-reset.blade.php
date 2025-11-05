<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .content {
            padding: 30px 20px;
        }

        .content p {
            margin-bottom: 15px;
            color: #555;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }

        .reset-button:hover {
            transform: scale(1.05);
        }

        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-box p {
            margin: 5px 0;
            color: #856404;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }

        .footer p {
            margin: 5px 0;
        }

        .alternative-link {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            word-break: break-all;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Reset Password</h1>
        </div>

        <div class="content">
            <p>Halo,</p>

            <p>Kami menerima permintaan untuk mereset password akun Anda di <strong>{{ config('app.name') }}</strong>.
            </p>

            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">Reset Password</a>
            </div>

            <div class="info-box">
                <p><strong>⏱️ Link ini akan kadaluarsa dalam
                        {{ config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') }} menit.</strong></p>
                <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            </div>

            <p>Jika Anda mengalami kesulitan mengklik tombol "Reset Password", salin dan tempel URL berikut ke browser
                Anda:</p>

            <div class="alternative-link">
                <a href="{{ $url }}" style="color: #667eea; text-decoration: none;">{{ $url }}</a>
            </div>

            <p style="margin-top: 30px;">Terima kasih,<br>Tim {{ config('app.name') }}</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon jangan membalas.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
