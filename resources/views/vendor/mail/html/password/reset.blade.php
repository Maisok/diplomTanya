<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #374151;
            line-height: 1.5;
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
            height: 50px;
            margin-bottom: 20px;
        }
        .button {
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
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('img/logo.png') }}" alt="Toothless" class="logo">
            <h1 style="color: #3a556a; font-size: 24px; margin-bottom: 10px;">Сброс пароля</h1>
        </div>

        <p>Вы получили это письмо, потому что был запрошен сброс пароля для вашего аккаунта.</p>
        
        <p>Для сброса пароля нажмите кнопку ниже:</p>
        
        <a href="{{ url('reset-password/'.$token).'?email='.urlencode($email) }}" class="button">
            Сбросить пароль
        </a>
        
        <p>Ссылка действительна в течение 60 минут. Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</p>
        
        <div class="footer">
            <p>© {{ date('Y') }} Toothless. Все права защищены.</p>
        </div>
    </div>
</body>
</html>