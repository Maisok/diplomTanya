<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Весь коллектив</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .specialist-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    .specialist-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 255, 255, 0.15);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }
    .specialist-image {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    .specialist-image:hover {
      transform: scale(1.05);
    }
    .rating-star {
      transition: all 0.2s ease;
    }
    .rating-star:hover {
      transform: scale(1.2);
    }
    /* Стили для модального окна */
    .image-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.9);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .modal-image {
      max-width: 90%;
      max-height: 90%;
      object-fit: contain;
    }
    .close-modal {
      position: absolute;
      top: 30px;
      right: 30px;
      color: white;
      font-size: 40px;
      cursor: pointer;
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
  <x-header class="flex-shrink-0"/>
  
  <div class="flex-grow py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">Наша команда</h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto">
          Профессиональные специалисты с международным опытом работы
        </p>
      </div>

      <!-- Модальное окно для изображения -->
      <div id="imageModal" class="image-modal">
        <span class="close-modal">&times;</span>
        <img id="modalImage" class="modal-image" src="">
      </div>

      <div class="space-y-6">
        @foreach($specialists as $specialist)
        <div class="specialist-card p-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Фото специалиста -->
                <div class="relative">
                    <img 
                        src="{{ $specialist->image ? asset('storage/'.$specialist->image) : asset('img/doctor-default.jpg') }}" 
                        alt="{{ $specialist->surname }} {{ $specialist->name }}"
                        class="specialist-image"
                        onclick="openModal(this)"
                        onerror="this.src='{{ asset('img/doctor-default.jpg') }}'"
                        data-full-image="{{ $specialist->image ? asset('storage/'.$specialist->image) : asset('img/doctor-default.jpg') }}"
                    >
                    
                    @if($specialist->average_rating)
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full whitespace-nowrap">
                            {{ number_format($specialist->average_rating, 1) }}
                        </div>
                    @endif
                </div>
    
                <!-- Информация о специалисте -->
                <div class="flex-grow text-center md:text-left">
                    <h2 class="text-2xl font-bold">
                        {{ $specialist->surname }} {{ $specialist->name }}
                    </h2>
    
                    <!-- Услуги -->
                    @if($specialist->services->isNotEmpty())
                        <div class="mt-2 mb-3">
                            @foreach($specialist->services->take(3) as $service)
                                <span class="inline-block bg-white/10 text-sm px-3 py-1 rounded-full mr-2 mb-2">
                                    {{ $service->name }}
                                </span>
                            @endforeach
    
                            @if($specialist->services->count() > 3)
                                <span class="inline-block bg-white/10 text-sm px-3 py-1 rounded-full">
                                    +{{ $specialist->services->count() - 3 }} ещё
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-400 text-sm mt-2">Нет услуг</p>
                    @endif
    
                    <!-- Рейтинг -->
                    @if($specialist->staffAppointments->avg('rating'))
                        <div class="flex items-center justify-center md:justify-start">
                            <div class="flex mr-2">
                                @php
                                    $rating = round($specialist->staffAppointments->avg('rating'), 1);
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 rating-star {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-300">
                                {{ $specialist->staffAppointments->count() }} отзывов
                            </span>
                        </div>
                    @else
                        <div class="text-sm text-gray-400">Пока нет оценок</div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
      </div>
    </div>
  </div>

  <script>
    // Функция для открытия модального окна с изображением
    function openModal(imgElement) {
      const modal = document.getElementById('imageModal');
      const modalImg = document.getElementById('modalImage');
      const closeBtn = document.querySelector('.close-modal');
      
      // Устанавливаем изображение в модальное окно
      modalImg.src = imgElement.getAttribute('data-full-image');
      modal.style.display = 'flex';
      
      // Закрытие по клику на крестик
      closeBtn.onclick = function() {
        modal.style.display = 'none';
      }
      
      // Закрытие по клику вне изображения
      modal.onclick = function(event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      }
      
      // Закрытие по ESC
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          modal.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>