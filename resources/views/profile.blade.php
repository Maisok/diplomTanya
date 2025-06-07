<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Личный кабинет</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .profile-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .appointment-card {
      backdrop-filter: blur(5px);
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    .appointment-card:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }
    .input-field {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.1) !important;
      border: 1px solid rgba(255, 255, 255, 0.2) !important;
      color: white !important;
    }
    .input-field:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.12);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
      width: 90%;
      max-width: 500px;
      padding: 25px;
      color: white;
    }
    .star-btn {
      transition: all 0.2s;
    }
    .star-btn:hover svg {
      fill: currentColor;
    }
    .rating-stars:hover .star-btn svg {
      fill: currentColor;
    }
    .rating-stars .star-btn:hover ~ .star-btn svg {
      fill: none;
    }
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .status-completed {
      background-color: rgba(16, 185, 129, 0.2);
      color: rgb(110, 231, 183);
    }
    .status-canceled {
      background-color: rgba(239, 68, 68, 0.2);
      color: rgb(252, 165, 165);
    }
    .status-upcoming {
      background-color: rgba(59, 130, 246, 0.2);
      color: rgb(147, 197, 253);
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
  <x-header class="flex-shrink-0"/>
  
  <div class="flex-grow py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-4xl font-bold text-center mb-12">Личный кабинет</h1>

      <!-- Уведомления -->
      @if(session('success'))
        <div class="profile-card p-4 mb-6 bg-green-500/20 border border-green-400/30">
          <p class="text-green-300 text-center">{{ session('success') }}</p>
        </div>
      @endif
      @if(session('error'))
        <div class="profile-card p-4 mb-6 bg-red-500/20 border border-red-400/30">
          <p class="text-red-300 text-center">{{ session('error') }}</p>
        </div>
      @endif

      <!-- Форма профиля -->
      <div class="profile-card p-8 mb-12">
        <h2 class="text-2xl font-bold mb-6">Редактирование профиля</h2>
        
        <form action="{{ route('profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return validateProfileForm()">
          @csrf
          
          <div>
              <label for="name" class="block text-sm font-medium mb-2">Имя*</label>
              <input type="text" name="name" id="name" maxlength="50" required
                     value="{{ old('name', $user->name) }}"
                     class="input-field w-full px-4 py-3 rounded-lg"
                     oninput="formatName(this)"
                     pattern="^[А-ЯЁA-Z][а-яёa-z\-]+$"
                     title="Имя должно начинаться с заглавной буквы">
              @error('name')
                  <span class="text-red-400 text-sm">{{ $message }}</span>
              @enderror
          </div>
          
          <div>
              <label for="surname" class="block text-sm font-medium mb-2">Фамилия</label>
              <input type="text" name="surname" id="surname" maxlength="50"
                     value="{{ old('surname', $user->surname) }}"
                     class="input-field w-full px-4 py-3 rounded-lg"
                     oninput="formatName(this)"
                     pattern="^[А-ЯЁA-Z][а-яёa-z\-]+$"
                     title="Фамилия должна начинаться с заглавной буквы">
              @error('surname')
                  <span class="text-red-400 text-sm">{{ $message }}</span>
              @enderror
          </div>
          
          <div>
              <label for="phone" class="block text-sm font-medium mb-2">Телефон*</label>
              <input type="text" name="phone" id="phone" maxlength="15" required
                     value="{{ old('phone', $user->phone) }}"
                     class="input-field w-full px-4 py-3 rounded-lg"
                     placeholder="8 999 999 99 99">
              @error('phone')
                  <span class="text-red-400 text-sm">{{ $message }}</span>
              @enderror
          </div>
          
          <div>
              <label for="password" class="block text-sm font-medium mb-2">Новый пароль</label>
              <input type="password" name="password" id="password"
                     class="input-field w-full px-4 py-3 rounded-lg"
                     placeholder="Оставьте пустым, если не хотите менять"
                     minlength="8">
              @error('password')
                  <span class="text-red-400 text-sm">{{ $message }}</span>
              @enderror
          </div>
          @if (!(auth()->user()->role === 'staff'))
          <div class="md:col-span-2 flex justify-center mt-4">
              <button type="submit" 
                      class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-8 py-3 shadow-md transition-all duration-300">
                  Сохранить изменения
              </button>
          </div>
          @endif
      </form>
      </div>

      <!-- Блок с email -->
      <div class="profile-card p-6 mb-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="mb-4 md:mb-0">
            <h3 class="text-lg font-semibold mb-1">Email</h3>
            <div class="flex items-center">
              <span class="text-gray-300">{{ $user->email ?? 'Не указан' }}</span>
              @if($user->email)
                @if($user->email_verified_at || $user->yandex_id)
                  <span class="ml-2 text-xs bg-green-500/20 text-green-300 px-2 py-1 rounded-full">подтвержден</span>
                @else
                  <span class="ml-2 text-xs bg-yellow-500/20 text-yellow-300 px-2 py-1 rounded-full">не подтвержден</span>
                @endif
              @endif
            </div>
          </div>
          @if (!(auth()->user()->role === 'staff'))
          @if(!$user->yandex_id)
            <button id="emailModalBtn" 
                    class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-6 py-2 shadow-md transition-all duration-300">
              {{ $user->email ? 'Подтвердить/Изменить' : 'Добавить email' }}
            </button>
          @endif
          @endif
        </div>
      </div>

      <!-- Записи -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Актуальные записи -->
        <div>
          <h2 class="text-2xl font-bold mb-6">Актуальные записи</h2>
          
          @forelse ($upcomingAppointments as $appointment)
            <div class="appointment-card p-5 mb-4">
              <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                  <p class="text-sm text-gray-400">Услуга</p>
                  <p>{{ $appointment->service->name }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Специалист</p>
                  <p>{{ $appointment->staff->name }} {{ $appointment->staff->surname }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Филиал</p>
                  <p>{{ $appointment->branch->address }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Дата и время</p>
                  <p>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d.m.Y H:i') }}</p>
                </div>
              </div>
              
              <div class="flex justify-between items-center">
                @php
                  $statusTranslations = [
                    'active' => 'Активная',
                    'completed' => 'Завершена', 
                    'cancelled' => 'Отменена'
                  ];
                  $translatedStatus = $statusTranslations[$appointment->status] ?? $appointment->status;
                  
                  // Стили для разных статусов
                  $statusStyles = [
                    'active' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                  ];
                  $currentStatusStyle = $statusStyles[$appointment->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $currentStatusStyle }}">
                  {{ $translatedStatus }}
                </span>
                
                @if($appointment->status == 'active')
                  @php
                    $now = \Carbon\Carbon::now();
                    $appointmentTime = \Carbon\Carbon::parse($appointment->appointment_time);
                    $hoursUntilAppointment = $now->diffInHours($appointmentTime, false);
                    $canCancel = $hoursUntilAppointment >= 1;
                  @endphp
                  
                  <form action="{{ route('appointments.cancel', $appointment) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="flex items-center gap-1 px-3 py-1 text-sm font-medium rounded-lg transition-all duration-200 
                                   {{ $canCancel ? 'bg-red-500/10 text-red-600 hover:bg-red-500/20 hover:text-red-700' : 'bg-gray-500/10 text-gray-400 cursor-not-allowed' }}"
                            {{ !$canCancel ? 'disabled' : '' }}
                            onclick="{{ $canCancel ? "return confirm('Вы уверены, что хотите отменить запись?')" : '' }}"
                            title="{{ !$canCancel ? 'Отмена возможна не позднее чем за 1 час до записи' : '' }}">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Отменить
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="appointment-card p-5 text-center text-gray-400">
              Нет актуальных записей
            </div>
          @endforelse
        </div>
        
        <!-- История записей -->
        <div>
          <h2 class="text-2xl font-bold mb-6">История записей</h2>
          
          @forelse ($pastAppointments as $appointment)
            <div class="appointment-card p-5 mb-4 hover:shadow-lg transition-shadow duration-200">
              <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                  <p class="text-sm text-gray-400">Услуга</p>
                  <p class="font-medium">{{ $appointment->service->name }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Специалист</p>
                  <p class="font-medium">{{ $appointment->staff->name }} {{ $appointment->staff->surname }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Филиал</p>
                  <p class="font-medium">{{ $appointment->branch->address }}</p>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Дата и время</p>
                  <p class="font-medium">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d.m.Y H:i') }}</p>
                </div>
              </div>
              
              <div class="flex justify-between items-center">
                @php
                  $statusTranslations = [
                    'active' => 'Активная',
                    'completed' => 'Завершена',
                    'cancelled' => 'Отменена'
                  ];
                  $translatedStatus = $statusTranslations[$appointment->status] ?? $appointment->status;
                  
                  $statusClasses = [
                    'active' => 'bg-blue-100 text-blue-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'canceled' => 'bg-red-100 text-red-800'
                  ];
                  $currentStatusClass = $statusClasses[$appointment->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $currentStatusClass }}">
                  {{ $translatedStatus }}
                </span>
                
                @if($appointment->status == 'completed' && !$appointment->rating)
                  <form action="{{ route('appointments.rate', $appointment) }}" method="POST" class="flex items-center">
                    @csrf
                    <span class="text-sm text-gray-400 mr-2">Оцените:</span>
                    <div class="rating-stars flex space-x-1">
                      @for($i = 1; $i <= 5; $i++)
                        <button type="submit" name="rating" value="{{ $i }}" 
                                class="star-btn focus:outline-none transition-transform hover:scale-125"
                                title="Оценить на {{ $i }} {{ $i == 1 ? 'звезду' : ($i < 5 ? 'звезды' : 'звёзд') }}">
                          <svg class="w-6 h-6 text-gray-400 hover:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                          </svg>
                        </button>
                      @endfor
                    </div>
                  </form>
                @elseif($appointment->rating)
                  <div class="flex items-center">
                    <span class="text-sm text-gray-400 mr-2">Ваша оценка:</span>
                    <div class="flex space-x-1">
                      @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= $appointment->rating ? 'text-yellow-400 fill-current' : 'text-gray-400' }}" 
                             viewBox="0 0 20 20"
                             title="{{ $i }} {{ $i == 1 ? 'звезда' : ($i < 5 ? 'звезды' : 'звёзд') }}">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                      @endfor
                    </div>
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="appointment-card p-8 text-center text-gray-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              <p class="text-lg">Нет записей в истории</p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Кнопка выхода -->
      <div class="flex justify-center mt-12">
        <form action="{{route('logout')}}" method="POST">
          @csrf
          <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg px-6 py-3 shadow-md transition-all duration-300">
            Выйти из аккаунта
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Модальное окно для email -->
  <div id="emailModal" class="modal">
    <div class="modal-content">
      <span class="close-modal text-2xl font-bold hover:text-gray-300 cursor-pointer">&times;</span>
      <h2 class="text-xl font-bold mb-6">{{ $user->email ? 'Изменить email' : 'Добавить email' }}</h2>
      
      <form id="emailForm" action="{{ route('profile.update-email') }}" method="POST">
        @csrf
        <div class="mb-6">
          <label for="email" class="block text-sm font-medium mb-2">Email</label>
          <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                 class="input-field w-full px-4 py-3 rounded-lg"
                 maxlength="100">
          @error('email')
            <span class="text-red-400 text-sm">{{ $message }}</span>
          @enderror
        </div>
        
        <div id="verificationSection" class="hidden mb-6">
          <div>
            <label for="verification_code" class="block text-sm font-medium mb-2">Код подтверждения</label>
            <input type="text" name="verification_code" id="verification_code" 
                   class="input-field w-full px-4 py-3 rounded-lg">
            @error('verification_code')
              <span class="text-red-400 text-sm">{{ $message }}</span>
            @enderror
          </div>
        </div>
        
        <div class="flex justify-between">
          <button type="button" id="sendCodeBtn" 
                  class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-6 py-2 shadow-md transition-all duration-300">
            Отправить код
          </button>
          <button type="submit" id="confirmBtn" 
                  class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg px-6 py-2 shadow-md transition-all duration-300 hidden">
            Подтвердить
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('emailModal');
      const modalBtn = document.getElementById('emailModalBtn');
      const closeModal = document.querySelector('.close-modal');
      const emailForm = document.getElementById('emailForm');
      const sendCodeBtn = document.getElementById('sendCodeBtn');
      const confirmBtn = document.getElementById('confirmBtn');
      const verificationSection = document.getElementById('verificationSection');
      const emailInput = document.getElementById('email');
      
      // Открытие модального окна
      modalBtn?.addEventListener('click', function() {
        modal.style.display = 'flex';
      });
      
      // Закрытие модального окна
      closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
      });
      
      // Закрытие при клике вне окна
      window.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.style.display = 'none';
        }
      });
      
      // Отправка кода подтверждения
      sendCodeBtn.addEventListener('click', function() {
        const email = emailInput.value.trim();
        const isYandexUser = {{ $user->yandex_id ? 'true' : 'false' }};
        
        if (!email) {
          alert('Пожалуйста, введите email');
          return;
        }
        
        // Для Yandex пользователей сразу подтверждаем
        if (isYandexUser) {
          emailForm.submit();
          return;
        }
        
        sendCodeBtn.disabled = true;
        sendCodeBtn.innerHTML = 'Отправка...';
        
        fetch('{{ route("profile.send-verification-code") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            verificationSection.classList.remove('hidden');
            sendCodeBtn.classList.add('hidden');
            confirmBtn.classList.remove('hidden');
            alert('Код подтверждения отправлен на ваш email');
          } else {
            alert(data.message || 'Ошибка при отправке кода');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Произошла ошибка при отправке кода');
        })
        .finally(() => {
          sendCodeBtn.disabled = false;
          sendCodeBtn.innerHTML = 'Отправить код';
        });
      });

      // Форматирование телефона
      const phoneInput = document.getElementById('phone');
      if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
          let phone = e.target.value.replace(/\D/g, '');
          let formattedPhone = '';
          
          if (phone.length > 0) {
            formattedPhone = '8 ';
          }
          if (phone.length > 1) {
            formattedPhone += phone.substring(1, 4) + ' ';
          }
          if (phone.length > 4) {
            formattedPhone += phone.substring(4, 7) + ' ';
          }
          if (phone.length > 7) {
            formattedPhone += phone.substring(7, 9) + ' ';
          }
          if (phone.length > 9) {
            formattedPhone += phone.substring(9, 11);
          }
          
          e.target.value = formattedPhone;
        });
        
        // Инициализация формата при загрузке
        if (phoneInput.value) {
          let phone = phoneInput.value.replace(/\D/g, '');
          let formattedPhone = '';
          
          if (phone.length > 0) {
            formattedPhone = '8 ';
          }
          if (phone.length > 1) {
            formattedPhone += phone.substring(1, 4) + ' ';
          }
          if (phone.length > 4) {
            formattedPhone += phone.substring(4, 7) + ' ';
          }
          if (phone.length > 7) {
            formattedPhone += phone.substring(7, 9) + ' ';
          }
          if (phone.length > 9) {
            formattedPhone += phone.substring(9, 11);
          }
          
          phoneInput.value = formattedPhone;
        }
      }
    });
    // Добавьте эти функции в тег <script>
