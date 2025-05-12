<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель управления персонала</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
</head>
<body class="bg-[#5A7684] flex flex-col min-h-screen w-full">
<x-header class="flex-shrink-0"/>
    <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-5xl p-6 bg-white rounded-lg shadow-lg">
            <h1 class="text-center text-2xl font-bold mb-6">Панель управления</h1>
            <p>Добро пожаловать, {{ $staff->first_name }} {{ $staff->last_name }}!</p>

            <h2 class="text-xl mt-6 mb-4">Ваши записи:</h2>
            @if($appointments->isEmpty())
                <p>У вас нет записей.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Услуга</th>
                                <th class="py-2 px-4 border-b">Дата и время</th>
                                <th class="py-2 px-4 border-b">Клиент</th>
                                <th class="py-2 px-4 border-b">Статус</th>
                                <th class="py-2 px-4 border-b">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $appointment->service->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $appointment->appointment_time }}</td>
                                    <td class="py-2 px-4 border-b">{{ $appointment->user->name }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            @if($appointment->status == 'active') bg-blue-100 text-blue-800
                                            @elseif($appointment->status == 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $appointment->status }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        @if($appointment->status == 'active')
                                            <form action="{{ route('staff.appointment.complete', $appointment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 mr-2">
                                                    Завершить
                                                </button>
                                            </form>
                                            <form action="{{ route('staff.appointment.cancel', $appointment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                                    Отменить
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Кнопка выхода -->
            <form action="{{ route('staff.logout') }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="w-full bg-red-500 text-white font-bold py-2 rounded hover:bg-red-600">
                    Выйти
                </button>
            </form>
        </div>
    </div>
</body>
</html>