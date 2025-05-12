<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - Сброс пароля</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
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
        <h1 class="text-3xl font-bold text-white mb-2">Сброс пароля</h1>
        <p class="text-gray-300">Введите новый пароль для вашего аккаунта</p>
      </div>

      <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ request()->email }}">

        <div>
          <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Новый пароль</label>
          <input 
            id="password" 
            type="password" 
            name="password" 
            required 
            class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
            placeholder="••••••••"
            minlength="8"
          >
          @error('password')
            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
          @enderror
        </div>
        
        <div>
          <label for="password-confirm" class="block text-sm font-medium text-gray-300 mb-1">Подтвердите пароль</label>
          <input 
            id="password-confirm" 
            type="password" 
            name="password_confirmation" 
            required 
            class="input-field w-full px-4 py-3 rounded-lg bg-white/10 text-white placeholder-gray-400 border border-white/20 focus:border-white/40 focus:outline-none"
            placeholder="••••••••"
            minlength="8"
          >
        </div>
        
        <button 
          type="submit" 
          class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg shadow-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          Сбросить пароль
        </button>
      </form>

      <div class="mt-6 text-center">
        <a 
          href="{{ route('login') }}" 
          class="text-sm text-blue-300 hover:text-blue-200 transition-colors"
        >
          Вернуться к авторизации
        </a>
      </div>
    </div>
  </div>
</body>
</html>