function formatName(input) {
    // Удаляем все кроме букв, пробелов и дефисов
    input.value = input.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s-]/g, '');
    
    // Делаем первую букву заглавной, остальные - строчными
    if (input.value.length > 0) {
        input.value = input.value.split(/\s+/).map(word => 
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');
    }
}

function validateProfileForm() {
    const nameInput = document.getElementById('name');
    const surnameInput = document.getElementById('surname');
    const phoneInput = document.getElementById('phone');
    const phonePattern = /^8 \d{3} \d{3} \d{2} \d{2}$/;
    const namePattern = /^[А-ЯЁA-Z][а-яёa-z\-]+$/u;

    // Валидация имени
    if (!namePattern.test(nameInput.value)) {
        alert('Имя должно начинаться с заглавной буквы и содержать только буквы и дефисы');
        nameInput.focus();
        return false;
    }

    // Валидация фамилии (если заполнена)
    if (surnameInput.value && !namePattern.test(surnameInput.value)) {
        alert('Фамилия должна начинаться с заглавной буквы и содержать только буквы и дефисы');
        surnameInput.focus();
        return false;
    }

    // Валидация телефона
    if (!phonePattern.test(phoneInput.value)) {
        alert('Номер телефона должен быть в формате 8 999 999 99 99');
        phoneInput.focus();
        return false;
    }

    return true;
}

</script>
</body>
</html>