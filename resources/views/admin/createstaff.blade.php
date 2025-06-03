<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить сотрудника</title>
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
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="max-w-md mx-auto form-card p-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Добавить сотрудника
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
            
            <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="staffForm">
                @csrf
            
                <!-- Имя -->
                <div>
                    <label for="name" class="block text-gray-300 mb-3 font-medium">Имя*</label>
                    <input type="text" name="name" id="name" maxlength="50" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('name') input-error @enderror"
                           placeholder="Максимум 50 символов"
                           value="{{ old('name') }}">
                    <p class="error-message hidden" id="nameError"></p>
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Фамилия -->
                <div>
                    <label for="surname" class="block text-gray-300 mb-3 font-medium">Фамилия</label>
                    <input type="text" name="surname" id="surname" maxlength="50"
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('surname') input-error @enderror"
                           placeholder="Максимум 50 символов" required
                           value="{{ old('surname') }}">
                    <p class="error-message hidden" id="surnameError"></p>
                    @error('surname')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Телефон -->
                <div>
                    <label for="phone" class="block text-gray-300 mb-3 font-medium">Телефон*</label>
                    <input type="text" name="phone" id="phone" maxlength="15" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('phone') input-error @enderror"
                           placeholder="8 999 999 99 99"
                           value="{{ old('phone') }}">
                    <p class="error-message hidden" id="phoneError"></p>
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-300 mb-2">Филиал</label>
                    <select name="branch_id" id="branch_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->address }}</option>
                        @endforeach
                    </select>
                </div>
            
                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-300 mb-3 font-medium">Email</label>
                    <input type="email" name="email" id="email" maxlength="100"
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('email') input-error @enderror"
                           placeholder="example@example.com" required
                           value="{{ old('email') }}">
                    <p class="error-message hidden" id="emailError"></p>
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-gray-300 mb-3 font-medium">Пароль*</label>
                    <input type="password" name="password" id="password" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('password') input-error @enderror"
                           placeholder="Минимум 8 символов">
                    <p class="error-message hidden" id="passwordError"></p>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Статус -->
                <div>
                    <label for="status" class="block text-gray-300 mb-3 font-medium">Статус</label>
                    <select name="status" id="status" class="form-control w-full p-3 rounded-lg">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Работает</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Не работает</option>
                    </select>
                </div>
            
                <!-- Фото -->
                <div>
                    <label for="image" class="block text-gray-300 mb-3 font-medium">Фото</label>
                    <input type="file" name="image" id="image" required
                           class="w-full input-field p-3 rounded-lg focus:outline-none @error('image') input-error @enderror"
                           accept="image/jpeg,image/png,image/jpg,image/gif">
                    <p class="error-message hidden" id="imageError"></p>
                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="{{ route('admin.staff.index') }}" class="btn-secondary px-6 py-3 rounded-lg">
                        Отмена
                    </a>
                    <button type="submit" class="btn-primary px-6 py-3 rounded-lg">
                        Добавить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('staffForm');
            const nameInput = document.getElementById('name');
            const surnameInput = document.getElementById('surname');
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const imageInput = document.getElementById('image');

            // Функция для форматирования имени (первая буква заглавная)
            function formatName(name) {
                return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
            }

            // Валидация имени
            nameInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, '');
                if (this.value.length > 50) {
                    this.value = this.value.substring(0, 50);
                }
            });

            // Валидация фамилии
            surnameInput.addEventListener('input', function() {
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

            // Валидация email
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    document.getElementById('emailError').textContent = 'Введите корректный email';
                    document.getElementById('emailError').classList.remove('hidden');
                    this.classList.add('input-error');
                } else {
                    document.getElementById('emailError').classList.add('hidden');
                    this.classList.remove('input-error');
                }
            });

            // Валидация пароля
            passwordInput.addEventListener('blur', function() {
                if (this.value.length < 8) {
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

                // Проверка имени
                if (!nameInput.value.trim()) {
                    document.getElementById('nameError').textContent = 'Поле "Имя" обязательно для заполнения';
                    document.getElementById('nameError').classList.remove('hidden');
                    nameInput.classList.add('input-error');
                    isValid = false;
                } else if (nameInput.value.length > 50) {
                    document.getElementById('nameError').textContent = 'Имя не должно превышать 50 символов';
                    document.getElementById('nameError').classList.remove('hidden');
                    nameInput.classList.add('input-error');
                    isValid = false;
                } else {
                    document.getElementById('nameError').classList.add('hidden');
                    nameInput.classList.remove('input-error');
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

                // Проверка пароля
                if (!passwordInput.value.trim()) {
                    document.getElementById('passwordError').textContent = 'Поле "Пароль" обязательно для заполнения';
                    document.getElementById('passwordError').classList.remove('hidden');
                    passwordInput.classList.add('input-error');
                    isValid = false;
                } else if (passwordInput.value.length < 8) {
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