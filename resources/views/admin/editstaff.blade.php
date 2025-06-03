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
        .current-photo {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.5rem;
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
            
            <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="editStaffForm">
                @csrf
                @method('PUT')
                
                <!-- Имя -->
                <div>
                    <label for="name" class="block text-gray-300 mb-3 font-medium">Имя*</label>
                    <input type="text" name="name" id="name" maxlength="50" 
                           value="{{ old('name', $staff->name) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('name') input-error @enderror"
                           placeholder="Максимум 50 символов">
                    <p class="error-message hidden" id="nameError"></p>
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Фамилия -->
                <div>
                    <label for="surname" class="block text-gray-300 mb-3 font-medium">Фамилия*</label>
                    <input type="text" name="surname" id="surname" maxlength="50" 
                           value="{{ old('surname', $staff->surname) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('surname') input-error @enderror"
                           placeholder="Максимум 50 символов">
                    <p class="error-message hidden" id="surnameError"></p>
                    @error('surname')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Телефон -->
                <div>
                    <label for="phone" class="block text-gray-300 mb-3 font-medium">Телефон*</label>
                    <input type="text" name="phone" id="phone" maxlength="15" 
                            value="{{ old('phone', $staff->formatted_phone) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('phone') input-error @enderror"
                           placeholder="8 999 999 99 99">
                    <p class="error-message hidden" id="phoneError"></p>
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="email" class="block text-gray-300 mb-3 font-medium">Почта*</label>
                    <input type="text" name="email" id="email" maxlength="100" 
                            value="{{ old('email', $staff->email) }}" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('email') input-error @enderror"
                           placeholder="example@mail.com">
                    <p class="error-message hidden" id="phoneError"></p>
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Статус -->
                <div>
                    <label for="status" class="block text-gray-300 mb-3 font-medium">Статус*</label>
                    <select name="status" id="status" class="w-full input-field p-3 rounded-lg focus:outline-none">
                        <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Работает</option>
                        <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Не работает</option>
                    </select>
                </div>
            
                <!-- Фото -->
                <div>
                    <label for="image" class="block text-gray-300 mb-3 font-medium">Фото</label>
                    @if($staff->image)
                        <div class="mb-4 current-photo p-2 inline-block">
                            <img src="{{ asset('storage/' . $staff->image) }}" alt="Текущее фото" class="h-32 rounded-md">
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
                    <p class="error-message hidden" id="imageError"></p>
                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Филиал -->
                <div>
                    <label for="branch_id" class="block text-gray-300 mb-3 font-medium">Филиал</label>
                    <select name="branch_id" id="branch_id" required
                            class="w-full input-field p-3 rounded-lg focus:outline-none @error('branch_id') input-error @enderror">
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
            
                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-gray-300 mb-3 font-medium">Новый пароль</label>
                    <input type="password" name="password" id="password"
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('password') input-error @enderror"
                           placeholder="Оставьте пустым, чтобы не изменять">
                    <p class="error-message hidden" id="passwordError"></p>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.staff.index') }}" class="btn-secondary px-6 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Отмена
                    </a>
                    <button type="submit" class="btn-primary px-6 py-3 rounded-lg flex items-center">
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editStaffForm');
            const lastNameInput = document.getElementById('last_name');
            const firstNameInput = document.getElementById('first_name');
            const middleNameInput = document.getElementById('middle_name');
            const phoneInput = document.getElementById('phone');
            const passwordInput = document.getElementById('password');
            const imageInput = document.getElementById('image');

            // Функция для форматирования имени (первая буква заглавная)
            function formatName(name) {
                return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
            }

            // Валидация фамилии
            lastNameInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, '');
                if (this.value.length > 50) {
                    this.value = this.value.substring(0, 50);
                }
            });

            // Валидация имени
            firstNameInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, '');
                if (this.value.length > 50) {
                    this.value = this.value.substring(0, 50);
                }
            });

            // Валидация отчества
            middleNameInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, '');
                if (this.value.length > 50) {
                    this.value = this.value.substring(0, 50);
                }
            });

            // Форматирование телефона (8 XXX XXX XX XX)
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

            // Валидация пароля (если введен)
            passwordInput.addEventListener('blur', function() {
                if (this.value && this.value.length < 8) {
                    document.getElementById('passwordError').textContent = 'Пароль должен содержать минимум 8 символов';
                    document.getElementById('passwordError').classList.remove('hidden');
                    this.classList.add('input-error');
                } else {
                    document.getElementById('passwordError').classList.add('hidden');
                    this.classList.remove('input-error');
                }
            });

            // Валидация изображения
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    const maxSize = 2 * 1024 * 1024; // 2MB

                    if (!validTypes.includes(file.type)) {
                        document.getElementById('imageError').textContent = 'Допустимые форматы: jpeg, png, jpg, gif';
                        document.getElementById('imageError').classList.remove('hidden');
                        this.classList.add('input-error');
                        this.value = '';
                    } else if (file.size > maxSize) {
                        document.getElementById('imageError').textContent = 'Максимальный размер изображения 2MB';
                        document.getElementById('imageError').classList.remove('hidden');
                        this.classList.add('input-error');
                        this.value = '';
                    } else {
                        document.getElementById('imageError').classList.add('hidden');
                        this.classList.remove('input-error');
                    }
                }
            });

            // Валидация формы перед отправкой
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Проверка фамилии
                if (!lastNameInput.value.trim()) {
                    document.getElementById('lastNameError').textContent = 'Поле "Фамилия" обязательно для заполнения';
                    document.getElementById('lastNameError').classList.remove('hidden');
                    lastNameInput.classList.add('input-error');
                    isValid = false;
                } else if (!/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/.test(lastNameInput.value)) {
                    document.getElementById('lastNameError').textContent = 'Фамилия должна содержать только буквы';
                    document.getElementById('lastNameError').classList.remove('hidden');
                    lastNameInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('lastNameError').classList.add('hidden');
                    lastNameInput.classList.remove('input-error');
                }

                // Проверка имени
                if (!firstNameInput.value.trim()) {
                    document.getElementById('firstNameError').textContent = 'Поле "Имя" обязательно для заполнения';
                    document.getElementById('firstNameError').classList.remove('hidden');
                    firstNameInput.classList.add('input-error');
                    isValid = false;
                } else if (!/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/.test(firstNameInput.value)) {
                    document.getElementById('firstNameError').textContent = 'Имя должно содержать только буквы';
                    document.getElementById('firstNameError').classList.remove('hidden');
                    firstNameInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('firstNameError').classList.add('hidden');
                    firstNameInput.classList.remove('input-error');
                }

                // Проверка отчества (необязательное поле)
                if (middleNameInput.value.trim() && !/^[a-zA-Zа-яА-ЯёЁ\s\-]*$/.test(middleNameInput.value)) {
                    document.getElementById('middleNameError').textContent = 'Отчество должно содержать только буквы';
                    document.getElementById('middleNameError').classList.remove('hidden');
                    middleNameInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('middleNameError').classList.add('hidden');
                    middleNameInput.classList.remove('input-error');
                }

                // Проверка телефона
                const phoneRegex = /^8 \d{3} \d{3} \d{2} \d{2}$/;
                if (!phoneInput.value.trim()) {
                    document.getElementById('phoneError').textContent = 'Поле "Телефон" обязательно для заполнения';
                    document.getElementById('phoneError').classList.remove('hidden');
                    phoneInput.classList.add('input-error');
                    isValid = false;
                } else if (!phoneRegex.test(phoneInput.value)) {
                    document.getElementById('phoneError').textContent = 'Телефон должен быть в формате: 8 999 999 99 99';
                    document.getElementById('phoneError').classList.remove('hidden');
                    phoneInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('phoneError').classList.add('hidden');
                    phoneInput.classList.remove('input-error');
                }

                // Проверка пароля (если введен)
                if (passwordInput.value && passwordInput.value.length < 8) {
                    document.getElementById('passwordError').textContent = 'Пароль должен содержать минимум 8 символов';
                    document.getElementById('passwordError').classList.remove('hidden');
                    passwordInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('passwordError').classList.add('hidden');
                    passwordInput.classList.remove('input-error');
                }

                if (!isValid) {
                    e.preventDefault();
                    // Прокрутка к первой ошибке
                    const firstError = document.querySelector('.error-message:not(.hidden)');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
</body>
</html>