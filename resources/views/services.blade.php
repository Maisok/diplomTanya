<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Услуги</title>
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
    .service-card {
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .service-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .category-btn {
      transition: all 0.2s ease;
    }
    .category-btn:hover {
      transform: translateY(-2px);
    }
    .search-input {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
    }
    .search-input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    .service-image {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

      /* Добавляем новые стили для обрезания текста */
      .service-text-container {
      position: relative;
      overflow: hidden;
      white-space: nowrap;
      width: 100%;
    }
    
    .service-text {
      display: inline-block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body class="min-h-screen text-white">
  <x-header/>
  
  <div class="container mx-auto px-4 py-12">
    <!-- Поиск и фильтры -->
    <div class="section-card p-8 mb-12">
      <h1 class="text-3xl font-bold text-center mb-6">Наши услуги</h1>
      
      <!-- Поисковая строка -->
      <div class="mb-8 max-w-2xl mx-auto">
        <form action="{{ route('showservice') }}" method="GET" id="search-form">
          <div class="relative flex items-center">
            <input 
              type="text" 
              name="search" 
              placeholder="Поиск по названию..." 
              value="{{ request('search') }}"
              class="search-input w-full px-6 py-3 rounded-full focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
            
            @if(request('search'))
              <button 
                type="button" 
                onclick="resetSearch()" 
                class="absolute right-14 text-gray-300 hover:text-white"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            @endif
            
            <button 
              type="submit" 
              class="absolute right-4 text-gray-300 hover:text-white"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </form>
      </div>

      <!-- Категории -->
      <div class="flex flex-wrap gap-3 justify-center">
        <a 
            href="{{ route('showservice') }}" 
            class="category-btn px-6 py-2 rounded-full {{ !request('category') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'bg-white/10 hover:bg-white/20' }}"
        >
            Все услуги
        </a>
    
        @foreach($categories as $category)
            <a 
                href="{{ route('showservice', ['category' => $category->id, 'search' => request('search')]) }}" 
                class="category-btn px-6 py-2 rounded-full {{ request('category') == $category->id ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'bg-white/10 hover:bg-white/20' }}"
            >
                {{ $category->name }}
            </a>
        @endforeach
    </div>
    
    <!-- Пагинация -->
    <div class="mt-8">
        {{ $categories->appends(['search' => request('search'), 'category' => request('category')])->links() }}
    </div>
    </div>

    <!-- Список услуг -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="services-container">
      @foreach($services as $service)
        <div class="service-card rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl">
          <a href="{{ route('services.show', $service) }}" class="block h-full">
            <!-- Картинка услуги -->
            <div class="overflow-hidden">
              <img 
                src="{{ asset('storage/' . $service->image) }}" 
                alt="{{ $service->name }}"
                class="w-full h-48 object-cover transition-transform duration-500 hover:scale-105"
              >
            </div>
            
            <!-- Контент услуги -->
            <div class="p-6 flex flex-col h-full">
              <div class="flex-grow">
                <h2 class="text-xl font-semibold mb-3">{{ $service->name }}</h2>
                <p class="service-text text-gray-300 mb-4">{{ Str::limit($service->description, 100) }}</p>
                
                <!-- Рейтинг -->
                @if($service->average_rating)
                  <div class="flex items-center mb-4">
                    <div class="flex mr-2">
                      @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($service->average_rating) ? 'text-yellow-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                      @endfor
                    </div>
                    <span class="text-sm text-gray-300">({{ number_format($service->average_rating, 1) }})</span>
                  </div>
                @else
                  <div class="text-sm text-gray-400 mb-4">Нет оценок</div>
                @endif
              </div>
              
              <div class="mt-auto pt-4 border-t border-white/10">
                <div class="flex justify-between items-center">
                  <span class="text-2xl font-bold text-blue-400">{{ $service->price }} ₽</span>
                  @if($service->category)
                    <span class="text-sm bg-white/10 px-3 py-1 rounded-full">{{ $service->category->name }}</span>
                  @endif
                </div>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </div>

  <script>
    function resetSearch() {
      const form = document.getElementById('search-form');
      const searchInput = form.querySelector('input[name="search"]');
      searchInput.value = '';
      
      // Удаляем параметр search из URL
      const url = new URL(window.location.href);
      url.searchParams.delete('search');
      window.location.href = url.toString();
    }

    function filterServices(categoryId) {
      // Обновляем URL без перезагрузки страницы
      const url = new URL(window.location.href);
      if (categoryId === 'all') {
        url.searchParams.delete('category');
      } else {
        url.searchParams.set('category', categoryId);
      }
      window.history.pushState({}, '', url);
      
      // Показываем/скрываем услуги
      document.querySelectorAll('.service-card').forEach(card => {
        const cardCategory = card.querySelector('.category-tag')?.textContent.trim();
        if (categoryId === 'all' || cardCategory === categoryId) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>