<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Создать администратора</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .form-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .form-input {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      color: white;
    }
    .form-input:focus {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.3);
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
  <x-header class="flex-shrink-0"/>
  <div class="container mx-auto px-4 py-8 flex-grow">
    <div class="max-w-2xl mx-auto">
      <div class="flex items-center mb-8">
        <a href="{{ route('admins.index') }}" class="mr-4">
          <svg class="w-6 h-6 text-gray-400 hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
        </a>
        <h1 class="text-2xl md:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-100">
          Создать нового администратора
        </h1>
      </div>

      @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="form-card p-6">
        <form action="{{ route('admins.store') }}" method="POST">
          @csrf
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label for="name" class="block text-gray-400 mb-2">Имя</label>
              <input type="text" name="name" id="name" 
                     class="form-input w-full px-4 py-2 rounded-lg" 
                     value="{{ old('name') }}" required>
            </div>
            
            <div>
              <label for="surname" class="block text-gray-400 mb-2">Фамилия</label>
              <input type="text" name="surname" id="surname" 
                     class="form-input w-full px-4 py-2 rounded-lg" 
                     value="{{ old('surname') }}" required>
            </div>
          </div>
          
          <div class="mb-6">
            <label for="email" class="block text-gray-400 mb-2">Email</label>
            <input type="email" name="email" id="email" 
                   class="form-input w-full px-4 py-2 rounded-lg" 
                   value="{{ old('email') }}" required>
          </div>
          
          <div class="mb-6">
            <label for="phone" class="block text-gray-400 mb-2">Телефон</label>
            <input type="text" name="phone" id="phone" 
                   class="form-input w-full px-4 py-2 rounded-lg" 
                   value="{{ old('phone') }}" 
                     oninput="formatPhoneNumber(this)"
                   placeholder="Формат: 8 ___ ___ __ __" required>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
              <label for="password" class="block text-gray-400 mb-2">Пароль</label>
              <input type="password" name="password" id="password" 
                     class="form-input w-full px-4 py-2 rounded-lg" required>
            </div>
            
            <div>
              <label for="password_confirmation" class="block text-gray-400 mb-2">Подтвердите пароль</label>
              <input type="password" name="password_confirmation" id="password_confirmation" 
                     class="form-input w-full px-4 py-2 rounded-lg" required>
            </div>
          </div>
          
          <div class="flex justify-end">
            <button type="submit" 
                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Создать администратора
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
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
    </script>
</body>
</html>