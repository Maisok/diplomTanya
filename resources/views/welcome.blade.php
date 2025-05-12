<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Главная</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=9fbfa4df-7869-44a3-ae8e-0ebc49545ea9" type="text/javascript"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .hero-section {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url({{asset("img/bgnav.png")}});
      background-size: cover;
      background-position: center;
    }
    .branch-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.9);
      z-index: 1000;
      overflow-y: auto;
      padding: 20px;
      box-sizing: border-box;
    }
    .modal-content {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.12);
      margin: 2% auto;
      padding: 25px;
      width: 90%;
      max-width: 800px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      max-height: 90vh;
      overflow-y: auto;
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .close-modal {
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      color: white;
    }
    .custom-placemark {
      width: 12px;
      height: 12px;
      background-color: #3B82F6;
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    body.modal-open {
      overflow: hidden;
    }
    .schedule-item {
      display: flex;
      justify-content: space-between;
      padding: 5px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .branch-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
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
    }
    .service-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    #map {
      width: 100%;
      height: 500px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .specialist-card {
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .specialist-card:hover {
      transform: translateY(-5px);
    }
    .specialist-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 50%;
      background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    .specialist-name {
      position: absolute;
      bottom: 0;
      left: 0;
      z-index: 2;
      padding: 1rem;
      color: white;
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
<body class="flex flex-col min-h-screen text-white">
  <x-header class="flex-shrink-0"/>

  <!-- Hero Section -->
  <section class="hero-section flex-grow flex items-center justify-center py-20 px-4">
    <div class="text-center max-w-4xl mx-auto">
      <h1 class="text-4xl md:text-5xl font-bold mb-6">КЛИНИКА ВАШЕЙ МЕЧТЫ</h1>
      <p class="text-xl md:text-2xl mb-8 opacity-90 max-w-2xl mx-auto">
        Лучшая клиника России открылась в Иркутске. У нас 150 филиалов по всей стране и более 2000 постоянных клиентов.
      </p>
      <a href="{{ route('showservice') }}" class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-8 py-3 shadow-lg transition-all duration-300 hover:shadow-xl transform hover:-translate-y-0.5">
        Записаться на прием
      </a>
    </div>
  </section>

  <!-- Branches Section -->
  <section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="section-card p-8">
        <h2 class="text-3xl font-bold text-center mb-4">Наши филиалы</h2>
        <p class="text-center text-gray-300 mb-8 max-w-2xl mx-auto">
          Найдите ближайший к вам филиал на карте
        </p>
        
        <div id="map" class="mb-8"></div>
        
        <div class="text-center">
          <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-6 py-2 shadow-md transition-all duration-300">
            Все филиалы
          </button>
        </div>
      </div>
    </div>
  </section>

  @if($specialists->isNotEmpty())
  <!-- Specialists Section -->
  <section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="section-card p-8">
        <h2 class="text-3xl font-bold text-center mb-4">Наши специалисты</h2>
        <p class="text-center text-gray-300 mb-8 max-w-2xl mx-auto">
          Мы покажем вам одних из лучших дантистов, которые успели поработать за границей.
        </p>
        
        <!-- Фильтруем специалистов с изображениями -->
        @php
          $specialistsWithImages = $specialists->filter(function($specialist) {
              return !empty($specialist->image);
          });
        @endphp
        
        @if($specialistsWithImages->count() >= 3)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          @foreach($specialistsWithImages->take(3) as $specialist)
          <div class="specialist-card rounded-lg overflow-hidden h-80">
            <img src="{{asset('storage/' . $specialist->image)}}" alt="{{ $specialist->first_name }}" class="w-full h-full object-cover" />
            <div class="specialist-name font-semibold text-xl">{{ $specialist->first_name }}</div>
          </div>
          @endforeach
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          @if($specialistsWithImages->count() > 3)
          <div class="specialist-card rounded-lg overflow-hidden h-80">
            <img src="{{asset('storage/' . $specialistsWithImages->get(3)->image)}}" alt="{{ $specialistsWithImages->get(3)->first_name }}" class="w-full h-full object-cover" />
            <div class="specialist-name font-semibold text-xl">{{ $specialistsWithImages->get(3)->first_name }}</div>
          </div>
          @endif
          <a href="{{ route('all.specialists') }}" class="specialist-card rounded-lg overflow-hidden h-80 relative">
            <img src="{{asset('img/all.png')}}" alt="Весь коллектив" class="w-full h-full object-cover" />
            <div class="specialist-name font-semibold text-xl">Весь коллектив</div>
          </a>
        </div>
        @elseif($specialistsWithImages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-{{ min($specialistsWithImages->count(), 4) }} gap-6">
          @foreach($specialistsWithImages as $specialist)
          <div class="specialist-card rounded-lg overflow-hidden h-80">
            <img src="{{asset('storage/' . $specialist->image)}}" alt="{{ $specialist->first_name }}" class="w-full h-full object-cover" />
            <div class="specialist-name font-semibold text-xl">{{ $specialist->first_name }}</div>
          </div>
          @endforeach
          
          @if($specialistsWithImages->count() < 4)
          <a href="{{ route('all.specialists') }}" class="specialist-card rounded-lg overflow-hidden h-80 relative">
            <img src="{{asset('img/all.png')}}" alt="Весь коллектив" class="w-full h-full object-cover" />
            <div class="specialist-name font-semibold text-xl">Весь коллектив</div>
          </a>
          @endif
        </div>
        @else
        <p class="text-center text-gray-400 py-8">Информация о специалистах временно недоступна</p>
        @endif
      </div>
    </div>
  </section>
  @endif
  @if($services->isNotEmpty())
  <!-- Services Section -->
  <section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="section-card p-8">
        <h2 class="text-3xl font-bold text-center mb-4">Услуги</h2>
        <p class="text-center text-gray-300 mb-8 max-w-2xl mx-auto">
          Расскажем о малой части наших услуг и ценах
        </p>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          @foreach($services as $service)
            @if(!empty($service->image))
            <a href="{{ route('services.show', $service) }}" class="service-card bg-white/10 rounded-lg overflow-hidden hover:bg-white/20 transition-all group">
              <!-- Картинка услуги -->
              <div class="h-40 overflow-hidden">
                <img 
                  src="{{ asset('storage/' . $service->image) }}" 
                  alt="{{ $service->name }}"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                >
              </div>
              <!-- Контент карточки -->
              <div class="p-4 flex flex-col">
                <h3 class="font-semibold text-lg mb-2 truncate">{{ $service->name }}</h3>
                <p class="text-blue-400 font-medium mb-2">{{ $service->price }} РУБ</p>
                <div class="service-text-container">
                  <p class="service-text text-gray-300 text-sm">{{ $service->description }}</p>
                  <div class="service-text-fade"></div>
                </div>
              </div>
            </a>
            @endif
          @endforeach
        </div>
        
        @if($services->where('image', '!=', '')->isEmpty())
        <p class="text-center text-gray-400 py-8">Информация об услугах временно недоступна</p>
        @endif
        
        <div class="text-center mt-8">
          <a href="{{ route('showservice') }}" class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-6 py-2 shadow-md transition-all duration-300">
            Записаться на прием
          </a>
        </div>
      </div>
    </div>
  </section>
  @endif
  <!-- Footer -->
  <footer class="py-16 px-4 bg-gradient-to-b from-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto text-center">
      <h2 class="text-3xl font-bold mb-4">Самое удобное расположение в шаговой доступности</h2>
      <p class="text-gray-400 mb-8 max-w-2xl mx-auto">
        До нас можно добраться на общественном транспорте или же на своей машине и припарковаться рядом с клиникой.
      </p>
      <div class="flex justify-center space-x-4">
        <a href="#" class="text-gray-400 hover:text-white transition-colors">
          <i class="fab fa-vk text-2xl"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-white transition-colors">
          <i class="fab fa-telegram text-2xl"></i>
        </a>
        <a href="#" class="text-gray-400 hover:text-white transition-colors">
          <i class="fab fa-instagram text-2xl"></i>
        </a>
      </div>
    </div>
  </footer>

  <!-- Branch Modal -->
  <div id="branchModal" class="branch-modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <div id="modalContent"></div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Обработчик бургер-меню
      const burger = document.getElementById('burger');
      if (burger) {
        burger.addEventListener('click', function() {
          const nav = document.getElementById('nav');
          if (nav) nav.classList.toggle('hidden');
        });
      }

      // Элементы модального окна
      const modal = document.getElementById('branchModal');
      const modalContent = document.getElementById('modalContent');
      const closeModal = document.querySelector('.close-modal');
      const body = document.body;
      
      // Функция для управления прокруткой фона
      function toggleBodyScroll(enable) {
        if (enable) {
          body.classList.remove('modal-open');
        } else {
          body.classList.add('modal-open');
        }
      }
      
      // Закрытие модального окна
      closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        toggleBodyScroll(true);
      });
      
      // Закрытие при клике вне контента
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.style.display = 'none';
          toggleBodyScroll(true);
        }
      });

      // Функция инициализации карты
      function initMap() {
        try {
          // Создаем карту
          const map = new ymaps.Map('map', {
            center: [55.76, 37.64],
            zoom: 10,
            controls: []
          });

          // Массив для хранения промисов геокодирования
          const geocodePromises = [];

          @foreach($branches as $branch)
            geocodePromises.push(
              ymaps.geocode('{{ $branch->address }}', { results: 1 })
                .then(function(res) {
                  const firstGeoObject = res.geoObjects.get(0);
                  if (firstGeoObject) {
                    const coordinates = firstGeoObject.geometry.getCoordinates();
                    
                    // Создаем кастомную метку
                    const placemarkLayout = ymaps.templateLayoutFactory.createClass(
                      '<div class="custom-placemark"></div>'
                    );
                    
                    const placemark = new ymaps.Placemark(coordinates, {
                      branchId: '{{ $branch->id }}',
                      branchName: '{{ $branch->name ?? "Филиал" }}',
                      branchAddress: '{{ $branch->address }}',
                      branchImage: '{{ $branch->image ? asset("storage/" . $branch->image) : asset("img/default-branch.jpg") }}',
                      mondayHours: '{{ $branch->monday_open }} - {{ $branch->monday_close }}',
                      tuesdayHours: '{{ $branch->tuesday_open }} - {{ $branch->tuesday_close }}',
                      wednesdayHours: '{{ $branch->wednesday_open }} - {{ $branch->wednesday_close }}',
                      thursdayHours: '{{ $branch->thursday_open }} - {{ $branch->thursday_close }}',
                      fridayHours: '{{ $branch->friday_open }} - {{ $branch->friday_close }}',
                      saturdayHours: '{{ $branch->saturday_open ? $branch->saturday_open . " - " . $branch->saturday_close : "Выходной" }}',
                      sundayHours: '{{ $branch->sunday_open ? $branch->sunday_open . " - " . $branch->sunday_close : "Выходной" }}',
                    branchServices: `{!! 
                        $branch->staff->flatMap(function($staff) {
                            return $staff->services;
                        })->unique('id')->map(function($service) {
                            return "<li>• " . e($service->name) . "</li>";
                        })->implode('') 
                    !!}`
                    }, {
                      iconLayout: placemarkLayout,
                      iconShape: {
                        type: 'Circle',
                        coordinates: [0, 0],
                        radius: 30
                      }
                    });

                    // Обработчик клика на метку
                    placemark.events.add('click', function(e) {
                      const target = e.get('target');
                      const properties = target.properties.getAll();
                      
                      // Заполняем модальное окно данными
                      modalContent.innerHTML = `
                       <div class="branch-info">
                        <img src="${properties.branchImage}" alt="Фото филиала" class="branch-image">
                        
                        <h3 class="text-2xl font-bold mb-2">${properties.branchName}</h3>
                        <p class="text-gray-300 mb-4">${properties.branchAddress}</p>
                        
                        <div class="mb-6">
                          <h4 class="font-semibold text-xl mb-2">Часы работы:</h4>
                          <div class="schedule-list">
                            <div class="schedule-item">
                              <span class="schedule-day">Понедельник:</span>
                              <span class="schedule-time">${formatHours(properties.mondayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Вторник:</span>
                              <span class="schedule-time">${formatHours(properties.tuesdayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Среда:</span>
                              <span class="schedule-time">${formatHours(properties.wednesdayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Четверг:</span>
                              <span class="schedule-time">${formatHours(properties.thursdayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Пятница:</span>
                              <span class="schedule-time">${formatHours(properties.fridayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Суббота:</span>
                              <span class="schedule-time">${formatHours(properties.saturdayHours)}</span>
                            </div>
                            <div class="schedule-item">
                              <span class="schedule-day">Воскресенье:</span>
                              <span class="schedule-time">${formatHours(properties.sundayHours)}</span>
                            </div>
                          </div>
                        </div>
                        
                        <div class="mb-6">
                          <h4 class="font-semibold text-xl mb-2">Услуги:</h4>
                          <ul class="text-gray-300">
                            ${properties.branchServices}
                          </ul>
                        </div>
                      </div>
                      `;
                      
                      // Показываем модальное окно и отключаем прокрутку фона
                      modal.style.display = 'block';
                      toggleBodyScroll(false);
                    });

                    map.geoObjects.add(placemark);
                    
                    // Для первого филиала центрируем карту
                    if ('{{ $loop->first }}' === '1') {
                      map.setCenter(coordinates, 12);
                    }
                  }
                })
                .catch(function(error) {
                  console.error('Ошибка геокодирования адреса {{ $branch->address }}:', error);
                })
            );
          @endforeach

          // Ждем завершения всех геокодирований
          Promise.all(geocodePromises)
            .then(() => {
              console.log('Все метки добавлены на карту');
            })
            .catch(error => {
              console.error('Ошибка при добавлении меток:', error);
            });

          // Убираем ненужные элементы управления
          map.controls.remove('geolocationControl');
          map.controls.remove('searchControl');
          map.controls.remove('trafficControl');
          map.controls.remove('typeSelector');
          map.controls.remove('fullscreenControl');
          map.controls.remove('rulerControl');
          
          // Отключаем скролл зум
          map.behaviors.disable(['scrollZoom']);

        } catch (error) {
          console.error('Ошибка инициализации карты:', error);
          showMapError(error.message);
        }
      }

      // Функция показа ошибки карты
      function showMapError(message) {
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
          mapContainer.innerHTML = `
            <div class="bg-red-100 text-red-800 p-4 rounded">
              Ошибка загрузки карты: ${message}
            </div>
          `;
        }
      }

      // Проверяем загрузился ли API Яндекс Карт
      if (typeof ymaps !== 'undefined') {
        // Добавляем небольшую задержку для гарантии полной загрузки API
        setTimeout(() => {
          ymaps.ready(initMap);
        }, 100);
      } else {
        console.error('Yandex Maps API не загружен');
        showMapError('Яндекс Карты не загрузились. Пожалуйста, обновите страницу.');
        
        // Пробуем перезагрузить API, если он не загрузился
        const script = document.createElement('script');
        script.src = 'https://api-maps.yandex.ru/2.1/?apikey=9fbfa4df-7869-44a3-ae8e-0ebc49545ea9&lang=ru_RU';
        script.onload = function() {
          if (typeof ymaps !== 'undefined') {
            ymaps.ready(initMap);
          }
        };
        document.head.appendChild(script);
      }
    });

    // Функция для форматирования времени
    function formatHours(timeString) {
  // Если строка пустая или содержит null/undefined
  if (!timeString || timeString.trim() === '' || timeString === 'null - null') {
    return 'Выходной';
  }
  
  // Проверяем, содержит ли строка разделитель времени
  if (!timeString.includes(' - ')) {
    return 'Выходной';
  }
  
  // Разделяем время открытия и закрытия
  const [openTime, closeTime] = timeString.split(' - ');
  
  // Если какое-то из времен отсутствует
  if (!openTime || !closeTime || openTime === 'null' || closeTime === 'null') {
    return 'Выходной';
  }
  
  // Форматируем каждое время (убираем секунды)
  const formatSingleTime = (time) => {
    const parts = time.split(':');
    if (parts.length >= 2) {
      return `${parts[0]}:${parts[1]}`; // Берем только часы и минуты
    }
    return time;
  };
  
  const formattedOpen = formatSingleTime(openTime);
  const formattedClose = formatSingleTime(closeTime);
  
  return `${formattedOpen} - ${formattedClose}`;
}

  </script>
</body>
</html>