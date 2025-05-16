<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Филиалы</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .branch-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    .branch-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
    }
    .day-schedule {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 2px 6px;
    }
    .day-off {
      color: rgba(255, 255, 255, 0.5);
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
  <x-header class="flex-shrink-0"/>
  <div class="container mx-auto px-4 py-8 flex-grow">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
      <div class="mb-4 md:mb-0">
        <h1 class="text-2xl md:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-100">
          <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          Управление филиалами
        </h1>
        <p class="text-gray-400 mt-2">Просмотр и редактирование всех филиалов клиники</p>
      </div>
      <a href="{{ route('admin.branches.create') }}" 
         class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Добавить филиал
      </a>
    </div>

        @if(session('error'))
    <div class="bg-red-500 text-white p-4 rounded-lg mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div>
      @if($branches->isEmpty())
        <div class="section-card p-8 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <h3 class="text-xl font-medium text-gray-300 mt-4">Нет добавленных филиалов</h3>
          <p class="text-gray-400 mt-2">Начните с добавления первого филиала</p>
        </div>
      @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($branches as $branch)
            <div class="branch-card overflow-hidden">
              <div class="relative h-48 overflow-hidden">
                <img src="{{ asset('storage/' . $branch->image) }}" 
                     alt="Филиал {{ $branch->address }}" 
                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-4">
                  <h3 class="text-xl font-semibold text-white">
                    {{ Str::limit($branch->address, 40) }}
                  </h3>
                </div>
              </div>
              
              <div class="p-5">
                <div class="space-y-3 mb-6">
                  @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    <div class="flex justify-between items-center">
                      <span class="text-gray-400">{{ trans("days.$day") }}</span>
                      @if($branch->{$day.'_open'})
                        <span class="day-schedule text-sm">
                          {{ Carbon\Carbon::parse($branch->{$day.'_open'})->format('H:i') }} - 
                          {{ Carbon\Carbon::parse($branch->{$day.'_close'})->format('H:i') }}
                        </span>
                      @else
                        <span class="day-off text-sm">Выходной</span>
                      @endif
                    </div>
                  @endforeach
                </div>

                @if($branch->status === 'active')
                <span class="badge badge-success">Работает</span>
            @else
                <span class="badge badge-secondary">Не работает</span>
            @endif
                
                <div class="flex space-x-3">
                  <a href="{{ route('admin.branches.edit', $branch->id) }}" 
                     class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-sm text-center flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Редактировать
                  </a>
                  <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-sm flex items-center justify-center"
                            onclick="return confirm('Вы уверены, что хотите удалить этот филиал?')">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Удалить
                    </button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <script>
    // Анимация карточек при загрузке
    document.addEventListener('DOMContentLoaded', () => {
      const cards = document.querySelectorAll('.branch-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `all 0.4s ease ${index * 0.1}s`;
        
        setTimeout(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 100);
      });
    });
  </script>
</body>
</html>