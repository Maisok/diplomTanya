<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать услугу</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
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
        .select2-container--default .select2-selection--multiple {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        .select2-container--default .select2-selection--single {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3B82F6 !important;
        }

        .select2-results__option {
    background-color: #4a6b7a !important;
    color: #E5E7EB !important;
}
    </style>
</head>
<body class="flex flex-col min-h-screen text-white">
    <x-header class="flex-shrink-0"/>
    
    <div class="container mx-auto px-4 py-8 flex-grow">
        <div class="max-w-2xl mx-auto form-card p-8">
            <h1 class="text-2xl md:text-3xl font-bold mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Редактировать услугу
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
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500 rounded-lg">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
        @endif
            
        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
    
            <!-- Название -->
            <div>
                <label for="name" class="block text-gray-300 mb-3 font-medium">Название услуги*</label>
                <input type="text" name="name" id="name" maxlength="100"
                       value="{{ old('name', $service->name) }}" required
                       class="w-full input-field p-3 rounded-lg focus:outline-none">
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Описание -->
            <div>
                <label for="description" class="block text-gray-300 mb-3 font-medium">Описание*</label>
                <textarea name="description" id="description" maxlength="500" required
                          class="w-full input-field p-3 rounded-lg focus:outline-none h-32">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Цена -->
            <div>
                <label for="price" class="block text-gray-300 mb-3 font-medium">Цена*</label>
                <input type="number" name="price" id="price" step="0.01" min="0" max="99999.99" required
                       value="{{ old('price', $service->price) }}"
                       class="w-full input-field p-3 rounded-lg focus:outline-none">
                @error('price')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Категория -->
            <div>
                <label for="category_id" class="block text-gray-300 mb-3 font-medium">Категория*</label>
                <select name="category_id" id="category_id" required class="w-full input-field p-3 rounded-lg focus:outline-none">
                    <option value="">Выберите категорию</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Персонал -->
            <div>
                <label for="staff_id" class="block text-gray-300 mb-3 font-medium">Персонал*</label>
                <select name="staff_id[]" id="staff_id" multiple required class="w-full input-field p-3 rounded-lg focus:outline-none">
                    @foreach($staff as $member)
                        <option value="{{ $member->id }}" {{ in_array($member->id, $selectedStaff ?? []) ? 'selected' : '' }}>
                            {{ $member->surname }} {{ $member->name }}
                        </option>
                    @endforeach
                </select>
                @error('staff_id')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Продолжительность -->
            <div>
                <label for="duration" class="block text-gray-300 mb-3 font-medium">Продолжительность (минут)*</label>
                <input type="number" name="duration" id="duration" min="5" max="300" required
                       value="{{ old('duration', $service->duration) }}"
                       class="w-full input-field p-3 rounded-lg focus:outline-none">
                @error('duration')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Статус -->
            <div>
                <label for="status" class="block text-gray-300 mb-3 font-medium">Статус*</label>
                <select name="status" id="status" required class="w-full input-field p-3 rounded-lg focus:outline-none bg-[#4a6b7a]">
                    <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Активна</option>
                    <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Не активна</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Фото -->
            <div>
                <label for="image" class="block text-gray-300 mb-3 font-medium">Фото</label>
                <div class="relative border-2 border-dashed border-white/30 rounded-lg p-6 text-center hover:border-white/50 transition">
                    <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1116.138 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-400">Перетащите изображение или нажмите для загрузки</p>
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, JPG, GIF. Максимум 2MB</p>
                </div>
                @if($service->image)
                    <div class="mt-4">
                        <p class="text-gray-400 mb-2">Текущее изображение:</p>
                        <img src="{{ asset('storage/' . $service->image) }}" alt="Фото услуги"
                             class="w-full h-40 object-cover rounded-lg border border-white/10">
                    </div>
                @endif
                @error('image')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
    
            <!-- Кнопки -->
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.services.index') }}" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Отмена
                </a>
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-300 shadow-md flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"></path>
                    </svg>
                    Сохранить изменения
                </button>
            </div>
        </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#staff_id').select2({
                placeholder: "Выберите персонал",
                allowClear: true,
                width: '100%',
                theme: 'default'
            });
            
            $('#category_id').select2({
                placeholder: "Выберите категорию",
                allowClear: true,
                width: '100%',
                theme: 'default'
            });

            // Ограничение ввода для названия
            $('#name').on('input', function() {
                if (this.value.length > 100) {
                    this.value = this.value.substring(0, 100);
                }
            });

            // Ограничение ввода для описания
            $('#description').on('input', function() {
                if (this.value.length > 500) {
                    this.value = this.value.substring(0, 500);
                }
            });

            // Ограничение ввода для цены
            $('#price').on('input', function() {
                if (this.value > 99999.99) {
                    this.value = 99999.99;
                }
            });

            // Ограничение ввода для продолжительности
            $('#duration').on('input', function() {
                if (this.value < 5) this.value = 5;
                if (this.value > 300) this.value = 300;
            });
        });
    </script>
</body>
</html>