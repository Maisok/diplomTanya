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
        .section-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .status-active {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
        }
        .status-completed {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }
        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        .table-row {
            transition: all 0.2s ease;
        }
        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .action-btn {
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    <div class="flex-grow p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-100 mb-2">
                    Управление всеми записями
                </h1>
                <p class="text-gray-300 max-w-2xl mx-auto">
                    Просмотр, фильтрация и управление всеми записями пациентов
                </p>
            </div>
            
            <!-- Фильтры -->
            <div class="section-card p-6 mb-8">
                <h2 class="text-xl md:text-2xl font-semibold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Фильтры
                </h2>
                <form method="GET" action="{{ route('admin.all-appointments') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Фильтр по персоналу -->
                    @if(auth()->user()->role === 'admin')
                    <div>
                        <label for="staff_id" class="block text-gray-300 mb-2">Специалист</label>
                        <select name="staff_id" id="staff_id" class="w-full p-3 rounded-lg bg-[#3A556A] border border-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white">
                            <option value="">Все специалисты</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->id }}" {{ request('staff_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} {{ $employee->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

            
                    
                    <!-- Фильтр по услугам -->
                    <div>
                        <label for="service_id" class="block text-gray-300 mb-2">Услуга</label>
                        <select name="service_id" id="service_id" class="w-full p-3 rounded-lg bg-[#3A556A] border border-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white">
                            <option value="">Все услуги</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Фильтр по статусу -->
                    <div>
                        <label for="status" class="block text-gray-300 mb-2">Статус</label>
                        <select name="status" id="status" class="w-full p-3 rounded-lg bg-[#3A556A] border border-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white">
                            <option value="">Все статусы</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершенные</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отмененные</option>
                        </select>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex items-end space-x-3 md:flex-col md:space-x-0 md:space-y-3 lg:flex-row lg:space-y-0 lg:space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-lg transition-all duration-300 shadow-md flex-1 flex items-center justify-center text-sm md:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Применить
                        </button>
                        <a href="{{ route('admin.all-appointments') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-4 py-3 rounded-lg transition-all duration-300 shadow-md flex-1 flex items-center justify-center text-sm md:text-base">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Сбросить
                        </a>
                    
                    </div>
                    @if(auth()->user()->role === 'staff')

                    <a href='{{route('staff.exports.clients.index')}}' class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-3 rounded-lg transition-all duration-300 shadow-md flex-1 flex items-center justify-center text-sm md:text-base">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Экспорт историй клиентов
                    </a>

                @endif
                </form>
            </div>
            
            <!-- Таблица записей -->
            <div class="section-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Клиент</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Услуга</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Специалист</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Дата и время</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($appointments as $appointment)
                                <tr class="table-row">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $appointment->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $appointment->user->name }} {{ $appointment->user->surname }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $appointment->service->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $appointment->staff->surname }} {{ $appointment->staff->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $appointment->appointment_time }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 rounded-full text-xs font-medium 
                                            @if($appointment->status == 'active') status-active
                                            @elseif($appointment->status == 'completed') status-completed
                                            @else status-cancelled @endif">
                                            @if($appointment->status == 'active') Активна
                                            @elseif($appointment->status == 'completed') Завершена
                                            @else Отменена @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                        @if($appointment->status != 'active')
                                            <form action="{{ route('admin.appointments.activate', $appointment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="action-btn bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 px-3 py-1 rounded-md text-sm transition-all flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Активировать
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status != 'completed')
                                            <form action="{{ route('admin.appointments.complete', $appointment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="action-btn bg-green-500/20 hover:bg-green-500/30 text-green-400 px-3 py-1 rounded-md text-sm transition-all flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Завершить
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($appointment->status != 'cancelled')
                                            <form action="{{ route('admin.appointments.cancel', $appointment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="action-btn bg-red-500/20 hover:bg-red-500/30 text-red-400 px-3 py-1 rounded-md text-sm transition-all flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Отменить
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Записи не найдены
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Пагинация -->
                @if($appointments->hasPages())
                    <div class="bg-white/5 px-6 py-4 border-t border-white/10">
                        {{ $appointments->appends(request()->query())->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Добавляем анимацию при загрузке страницы
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = `all 0.3s ease ${index * 0.05}s`;
                
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100);
            });
        });
    </script>
</body>
</html>