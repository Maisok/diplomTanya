<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить филиал</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .day-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .day-card:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .time-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }
        .time-input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .error-border {
            border-color: #f87171;
        }

        .error-border {
            border-color: #f87171 !important;
            animation: pulseError 0.5s ease-in-out;
        }

        @keyframes pulseError {
            0% { border-color: rgba(248, 113, 113, 0.1); }
            50% { border-color: rgba(248, 113, 113, 0.6); }
            100% { border-color: #f87171; }
        }

        .error-message {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
        }

        .error-message::before {
            content: "⚠";
            margin-right: 0.5rem;
        }

        #image-preview-container {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    #image-preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 0.5rem;
    }

    .image-preview-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="max-w-3xl mx-auto section-card p-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-100">
                Добавить новый филиал
            </h1>
            
            <!-- Вывод ошибок валидации -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-900/50 border border-red-700 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Ошибки при заполнении формы
                    </h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.branches.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-8">
                    <label for="image" class="block text-gray-300 mb-3 font-medium">Изображение филиала</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-white/30 rounded-lg cursor-pointer bg-white/5 hover:bg-white/10 transition @error('image') error-border @enderror">
                            <div class="flex flex-col items-center justify-center w-full h-full p-4" id="image-preview-container">
                                <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-400">Нажмите для загрузки изображения</p>
                                <p class="text-xs text-gray-400">JPEG, PNG (макс. 2MB)</p>
                            </div>
                            <input id="image" name="image" type="file" class="opacity-0 absolute" accept="image/jpeg,image/png" required>
                        </label>
                    </div>
                    @error('image')
                        <p class="error-message mt-2">
                            @if($message == 'validation.required')
                                Пожалуйста, загрузите изображение филиала
                            @else
                                {{ $message }}
                            @endif
                        </p>
                    @enderror
                </div>
                
                <!-- Адрес филиала -->
                <div class="mb-8">
                    <label for="address" class="block text-gray-300 mb-3 font-medium">Адрес филиала</label>
                    <input type="text" name="address" id="address" 
                        class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-gray-400 @error('address') error-border @enderror" 
                        required 
                        placeholder="г.Москва, ул.Ленина, д.10"
                        maxlength="255"
                        value="{{ old('address') }}">
                    @error('address')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-1">Максимальная длина: 255 символов</p>
                </div>
                
                <!-- Расписание работы -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Расписание работы
                    </h2>
                    <p class="text-gray-400 text-sm mb-6">Оставьте поля пустыми, если филиал не работает в этот день</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="day-card p-4 rounded-lg @error($day.'_open') error-border @enderror @error($day.'_close') error-border @enderror">
                                <h3 class="text-gray-300 font-semibold mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                    {{ trans("days.$day") }}
                                </h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="{{ $day }}_open" class="block text-gray-400 text-sm mb-2">Открытие</label>
                                        <input type="time" name="{{ $day }}_open" id="{{ $day }}_open" 
                                               class="w-full time-input p-2 rounded @error($day.'_open') error-border @enderror"
                                               value="{{ old($day.'_open') }}">
                                        @error($day.'_open')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="{{ $day }}_close" class="block text-gray-400 text-sm mb-2">Закрытие</label>
                                        <input type="time" name="{{ $day }}_close" id="{{ $day }}_close" 
                                               class="w-full time-input p-2 rounded @error($day.'_close') error-border @enderror"
                                               value="{{ old($day.'_close') }}">
                                        @error($day.'_close')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Кнопки -->
                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('admin.branches.index') }}" 
                       class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md text-center flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Отмена
                    </a>
                    <button type="submit" 
                            class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md text-center flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Добавить филиал
                    </button>
                </div>
            </form>
        </div>
    </div>
  
    <script>
           document.querySelector('form').addEventListener('submit', function(e) {
        const imageInput = document.getElementById('image'); // Объявляем один раз
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = {
            'monday': 'понедельник',
            'tuesday': 'вторник',
            'wednesday': 'среду',
            'thursday': 'четверг',
            'friday': 'пятницу',
            'saturday': 'субботу',
            'sunday': 'воскресенье'
        };
        
        // Проверка изображения (только один раз)
        if (!imageInput.files || imageInput.files.length === 0) {
        e.preventDefault();
        
        // Создаем временное видимое поле для фокуса
        const tempInput = document.createElement('input');
        tempInput.type = 'file';
        tempInput.name = 'image';
        tempInput.style.position = 'fixed';
        tempInput.style.top = '0';
        tempInput.style.left = '0';
        tempInput.style.opacity = '0.01';
        document.body.appendChild(tempInput);
        
        // Фокус и удаление временного поля
        setTimeout(() => {
            tempInput.focus();
            setTimeout(() => {
                document.body.removeChild(tempInput);
                // Прокрутка к полю с изображением
                imageInput.closest('.mb-8').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                // Подсветка поля
                document.querySelector('label[for="image"]').classList.add('error-border');
            }, 100);
        }, 100);
        
        return false;
    }
        
        // Проверка типа и размера изображения
        const file = imageInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        const maxSize = 2 * 1024 * 1024;
        
        if (!validTypes.includes(file.type)) {
            alert('Пожалуйста, загрузите изображение в формате JPEG или PNG');
            e.preventDefault();
            return false;
        }
        
        if (file.size > maxSize) {
            alert('Размер изображения не должен превышать 2MB');
            e.preventDefault();
            return false;
        }
        
        // Проверка адреса
        const address = document.getElementById('address').value.trim();
        if (address.length < 5) {
            alert('Пожалуйста, введите корректный адрес (минимум 5 символов)');
            e.preventDefault();
            return false;
        }
        
        // Проверка расписания
        for (const day of days) {
            const open = document.getElementById(`${day}_open`).value;
            const close = document.getElementById(`${day}_close`).value;
            
            if ((open && !close) || (!open && close)) {
                alert(`Для ${dayNames[day]} необходимо указать оба времени или оставить оба поля пустыми`);
                e.preventDefault();
                return false;
            }
            
            if (open && close) {
                if (open < '08:00') {
                    alert(`Время открытия в ${dayNames[day]} не может быть раньше 8:00`);
                    e.preventDefault();
                    return false;
                }
                
                if (close > '21:00') {
                    alert(`Время закрытия в ${dayNames[day]} не может быть позже 21:00`);
                    e.preventDefault();
                    return false;
                }
                
                if (open >= close) {
                    alert(`В ${dayNames[day]} время закрытия должно быть позже времени открытия`);
                    e.preventDefault();
                    return false;
                }
                
                const diffMinutes = (new Date(`2000-01-01T${close}`) - new Date(`2000-01-01T${open}`)) / (1000 * 60);
                if (diffMinutes < 120) {
                    alert(`В ${dayNames[day]} филиал должен работать не менее 2 часов`);
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    // Превью изображения
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('image-preview-container');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewContainer.innerHTML = `
                        <div class="image-preview-wrapper">
                            <img src="${event.target.result}" class="w-full h-full object-contain" alt="Превью">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition">
                                <span class="text-white text-sm">Изменить изображение</span>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                // Возвращаем исходное состояние, если файл не выбран
                previewContainer.innerHTML = `
                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mb-2 text-sm text-gray-400">Нажмите для загрузки изображения</p>
                    <p class="text-xs text-gray-400">JPEG, PNG (макс. 2MB)</p>
                `;
            }
        });

        function validateWorkingHours(open, close, day) {
            if (!open || !close) return true;
            
            const start = new Date(`2000-01-01T${open}`);
            const end = new Date(`2000-01-01T${close}`);
            const diffMinutes = (end - start) / (1000 * 60);
            
            if (diffMinutes < 120) {
                const dayNames = {
                    'monday'    : 'Понедельник',
                    'tuesday'   : 'Вторник',
                    'wednesday' : 'Среда',
                    'thursday'  : 'Четверг',
                    'friday'    : 'Пятница',
                    'saturday'  : 'Суббота',
                    'sunday'    : 'Воскресенье',
                };
                alert(`Филиал должен работать не менее 2 часов в ${dayNames[day]}`);
                return false;
            }
            return true;
        }

        function getDayName(day) {
            const daysMap = {
                'monday': 'Понедельника',
                'tuesday': 'Вторника',
                'wednesday': 'Среды',
                'thursday': 'Четверга',
                'friday': 'Пятницы',
                'saturday': 'Субботы',
                'sunday': 'Воскресенья'
            };
            return daysMap[day];
        }


        async function checkAddressUnique(address) {
        try {
            const response = await fetch('/api/check-address', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ address })
            });
            const data = await response.json();
            return data.unique;
        } catch (error) {
            console.error('Ошибка проверки адреса:', error);
            return true;
        }
    }


    document.getElementById('address').addEventListener('input', function(e) {
        const addressPattern = /^г\.[а-яА-ЯёЁ\s-]+, ул\.[а-яА-ЯёЁ\s-]+, д\.\d+[а-яА-Я]?$/u;
        if (!addressPattern.test(this.value)) {
            this.setCustomValidity('Введите адрес в формате: "г.Город, ул.Улица, д.Номер"');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>
</body>
</html>