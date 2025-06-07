<!DOCTYPE html>
<html>
<head>
    <title>Подтверждение записи</title>
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
        .details {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-item {
            margin-bottom: 12px;
            display: flex;
        }
        .detail-label {
            font-weight: 500;
            color: #64748b;
            min-width: 120px;
        }
        .detail-value {
            font-weight: 500;
            color: #1e293b;
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
            color: #1e40af;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <h2 class="header">Подтверждение записи</h2>
        
        <p>Благодарим вас за доверие! Ваша запись успешно оформлена.</p>
        
        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Услуга:</span>
                <span class="detail-value">{{ $service->name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Специалист:</span>
                <span class="detail-value">{{ $staff->name }} {{ $staff->surname }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Филиал:</span>
                <span class="detail-value">{{ $branch->address }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Дата и время:</span>
                <span class="detail-value highlight">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d.m.Y H:i') }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Стоимость:</span>
                <span class="detail-value">{{ $service->price }} руб.</span>
            </div>
        </div>
        
        <p>Пожалуйста, приходите за 10 минут до назначенного времени.</p>
        
        <div class="footer">
            <p>С уважением,<br>Команда клиники</p>
            <p>Если у вас возникли вопросы, свяжитесь с нами по телефону: <strong>887-812</strong></p>
        </div>
    </div>
</body>
</html>