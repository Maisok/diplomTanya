<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toothless - Сброс пароля</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #3a556a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Toothless</div>
            <h2>Сброс пароля</h2>
        </div>

        <div class="content">
            <p>Вы получили это письмо, потому что был запрошен сброс пароля для вашего аккаунта.</p>
            
            <a href="{{ url('reset-password/'.$token).'?email='.urlencode($email) }}" class="btn">
                Сбросить пароль
            </a>
            
            <div class="divider"></div>
            
            <p><small>Ссылка действительна в течение 60 минут.</small></p>
            <p><small>Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</small></p>
        </div>

        <div class="footer">
            © {{ date('Y') }} Toothless. Все права защищены.
        </div>
    </div>
</body>
</html>