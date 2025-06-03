<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Админ-панель</title>
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
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    <div class="flex-grow flex items-center justify-center w-full p-4">
        <div class="w-full max-w-5xl">
            <div class="flex flex-col items-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-center mb-2 flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Админ-панель
                </h1>
                <p class="text-gray-300 text-center max-w-lg">Панель управления контентом и пользователями системы</p>
            </div>
            
            <div class="admin-card p-6 md:p-8 w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Управление контентом -->
                    <div class="panel-card p-5">
                        <h3 class="text-xl font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                            </svg>
                            Управление контентом
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.categories.index') }}" class="admin-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Категории услуг</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.services.index') }}" class="admin-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Услуги</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.staff.index') }}" class="admin-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Персонал</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.branches.index') }}" class="admin-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Филиалы</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.all-appointments') }}" class="admin-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Записи на прием</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Экспорт данных -->
                    <div class="panel-card p-5">
                        <h3 class="text-xl font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Экспорт данных
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.reports.completed') }}" class="export-btn text-white px-4 py-3 rounded-lg flex items-center justify-between">
                                <span>Экспорт заявок</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>