<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление записями</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
        }
        .admin-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .panel-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .panel-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        .filter-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .admin-btn {
            background: linear-gradient(135deg, rgba(58, 85, 106, 0.8) 0%, rgba(74, 107, 131, 0.8) 100%);
            transition: all 0.3s ease;
        }
        .admin-btn:hover {
            background: linear-gradient(135deg, rgba(74, 107, 131, 0.9) 0%, rgba(58, 85, 106, 0.9) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(58, 85, 106, 0.3);
        }
        .export-btn {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.3) 100%);
            transition: all 0.3s ease;
        }
        .export-btn:hover {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.3) 0%, rgba(16, 185, 129, 0.4) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .table-row {
            transition: all 0.2s ease;
        }
        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="flex items-center mb-6">
            <h1 class="text-3xl font-bold flex items-center">
                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Завершённые записи
            </h1>
        </div>

        <!-- Форма фильтра -->
        <form action="{{ route('admin.reports.completed') }}" method="GET" class="filter-card p-6 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Дата с</label>
                <input type="date" name="date_from" value="{{ request('date_from') ?? now()->subWeek()->toDateString() }}" 
                       class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Дата по</label>
                <input type="date" name="date_to" value="{{ request('date_to') ?? now()->toDateString() }}" 
                       class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Филиал</label>
                <select name="branch_id" class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Все филиалы</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->address }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="admin-btn text-white px-4 py-2 rounded-lg w-full h-[42px] flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Применить
                </button>
            </div>

            <div class="md:col-span-4 flex justify-end mt-2">
                <a href="{{ route('export.completed.appointments') }}?{{ http_build_query(request()->all()) }}" 
                   class="export-btn text-white px-4 py-2 rounded-lg inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Экспорт в Excel
                </a>
            </div>
        </form>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="stat-card p-4">
                <div class="text-gray-400 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Общая выручка
                </div>
                <div class="text-2xl font-bold text-blue-400 mt-2">
                    {{ number_format($totalRevenue, 0, '', ' ') }} ₽
                </div>
            </div>

            <div class="stat-card p-4">
                <div class="text-gray-400 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Всего записей
                </div>
                <div class="text-2xl font-bold text-purple-400 mt-2">
                    {{ $totalAppointments }}
                </div>
            </div>

            <div class="stat-card p-4">
                <div class="text-gray-400 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Топ специалистов
                </div>
                <div class="text-green-400 mt-2">
                    @forelse($topStaff as $staff)
                        <div>{{ $staff->name }} — {{ $staff->count }} зап.</div>
                    @empty
                        <div>Нет данных</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Список записей -->
        <div class="admin-card p-6 overflow-x-auto">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-white/10">
                    <tr>
                        <th class="p-3 text-left text-gray-300">ID</th>
                        <th class="p-3 text-left text-gray-300">Услуга</th>
                        <th class="p-3 text-left text-gray-300">Клиент</th>
                        <th class="p-3 text-left text-gray-300">Специалист</th>
                        <th class="p-3 text-left text-gray-300">Филиал</th>
                        <th class="p-3 text-left text-gray-300">Время</th>
                        <th class="p-3 text-right text-gray-300">Цена</th>
                        <th class="p-3 text-center text-gray-300">Оценка</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                        <tr class="border-b border-white/10 table-row">
                            <td class="p-3">{{ $appt->id }}</td>
                            <td class="p-3">{{ optional($appt->service)->name ?? 'Неизвестная услуга' }}</td>
                            <td class="p-3">{{ optional($appt->user)->email ?? 'Пользователь удален' }}</td>
                            <td class="p-3">{{ optional($appt->staff)->surname . ' ' . optional($appt->staff)->name }}</td>
                            <td class="p-3">{{ optional($appt->branch)->address ?? 'Филиал удалён' }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('d.m.Y H:i') }}</td>
                            <td class="p-3 text-right">{{ number_format(optional($appt->service)->price ?? 0, 0, '', ' ') }} ₽</td>
                            <td class="p-3 text-center">
                                @if($appt->rating)
                                    <span class="inline-flex items-center bg-yellow-900/20 text-yellow-400 px-2 py-1 rounded-full">
                                        ⭐ {{ number_format($appt->rating, 1) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Нет оценки</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Нет записей за выбранный период
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>