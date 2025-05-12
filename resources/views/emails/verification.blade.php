<!DOCTYPE html>
<html>
<head>
    <title>Код подтверждения email</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        .container {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            color: #1e40af;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }
        .code-container {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .verification-code {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #1e40af;
            padding: 10px 20px;
            background-color: #e0e7ff;
            border-radius: 6px;
            display: inline-block;
        }
        .instructions {
            margin: 20px 0;
            line-height: 1.7;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #64748b;
            font-size: 14px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            height: 50px;
        }
        .highlight {
            font-weight: 600;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <h2 class="header">Подтверждение email</h2>
        
        <p>Для завершения регистрации введите следующий код подтверждения:</p>
        
        <div class="code-container">
            <div class="verification-code">{{ $code }}</div>
        </div>
        
        <div class="instructions">
            <p>1. Перейдите в <span class="highlight">личный кабинет</span></p>
            <p>2. Введите указанный выше код в поле подтверждения</p>
            <p>3. Нажмите кнопку "Подтвердить"</p>
        </div>
        
        <p>Если вы не запрашивали это подтверждение, пожалуйста, проигнорируйте это письмо.</p>
        
        <div class="footer">
            <p>С уважением,<br>Команда клиники</p>
            <p>Это письмо было отправлено автоматически, пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>