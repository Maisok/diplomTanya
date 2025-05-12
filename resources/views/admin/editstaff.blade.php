<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать сотрудника</title>
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
        .input-field {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .file-upload {
            background: rgba(255, 255, 255, 0.05);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .file-upload:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.08);
        }
        select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        .current-photo {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        /* Стили для ошибок */
        .error-message {
            color: #F87171;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .input-error {
            border-color: #F87171 !important;
        }
        /* Стили для выпадающего списка */
        select option {
            background-color: #2D3748;
            color: white;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="max-w-md mx-auto form-card p-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Редактировать сотрудника
            </h1>
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500 rounded-lg">
                    <h3 class="text-red-400 font-bold mb-2">Ошибки при заполнении формы:</h3>
                    <ul class="list-disc list-inside text-red-400 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500 rounded-lg">
                    <p class="text-green-400">{{ session('success') }}</p>
                </div>
            @endif
            
            <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateForm()">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="last_name" class="block text-gray-300 mb-3 font-medium">Фамилия*</label>
                    <input type="text" name="last_name" id="last_name" maxlength="50" 
                           value="{{ old('last_name', $staff->last_name) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('last_name') input-error @enderror"
                           placeholder="Максимум 50 символов">
                    @error('last_name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-2">Максимум 50 символов</p>
                </div>
                
                <div>
                    <label for="first_name" class="block text-gray-300 mb-3 font-medium">Имя*</label>
                    <input type="text" name="first_name" id="first_name" maxlength="50" 
                           value="{{ old('first_name', $staff->first_name) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('first_name') input-error @enderror"
                           placeholder="Максимум 50 символов">
                    @error('first_name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-2">Максимум 50 символов</p>
                </div>

                <div>
                    <label for="middle_name" class="block text-gray-300 mb-3 font-medium">Отчество</label>
                    <input type="text" name="middle_name" id="middle_name" maxlength="50" 
                           value="{{ old('middle_name', $staff->middle_name) }}"
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('middle_name') input-error @enderror"
                           placeholder="Максимум 50 символов">
                    @error('middle_name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-2">Максимум 50 символов</p>
                </div>

                <div>
                    <label for="phone" class="block text-gray-300 mb-3 font-medium">Телефон*</label>
                    <input type="text" name="phone" id="phone" maxlength="15" 
                           value="{{ old('phone', $staff->phone) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('phone') input-error @enderror"
                           placeholder="8 999 999 99 99">
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-2">Формат: 8 999 999 99 99</p>
                </div>

                <div>
                    <label for="image" class="block text-gray-300 mb-3 font-medium">Фото</label>
                    @if($staff->image)
                        <div class="mb-4 current-photo p-2 inline-block">
                            <img src="{{ asset('storage/' . $staff->image) }}" alt="Текущее фото" class="h-32 rounded-md">
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="remove_image" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-400">Удалить текущее фото</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    <div class="file-upload relative rounded-lg p-6 text-center @error('image') input-error @enderror">
                        <input type="file" name="image" id="image" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               accept="image/jpeg,image/png,image/jpg,image/gif">
                        <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-400">Перетащите новое фото или нажмите для загрузки</p>
                        <p class="mt-1 text-xs text-gray-500">Форматы: JPEG, PNG, JPG, GIF. Максимум 2MB</p>
                    </div>
                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="branch_id" class="block text-gray-300 mb-3 font-medium">Филиал</label>
                    <select name="branch_id" id="branch_id"
                            class="w-full input-field p-3 rounded-lg focus:outline-none @error('branch_id') input-error @enderror">
                        <option value="">-- Выберите филиал --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" 
                                {{ old('branch_id', $staff->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-gray-300 mb-3 font-medium">Новый пароль</label>
                    <input type="password" name="password" id="password"
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('password') input-error @enderror"
                           placeholder="Оставьте пустым, чтобы не изменять">
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-400 text-xs mt-2">Минимум 8 символов</p>
                </div>

                <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-4">
                    <a href="{{ route('admin.staff.index') }}" 
                       class="btn-secondary text-white px-6 py-3 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Отмена
                    </a>
                    <button type="submit" 
                            class="btn-primary text-white px-6 py-3 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Обновить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function validateForm() {
            const lastName = document.getElementById('last_name').value;
            const firstName = document.getElementById('first_name').value;
            const middleName = document.getElementById('middle_name').value;
            const phone = document.getElementById('phone').value;

            // Проверка фамилии
            if (!/^[a-zA-Zа-яА-Я\s]+$/.test(lastName)) {
                alert('Фамилия должна содержать только буквы и пробелы');
                return false;
            }

            // Проверка имени
            if (!/^[a-zA-Zа-яА-Я\s]+$/.test(firstName)) {
                alert('Имя должно содержать только буквы и пробелы');
                return false;
            }

            // Проверка отчества
            if (middleName && !/^[a-zA-Zа-яА-Я\s]*$/.test(middleName)) {
                alert('Отчество должно содержать только буквы и пробелы');
                return false;
            }

            // Проверка телефона
            if (!/^8 \d{3} \d{3} \d{2} \d{2}$/.test(phone)) {
                alert('Телефон должен быть в формате: 8 999 999 99 99');
                return false;
            }

            return true;
        }

        // Обработчики ввода
        document.getElementById('last_name').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Zа-яА-Я\s]/g, '');
            if (this.value.length > 50) {
                this.value = this.value.substring(0, 50);
            }
            this.value = capitalizeFirstLetter(this.value);
        });

        document.getElementById('first_name').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Zа-яА-Я\s]/g, '');
            if (this.value.length > 50) {
                this.value = this.value.substring(0, 50);
            }
            this.value = capitalizeFirstLetter(this.value);
        });

        document.getElementById('middle_name').addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-Zа-яА-Я\s]/g, '');
            if (this.value.length > 50) {
                this.value = this.value.substring(0, 50);
            }
            this.value = capitalizeFirstLetter(this.value);
        });

        document.getElementById('phone').addEventListener('input', function(e) {
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
    </script>
</body>
</html>