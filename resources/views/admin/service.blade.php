<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Услуги</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .table-row {
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .table-row:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .description-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: rgba(255, 255, 255, 0.7);
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
        .photo-cell {
            width: 80px;
        }
        .duration-cell {
            width: 100px;
        }
        .actions-cell {
            width: 120px;
        }
    </style>
    <style>
        .error-message {
            color: #F87171;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .input-error {
            border-color: #F87171 !important;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="card p-6">
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
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Управление услугами
                </h1>
                <a href="{{ route('admin.services.create') }}" class="btn-primary text-white px-6 py-3 rounded-lg flex items-center mt-4 md:mt-0">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Добавить услугу
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Название</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Описание</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Цена</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider photo-cell">Фото</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Сотрудник</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider duration-cell">Длительность</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider actions-cell">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($services as $service)
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $service->name }}</td>
                            <td class="px-6 py-4 text-sm description-cell" title="{{ $service->description }}">
                                {{ Str::limit($service->description, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $service->price }} ₽</td>
                            <td class="px-6 py-4 whitespace-nowrap photo-cell">
                                @if($service->image)
                                <div class="w-16 h-16 rounded-md overflow-hidden border border-white/20">
                                    <img src="{{asset('storage/' .  $service->image)}}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <span class="text-gray-400">Нет фото</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($service->staff->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($service->staff as $staffMember)
                                            <span class="bg-blue-500/20 text-blue-200 px-2 py-1 rounded-full text-xs">{{ $staffMember->first_name }} {{ $staffMember->last_name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">Не назначен</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white duration-cell">
                                <span class="bg-purple-500/20 text-purple-200 px-2 py-1 rounded-full">{{ $service->duration }} мин</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium actions-cell">
                                <div class="flex space-x-4">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="action-link text-blue-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Ред.
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту услугу?');">
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
            
            @if($services->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-300">Услуги не найдены</h3>
                <p class="mt-1 text-gray-400">Начните с добавления новой услуги</p>
                <div class="mt-6">
                    <a href="{{ route('admin.services.create') }}" class="btn-primary text-white px-6 py-3 rounded-lg inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Добавить услугу
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</body>
</html>