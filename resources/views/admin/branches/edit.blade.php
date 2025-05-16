<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать филиал</title>
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
        .day-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .day-card:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .file-upload {
            border: 2px dashed rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .file-upload:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.05);
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
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-100">
                Редактирование филиала
            </h1>
            
            <!-- Вывод ошибок валидации -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-900/50 border border-red-700 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Ошибки при заполнении формы</h3>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-card p-8">
                <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data" id="branchForm">
                    @csrf
                    @method('PUT')

                    <!-- Изображение филиала -->
                    <div class="mb-8">
                        <label class="block text-gray-300 mb-3 font-medium">Изображение филиала</label>
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="flex-1">
                                <label for="image" class="file-upload flex items-center justify-center w-full h-40 rounded-lg cursor-pointer mb-4">
                                    <div class="text-center p-4" id="upload-text">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                        <p class="text-sm text-gray-400">Нажмите для загрузки нового изображения</p>
                                        <p class="text-xs text-gray-500 mt-1">JPEG, PNG (макс. 2MB)</p>
                                    </div>
                                    <input type="file" name="image" id="image" accept="image/jpeg,image/png" class="hidden">
                                </label>
                            </div>
                            
                            <div class="flex-1">
                                <p class="text-gray-300 mb-2">Текущее изображение:</p>
                                <img src="{{ asset('storage/' . $branch->image) }}" 
                                     alt="Текущее изображение филиала" 
                                     class="w-full h-40 object-cover rounded-lg border border-white/10">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Адрес филиала -->
                    <div class="mb-8">
                        <label for="address" class="block text-gray-300 mb-3 font-medium">Адрес филиала</label>
                        <input type="text" name="address" id="address" 
                               value="{{ old('address', $branch->address) }}"
                               class="w-full p-3 rounded-lg bg-white/5 border border-white/10 focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-gray-400" 
                               required 
                               placeholder="г.Москва, ул.Ленина, д.10"
                               pattern="^г\.[а-яА-ЯёЁ\s-]+, ул\.[а-яА-ЯёЁ\s-]+, д\.\d+[а-яА-Я]?$"
                               title="Формат: г.Город, ул.Улица, д.Номер"
                               maxlength="255">
                        <p class="text-gray-400 text-xs mt-1">Формат: г.Город, ул.Улица, д.Номер</p>
                    </div>
                    
                    <!-- Расписание работы -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 flex items-center text-gray-300">
                            Расписание работы
                        </h2>
                        <p class="text-gray-400 text-sm mb-6">Оставьте поля пустыми, если филиал не работает в этот день</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <div class="day-card p-4 rounded-lg">
                                    <h3 class="text-gray-300 font-semibold mb-3">
                                        {{ trans("days.$day") }}
                                    </h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="{{ $day }}_open" class="block text-gray-400 text-sm mb-2">Открытие</label>
                                            <input type="time" name="{{ $day }}_open" id="{{ $day }}_open"
                                                   value="{{ old($day.'_open', $branch->{$day.'_open'} ? Carbon\Carbon::parse($branch->{$day.'_open'})->format('H:i') : '') }}"
                                                   class="w-full time-input p-2 rounded">
                                        </div>
                                        <div>
                                            <label for="{{ $day }}_close" class="block text-gray-400 text-sm mb-2">Закрытие</label>
                                            <input type="time" name="{{ $day }}_close" id="{{ $day }}_close"
                                                   value="{{ old($day.'_close', $branch->{$day.'_close'} ? Carbon\Carbon::parse($branch->{$day.'_close'})->format('H:i') : '') }}"
                                                   class="w-full time-input p-2 rounded">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Статус</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active" {{ old('status', $branch->status ?? '') == 'active' ? 'selected' : '' }}>Активный</option>
                            <option value="inactive" {{ old('status', $branch->status ?? '') == 'inactive' ? 'selected' : '' }}>Не активный</option>
                        </select>
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
                            Изменить филиал
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Валидация формы
        // document.getElementById('branchForm').addEventListener('submit', function(e) {
        //     const addressInput = document.getElementById('address');
        //     const addressPattern = /^г\.[а-яА-ЯёЁ\s-]+, ул\.[а-яА-ЯёЁ\s-]+, д\.\d+[а-яА-Я]?$/u;
            
        //     // Проверка адреса
        //     if (!addressPattern.test(addressInput.value)) {
        //         e.preventDefault();
        //         alert('Пожалуйста, введите адрес в формате: "г.Город, ул.Улица, д.Номер"');
        //         addressInput.focus();
        //         return false;
        //     }
            
        //     // Проверка времени работы
        //     const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        //     const dayNames = {
        //         'monday': 'понедельник', 'tuesday': 'вторник', 'wednesday': 'среда',
        //         'thursday': 'четверг', 'friday': 'пятница', 'saturday': 'суббота', 
        //         'sunday': 'воскресенье'
        //     };
            
        //     for (const day of days) {
        //         const open = document.getElementById(`${day}_open`).value;
        //         const close = document.getElementById(`${day}_close`).value;
                
        //         if ((open && !close) || (!open && close)) {
        //             e.preventDefault();
        //             alert(`Для ${dayNames[day]} необходимо указать оба времени или оставить оба поля пустыми`);
        //             return false;
        //         }
                
        //         if (open && close) {
        //             const start = new Date(`2000-01-01T${open}`);
        //             const end = new Date(`2000-01-01T${close}`);
                    
        //             if (start >= end) {
        //                 e.preventDefault();
        //                 alert(`В ${dayNames[day]} время закрытия должно быть позже времени открытия`);
        //                 return false;
        //             }
                    
        //             const diffMinutes = (end - start) / (1000 * 60);
        //             if (diffMinutes < 120) {
        //                 e.preventDefault();
        //                 alert(`В ${dayNames[day]} филиал должен работать не менее 2 часов`);
        //                 return false;
        //             }
        //         }
        //     }
            
        //     return true;
        // });

        // Обработка загрузки изображения
        const fileUpload = document.querySelector('.file-upload');
        const fileInput = document.getElementById('image');
        const uploadText = document.getElementById('upload-text');
        
        fileUpload.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(file.type)) {
                    alert('Пожалуйста, загрузите изображение в формате JPEG или PNG');
                    this.value = '';
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('Размер изображения не должен превышать 2MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    uploadText.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg" alt="Превью">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 hover:opacity-100 transition">
                            <span class="text-white">Изменить изображение</span>
                        </div>
                    `;
                    fileUpload.classList.add('relative');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>