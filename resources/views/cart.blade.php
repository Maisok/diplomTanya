<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toothless - {{ $service->name }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #4a6b7a 0%, #3a556a 100%);
    }
    .card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.08);
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }
    .card:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }
    .service-image {
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      cursor: pointer;
    }
    .service-image:hover {
      transform: scale(1.02);
    }
    select, input {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.1) !important;
      border: 1px solid rgba(255, 255, 255, 0.2) !important;
      color: white !important;
    }
    select:focus, input:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    }
    select option {
      background: #3A556A;
    }
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .compact-table {
      font-size: 0.875rem;
    }
    .compact-table th, 
    .compact-table td {
      padding: 8px 12px;
    }
    .day-card {
      transition: all 0.2s ease;
    }
    .day-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    /* Модальное окно для изображения */
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
      overflow: auto;
    }
    .modal-content {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
    }
    .modal-img {
      max-width: 90%;
      max-height: 90%;
      object-fit: contain;
      border-radius: 8px;
    }
    .close {
      position: absolute;
      top: 20px;
      right: 30px;
      color: white;
      font-size: 35px;
      font-weight: bold;
      cursor: pointer;
    }
  </style>
</head>
<body class="flex flex-col min-h-screen text-white">
  <!-- Модальное окно для изображения -->
  <div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <div class="modal-content">
      <img id="expandedImg" class="modal-img" src="">
    </div>
  </div>

  <x-header class="flex-shrink-0"/>
  
  <div class="flex-grow py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
      <!-- Компактная карточка услуги -->
      <div class="card p-6">
        <div class="flex flex-col md:flex-row gap-6 items-center">
          <div class="md:w-1/4 flex justify-center">
            <img src="{{ asset('storage/' . $service->image) }}" 
                 alt="{{ $service->name }}"
                 class="service-image w-full max-w-xs"
                 onclick="openModal(this)">
          </div>
          
          <div class="md:w-3/4 space-y-4">
            <h1 class="text-2xl font-bold">{{ $service->name }}</h1>
            <p class="text-gray-300" style="overflow-wrap: break-word; word-wrap: break-word;">{{ $service->description }}</p>
            
            <div class="flex flex-wrap gap-4">
              <div class="bg-white/10 px-4 py-3 rounded-lg">
                <p class="text-sm text-gray-400">Стоимость</p>
                <p class="text-xl font-bold text-blue-400">{{ $service->price }} ₽</p>
              </div>
              
              @if($service->duration)
              <div class="bg-white/10 px-4 py-3 rounded-lg">
                <p class="text-sm text-gray-400">Длительность</p>
                <p class="text-lg">{{ $service->duration }} мин.</p>
              </div>
              @endif
              
              @if($service->average_rating)
              <div class="bg-white/10 px-4 py-3 rounded-lg">
                <p class="text-sm text-gray-400">Рейтинг</p>
                <div class="flex items-center gap-1">
                  @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= round($service->average_rating) ? 'text-yellow-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  @endfor
                  <span class="text-sm ml-1">{{ number_format($service->average_rating, 1) }}</span>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Группа карточек в ряд -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Форма записи -->
        <div class="card p-6 lg:col-span-1">
          <h2 class="text-xl font-bold mb-4">Записаться на прием</h2>
          
          <form action="{{ route('appointments.create', $service) }}" method="POST" class="space-y-4">
            @csrf
              
            <div>
              <label class="block text-sm text-gray-400 mb-1">Филиал</label>
              <select name="branch_id" id="branch_id" 
                      class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      required>
                <option value="" disabled selected>Выберите филиал</option>
                @foreach($branches as $branch)
                  <option value="{{ $branch->id }}">{{ $branch->address }}</option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label class="block text-sm text-gray-400 mb-1">Специалист</label>
              <select name="staff_id" id="staff_id" 
                      class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                      disabled required>
                <option value="" disabled selected>Сначала выберите филиал</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm text-gray-400 mb-1">Дата и время</label>
              <input type="datetime-local" id="appointment_time" name="appointment_time" 
                     class="w-full px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                     required>
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-lg px-4 py-2 shadow transition-all">
              Записаться
            </button>
          </form>
        </div>

        <!-- Расписание и записи в одной колонке -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Блок расписания (всегда видим, но контент появляется после выбора) -->
          <div class="card p-6">
            <h2 class="text-xl font-bold mb-4">Расписание филиала</h2>
            <div id="schedule-placeholder" class="text-gray-400 text-center py-4">
              Выберите филиал для отображения расписания
            </div>
            <div id="branch-schedule" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 hidden"></div>
          </div>

          <!-- Существующие записи -->
          <div class="card p-6">
            <h2 class="text-xl font-bold mb-4">Существующие записи</h2>
            <div class="overflow-x-auto">
              <table id="appointments-table" class="w-full compact-table">
                <thead>
                  <tr class="bg-white/10">
                    <th class="text-left">Дата и время</th>
                    <th class="text-left">Специалист</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="2" class="text-center text-gray-400 py-4">
                      Выберите филиал для отображения записей
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      @if(session('success') || session('error') || $errors->any())
      <div class="card p-4">
        @if(session('success'))
          <div class="text-green-400 text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
          </div>
        @endif

        @if(session('error'))
          <div class="text-red-400 text-sm flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
          </div>
        @endif

        @if($errors->any())
          <div class="text-red-400 text-sm">
            <div class="flex items-start">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div>
                <h3 class="font-medium">Ошибки при заполнении формы:</h3>
                <ul class="list-disc list-inside pl-4">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endif
      </div>
      @endif
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const branchSelect = document.getElementById('branch_id');
        const staffSelect = document.getElementById('staff_id');
        const timeInput = document.getElementById('appointment_time');
        const schedulePlaceholder = document.getElementById('schedule-placeholder');
        const branchSchedule = document.getElementById('branch-schedule');
        const appointmentsTableBody = document.querySelector('#appointments-table tbody');
    
        let branchScheduleData = {};
        let staffListCache = {};
    
        // Форматирование даты для datetime-local
        function formatForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    
        // Получаем график работы филиала
        async function fetchBranchSchedule(branchId) {
            try {
                const response = await fetch(`/branches/${branchId}/schedule`);
                if (!response.ok) throw new Error('Ошибка загрузки расписания');
                return await response.json();
            } catch (error) {
                console.error(error);
                return null;
            }
        }
    
        // Обновляем min/max значения инпута времени
        async function updateDateTimeConstraints() {
            const branchId = branchSelect.value;
            if (!branchId) return;
    
            const schedule = await fetchBranchSchedule(branchId);
            if (!schedule) return;
    
            branchScheduleData = schedule;
    
            const now = new Date();
            const minTime = new Date(now.getTime() + 60 * 60 * 1000); // через 1 час
            const maxTime = new Date(now);
            maxTime.setDate(maxTime.getDate() + 30); // до 30 дней
    
            timeInput.min = formatForInput(minTime);
            timeInput.max = formatForInput(maxTime);
    
            timeInput.addEventListener('change', function () {
                const selectedDate = new Date(this.value);
                const day = selectedDate.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
    
                const openTime = schedule[`${day}_open`];
                const closeTime = schedule[`${day}_close`];
    
                this.setCustomValidity(""); // <-- очистка ошибки
    
                if (!openTime || !closeTime) {
                    this.setCustomValidity("Филиал не работает в этот день");
                    return;
                }
    
                const [openHour, openMinute] = openTime.split(':');
                const [closeHour, closeMinute] = closeTime.split(':');
    
                const selectedTime = selectedDate.getHours() * 60 + selectedDate.getMinutes();
                const open = parseInt(openHour) * 60 + parseInt(openMinute);
                const close = parseInt(closeHour) * 60 + parseInt(closeMinute);
    
                if (selectedTime < open || selectedTime > close) {
                    this.setCustomValidity(`Время должно быть между ${openTime} и ${closeTime}`);
                }
            });
        }
    
        // Загрузка специалистов
        async function loadStaffList(branchId) {
            staffSelect.disabled = true;
            staffSelect.innerHTML = '<option value="" disabled selected><span class="loading"></span> Загрузка...</option>';
    
            if (!branchId) {
                staffSelect.innerHTML = '<option value="" disabled selected>Сначала выберите филиал</option>';
                return;
            }
    
            if (staffListCache[branchId]) {
                renderStaffOptions(staffListCache[branchId]);
                return;
            }
    
            try {
                const response = await fetch(`/branches/${branchId}/staff?service_id={{ $service->id }}`);
                if (!response.ok) throw new Error('Ошибка загрузки специалистов');
    
                const staffList = await response.json();
                staffListCache[branchId] = staffList;
                renderStaffOptions(staffList);
            } catch (error) {
                staffSelect.innerHTML = '<option value="" disabled>Ошибка загрузки</option>';
                console.error(error);
            }
        }
    
        function renderStaffOptions(staffList) {
            staffSelect.innerHTML = '<option value="" disabled selected>Выберите специалиста</option>';
            if (staffList.length === 0) {
                staffSelect.innerHTML += '<option value="" disabled>Нет доступных специалистов</option>';
                staffSelect.disabled = true;
                return;
            }
    
            staffList.forEach(staff => {
                const option = document.createElement('option');
                option.value = staff.id;
                option.textContent = `${staff.first_name} ${staff.last_name}`;
                staffSelect.appendChild(option);
            });
    
            staffSelect.disabled = false;
        }
    
        // Отображение графика работы филиала
        function displayBranchSchedule(schedule) {
            branchSchedule.innerHTML = '';
            const days = [
                { name: 'Пн', openKey: 'monday_open', closeKey: 'monday_close' },
                { name: 'Вт', openKey: 'tuesday_open', closeKey: 'tuesday_close' },
                { name: 'Ср', openKey: 'wednesday_open', closeKey: 'wednesday_close' },
                { name: 'Чт', openKey: 'thursday_open', closeKey: 'thursday_close' },
                { name: 'Пт', openKey: 'friday_open', closeKey: 'friday_close' },
                { name: 'Сб', openKey: 'saturday_open', closeKey: 'saturday_close' },
                { name: 'Вс', openKey: 'sunday_open', closeKey: 'sunday_close' }
            ];
    
            days.forEach(day => {
                const openTime = schedule[day.openKey];
                const closeTime = schedule[day.closeKey];
                const dayCard = document.createElement('div');
                dayCard.className = 'day-card bg-white/10 p-3 rounded-lg text-center';
                const dayTitle = document.createElement('h3');
                dayTitle.className = 'font-semibold mb-1';
                dayTitle.textContent = day.name;
    
                const scheduleInfo = document.createElement('p');
                scheduleInfo.className = 'text-sm';
    
                if (openTime && closeTime && openTime !== 'null' && closeTime !== 'null') {
                    scheduleInfo.textContent = `${formatTime(openTime)}–${formatTime(closeTime)}`;
                    scheduleInfo.classList.add('text-green-400');
                } else {
                    scheduleInfo.textContent = 'Выходной';
                    scheduleInfo.classList.add('text-red-400');
                }
    
                dayCard.appendChild(dayTitle);
                dayCard.appendChild(scheduleInfo);
                branchSchedule.appendChild(dayCard);
            });
    
            schedulePlaceholder.classList.add('hidden');
            branchSchedule.classList.remove('hidden');
        }
    
        // Загрузка записей филиала
        async function loadBranchAppointments(branchId) {
            try {
                const response = await fetch(`/services/{{ $service->id }}/appointments2?branch_id=${branchId}`);
                if (!response.ok) throw new Error('Ошибка загрузки записей');
                const appointments = await response.json();
                updateAppointmentsTable(appointments);
            } catch (error) {
                console.error('Ошибка загрузки записей:', error);
            }
        }
    
        function updateAppointmentsTable(appointments) {
            appointmentsTableBody.innerHTML = '';
            if (appointments.length === 0) {
                appointmentsTableBody.innerHTML = `
                    <tr>
                        <td colspan="2" class="text-center text-gray-400 py-4">
                            Нет активных записей
                        </td>
                    </tr>`;
                return;
            }
    
            appointments.forEach(appointment => {
                const row = document.createElement('tr');
                row.className = 'border-b border-white/10 hover:bg-white/5';
                row.innerHTML = `
                    <td>${appointment.appointment_time}</td>
                    <td>${appointment.staff?.first_name} ${appointment.staff?.last_name}</td>`;
                appointmentsTableBody.appendChild(row);
            });
        }
    
        // Форматирование времени
        function formatTime(timeString) {
            return timeString ? timeString.substring(0, 5) : timeString;
        }
    
        // Слушатель изменения филиала
        branchSelect.addEventListener('change', async function () {
            const branchId = this.value;
            if (!branchId) return;
    
            await loadStaffList(branchId);
            const schedule = await fetchBranchSchedule(branchId);
            if (schedule) {
                displayBranchSchedule(schedule);
                loadBranchAppointments(branchId);
            }
            updateDateTimeConstraints();
        });
    
        // При загрузке страницы
        @if(old('branch_id'))
        setTimeout(() => {
            branchSelect.value = '{{ old('branch_id') }}';
            const event = new Event('change');
            branchSelect.dispatchEvent(event);
        }, 100);
        @endif
    
        // Округляем старое значение времени
        @if(old('appointment_time'))
        timeInput.value = '{{ old('appointment_time') }}';
        @endif
    });
    </script>
</body>
</html>