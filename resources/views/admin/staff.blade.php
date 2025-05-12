<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Персонал</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .table-row {
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .table-row:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .table-header {
            color: rgba(255, 255, 255, 0.6);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .action-link {
            transition: all 0.2s ease;
        }
        .action-link:hover {
            color: #3B82F6;
            transform: translateX(2px);
        }
        .delete-btn {
            transition: all 0.2s ease;
        }
        .delete-btn:hover {
            color: #EF4444;
            transform: translateX(2px);
        }
        .staff-photo {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        .empty-state {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="card p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Управление персоналом
                </h1>
                <a href="{{ route('admin.staff.create') }}" class="btn-primary text-white px-6 py-3 rounded-lg flex items-center mt-4 md:mt-0">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Добавить сотрудника
                </a>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500 rounded-lg">
                    <p class="text-red-400">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500 rounded-lg">
                    <p class="text-green-400">{{ session('success') }}</p>
                </div>
            @endif
            
            @if($staff->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ФИО</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Телефон</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Фото</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Филиал</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($staff as $employee)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">
                                    {{ $employee->last_name }} {{ $employee->first_name }} {{ $employee->middle_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $employee->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($employee->image)
                                <div class="staff-photo w-12 h-12 overflow-hidden">
                                    <img src="{{ asset('storage/' . $employee->image) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <span class="text-gray-400">Нет фото</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($employee->branch)
                                <span class="bg-green-500/20 text-green-200 px-2 py-1 rounded-full text-xs">{{ $employee->branch->address }}</span>
                                @else
                                <span class="text-gray-400">Не назначен</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-4">
                                    <a href="{{ route('admin.staff.edit', $employee) }}" class="action-link text-blue-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Ред.
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $employee) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этого сотрудника?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn text-red-400 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Удалить
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state text-center py-12 px-4">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-300">Сотрудники не найдены</h3>
                <p class="mt-1 text-gray-400">Начните с добавления нового сотрудника</p>
                <div class="mt-6">
                    <a href="{{ route('admin.staff.create') }}" class="btn-primary text-white px-6 py-3 rounded-lg inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Добавить сотрудника
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>