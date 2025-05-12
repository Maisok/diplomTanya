<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для персонала</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">

</head>
<body class="bg-[#5A7684] flex flex-col min-h-screen w-full">
<x-header class="flex-shrink-0"/>
    <div class="flex-grow flex items-center justify-center">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
            <h1 class="text-center text-2xl font-bold mb-6">Вход для персонала</h1>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-600 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ошибка!</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('staff.login') }}">
                @csrf
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700">Номер телефона</label>
                    <input type="text" name="phone" id="phone" placeholder="Номер телефона" maxlength="15" 
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2" 
                           oninput="formatPhoneNumber(this)" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Пароль</label>
                    <input type="password" name="password" id="password" placeholder="Пароль" minlength="8" maxlength="255" 
                           class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 rounded">Войти</button>
            </form>
        </div>
    </div>

    <script>
        function formatPhoneNumber(input) {
            let phone = input.value.replace(/\D/g, ''); // Удаляем все нецифровые символы
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

            input.value = formattedPhone;
        }
    </script>
</body>
</html>