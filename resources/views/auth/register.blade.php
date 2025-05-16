<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Регистрация</title>
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .input-field {
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .input-field:focus {
      box-shadow: 0 4px 10px rgba(58, 85, 106, 0.3);
      transform: translateY(-2px);
    }
    .auth-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body class="flex flex-col min-h-screen">
  <x-header class="flex-shrink-0"/>
  
  <div class="flex-grow flex items-center justify-center p-4">
    <div class="auth-card w-full max-w-md p-8 sm:p-10">
      <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-white mb-2">Создайте аккаунт</h1>
        <p class="text-gray-300">Заполните форму, чтобы начать пользоваться сервисом</p>
      </div>

      <form action="{{ route('register') }}" method="POST" class="space-y-6" onsubmit="return validateForm()">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Имя*</label>
              <input 
                  type="text" 
                  id="name" 
                  name="name" 
                  placeholder="Иван" 
                  maxlength="50" 
                  class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
                  value="{{ old('name') }}"
                  required
                  oninput="formatName(this)"
                  pattern="^[А-ЯЁA-Z][а-яёa-z\-]+$"
                  title="Имя должно начинаться с заглавной буквы"
              >
              @error('name')
                  <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
              @enderror
          </div>
          
          <div>
              <label for="surname" class="block text-sm font-medium text-gray-300 mb-1">Фамилия*</label>
              <input 
                  type="text" 
                  id="surname" 
                  name="surname" 
                  placeholder="Иванов" 
                  maxlength="50" 
                  class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
                  value="{{ old('surname') }}"
                  required
                  oninput="formatName(this)"
                  pattern="^[А-ЯЁA-Z][а-яёa-z\-]+$"
                  title="Фамилия должна начинаться с заглавной буквы"
              >
              @error('surname')
                  <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
              @enderror
          </div>
      </div>

        <div>
          <label for="phone" class="block text-sm font-medium text-gray-300 mb-1">Телефон*</label>
          <input 
            type="text" 
            id="phone" 
            name="phone" 
            placeholder="8 999 123 45 67" 
            maxlength="15" 
            class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
            value="{{ old('phone') }}"
            oninput="formatPhoneNumber(this)"
            required
          >
          @error('phone')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Электронная почта</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              placeholder="explame@mail.com" 
              minlength="8" 
              class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
              required
            >
            @error('email')
              <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Пароль*</label>
            <input 
              type="password" 
              id="password" 
              name="password" 
              placeholder="••••••••" 
              minlength="8" 
              class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
              required
            >
            @error('password')
              <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
          </div>

          
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Подтвердите*</label>
            <input 
              type="password" 
              id="password_confirmation" 
              name="password_confirmation" 
              placeholder="••••••••" 
              minlength="8" 
              class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
              required
            >
          </div>
        </div>

        <div class="g-recaptcha" data-sitekey="6LfupH4qAAAAAM0ukK0Vhjo3pDXriY6NqpLxZUXE"></div>
        @error('g-recaptcha-response')
          <p class="mt-1 text-sm text-red-400">Пожалуйста, подтвердите, что вы не робот</p>
        @enderror

        <button 
          type="submit" 
          class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          Создать аккаунт
        </button>
      </form>

      <div class="mt-6">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-600"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-transparent text-gray-400">Или зарегистрируйтесь через</span>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3">
          <a 
            href="{{ route('yandex') }}" 
            class="group relative flex items-center justify-center w-full p-3 rounded-lg bg-gradient-to-r from-[#FF3F3F] via-[#FC3F1D] to-[#FF5E1A] overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-[#FC3F1D]/30"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-[#FF5E1A] via-[#FC3F1D] to-[#FF3F3F] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            
            <svg 
              xmlns="http://www.w3.org/2000/svg" 
              viewBox="0 0 24 24" 
              class="w-6 h-6 mr-2 z-10 transition-transform duration-300 group-hover:scale-110 group-hover:drop-shadow-[0_0_6px_rgba(255,255,255,0.8)]"
            >
              <path fill="#FFF" d="M2.04 12c0-5.523 4.476-10 10-10 5.522 0 10 4.477 10 10s-4.478 10-10 10c-5.524 0-10-4.477-10-10z"/>
              <path fill="#FF0000" d="M13.32 7.666h-.924c-1.694 0-2.585.858-2.585 2.123 0 1.43.616 2.1 1.881 2.959l1.045.704-3.003 4.487H7.49l2.695-4.014c-1.55-1.111-2.42-2.19-2.42-4.015 0-2.288 1.595-3.85 4.62-3.85h3.003v11.868H13.32V7.666z"/>
            </svg>
            
            <span class="font-medium text-white z-10">Яндекс</span>
          </a>
        </div>
      </div>

      <div class="mt-8 text-center">
        <p class="text-gray-400">
          Уже есть аккаунт?
          <a href="{{route('login')}}" class="font-medium text-blue-300 hover:text-blue-200 transition-colors">Войти</a>
        </p>
      </div>
    </div>
  </div>

  <script>
    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function formatPhoneNumber(input) {
    let phone = input.value.replace(/\D/g, '');
    let formattedPhone = '';
    
    if (phone.length > 0) {
        formattedPhone = '8 ';
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
    }
    
    input.value = formattedPhone;
}

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

function validateForm() {
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

    // Валидация фамилии
    if (!namePattern.test(surnameInput.value)) {
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