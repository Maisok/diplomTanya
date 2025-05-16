<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentConfirmation;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    


    public function show(Service $service)
    {
        $service->load(['branches' => function($query) use ($service) {
            $query->whereHas('staff.services', function($q) use ($service) {
                $q->where('services.id', $service->id);
            });
        }]);
        
        return view('services.show', compact('service'));
    }

    public function getBranchWorkingHours($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        return response()->json([
            'monday_open' => $branch->monday_open,
            'monday_close' => $branch->monday_close,
            'tuesday_open' => $branch->tuesday_open,
            'tuesday_close' => $branch->tuesday_close,
            'wednesday_open' => $branch->wednesday_open,
            'wednesday_close' => $branch->wednesday_close,
            'thursday_open' => $branch->thursday_open,
            'thursday_close' => $branch->thursday_close,
            'friday_open' => $branch->friday_open,
            'friday_close' => $branch->friday_close,
            'saturday_open' => $branch->saturday_open,
            'saturday_close' => $branch->saturday_close,
            'sunday_open' => $branch->sunday_open,
            'sunday_close' => $branch->sunday_close,
        ]);
    }

    public function create(Request $request, Service $service)
    {
        try {
            // Проверка подтверждения email или входа через Яндекс
            $user = Auth::user();
            if (!$user->email_verified_at && !$user->yandex_id) {
                return redirect()->back()
                    ->with('error', 'Для записи на услугу необходимо подтвердить email в личном кабинете')
                    ->withInput();
            }

            $validated = $this->validateAppointmentRequest($request);
            $staff = Staff::findOrFail($validated['staff_id']);
            $branch = Branch::findOrFail($validated['branch_id']);
            $appointmentTime = Carbon::parse($validated['appointment_time']);
            $duration = abs($service->duration);
            $endTime = $appointmentTime->copy()->addMinutes($duration);
            $bufferMinutes = 10;

            // Основные проверки
            $this->validateStaff($staff, $branch, $service);
            $this->validateAppointmentTime($appointmentTime);
            $this->validateUserDailyAppointments($user->id, $appointmentTime);
            
            // Проверки времени работы и доступности
            $this->validateBranchWorkingHours($branch, $appointmentTime, $endTime);
            
            // Проверка доступности мастера
            $this->validateStaffAvailability($staff, $appointmentTime, $endTime);
            
            // Проверка что у пользователя нет других записей на это время
            $this->validateUserAppointments($user->id, $appointmentTime, $endTime);

            // Создание записи
            $appointment = $this->createAppointment($service, $user->id, $staff, $branch, $appointmentTime);
            $this->sendConfirmationEmail($appointment, $service, $staff, $branch, $duration);

            return redirect()->back()
                ->with('success', 'Запись успешно создана! На вашу почту отправлено подтверждение.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function rate(Appointment $appointment, Request $request)
    {
        
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5'
        ]);

        $appointment->update(['rating' => $validated['rating']]);

        return back()->with('success', 'Спасибо за вашу оценку!');
    }

    /**
     * Валидация данных записи
     */
    protected function validateAppointmentRequest(Request $request): array
    {
        return $request->validate([
            'appointment_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $selectedDateTime = new \DateTime($value);
                    $now = new \DateTime();
                    $maxAllowedDate = (new \DateTime())->modify('+1 month');
                    
                    if ($selectedDateTime <= $now) {
                        $fail('Запись возможна только на будущее время.');
                    }
                    
                    if ($selectedDateTime > $maxAllowedDate) {
                        $fail('Запись возможна только на период до 1 месяца вперед.');
                    }
                },
            ],
            'staff_id' => [
                'required',
                'exists:staff,id',
                function ($attribute, $value, $fail) use ($request) {
                    $branchId = $request->input('branch_id');
                    $staff = Staff::find($value);
                    
                    if ($staff && $staff->branch_id != $branchId) {
                        $fail('Выбранный специалист не работает в этом филиале.');
                    }
                },
            ],
            'branch_id' => 'required|exists:branches,id',
        ], [
            'appointment_time.required' => 'Пожалуйста, выберите время записи.',
            'staff_id.required' => 'Пожалуйста, выберите специалиста.',
            'branch_id.required' => 'Пожалуйста, выберите филиал.',
            'staff_id.exists' => 'Выбранный специалист не существует.',
            'branch_id.exists' => 'Выбранный филиал не существует.',
        ]);
    }

    /**
     * Проверка специалиста
     */
    protected function validateStaff(Staff $staff, Branch $branch, Service $service): void
    {
        if ($staff->branch_id != $branch->id) {
            throw new \Exception('Этот специалист не работает в выбранном филиале');
        }

        if (!$staff->services()->where('services.id', $service->id)->exists()) {
            throw new \Exception('Этот специалист не оказывает выбранную услугу');
        }
    }


    public function checkStaffAvailability($staffId, Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'buffer' => 'required|integer|min:0'
        ]);
    
        $startTime = Carbon::parse($request->date);
        $endTime = $startTime->copy()->addMinutes($request->duration);
        $bufferStart = $startTime->copy()->subMinutes($request->buffer);
        $bufferEnd = $endTime->copy()->addMinutes($request->buffer);
        
        $staff = Staff::findOrFail($staffId);
    
        // Проверка занятости специалиста с учетом буфера
        $isAvailable = !Appointment::where('staff_id', $staffId)
            ->where(function($query) use ($bufferStart, $bufferEnd) {
                $query->where(function($q) use ($bufferStart, $bufferEnd) {
                    $q->where('appointment_time', '<', $bufferEnd)
                      ->whereRaw("DATE_ADD(appointment_time, INTERVAL (SELECT duration FROM services WHERE id = service_id) MINUTE) > ?", [$bufferStart]);
                });
            })
            ->whereIn('status', ['active', 'confirmed'])
            ->exists();
    
        return response()->json($isAvailable);
    }

    protected function validateBranchWorkingHours(Branch $branch, Carbon $startTime, Carbon $endTime): void
    {
        $dayOfWeek = strtolower($startTime->englishDayOfWeek);
        $openTimeStr = $branch->{"{$dayOfWeek}_open"};
        $closeTimeStr = $branch->{"{$dayOfWeek}_close"};
    
        if (!$openTimeStr || !$closeTimeStr) {
            throw new \Exception('Филиал не работает в выбранный день');
        }
    
        $openTime = $startTime->copy()->setTimeFromTimeString($openTimeStr);
        $closeTime = $startTime->copy()->setTimeFromTimeString($closeTimeStr);
    
        // Проверка, что запись не до открытия
        if ($startTime->lt($openTime)) {
            throw new \Exception('Филиал открывается в '.$openTime->format('H:i').'. Первая возможная запись: '.$openTime->format('H:i'));
        }
    
        // Проверка, что запись не после закрытия
        $lastPossibleStart = $closeTime->copy()->addMinutes($endTime->diffInMinutes($startTime));
        
        if ($startTime->gt($lastPossibleStart)) {
            throw new \Exception('Филиал закрывается в '.$closeTime->format('H:i').'. Последняя возможная запись: '.$lastPossibleStart->format('H:i'));
        }
    }
    

    protected function validateAppointmentTime(Carbon $appointmentTime): void
    {
        if ($appointmentTime->isPast()) {
            throw new \Exception('Нельзя записаться на прошедшее время');
        }

        if ($appointmentTime->gt(now()->addMonths(3))) {
            throw new \Exception('Максимальный срок записи - 3 месяца');
        }
    }


    protected function validateUserDailyAppointments(int $userId, Carbon $appointmentTime): void
    {
        $appointmentsCount = Appointment::where('user_id', $userId)
            ->whereDate('appointment_time', $appointmentTime->toDateString())
            ->count();

        if ($appointmentsCount >= 2) {
            throw new \Exception('Вы можете иметь не более 2 записей в день');
        }
    }

    protected function validateStaffAvailability(Staff $staff, Carbon $startTime, Carbon $endTime): void
    {
        $conflictingAppointments = Appointment::where('staff_id', $staff->id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('appointment_time', '<', $endTime)
                      ->whereRaw("DATE_ADD(appointment_time, INTERVAL (SELECT duration FROM services WHERE id = service_id) MINUTE) > ?", [$startTime]);
                });
            })
            ->whereIn('status', ['active', 'confirmed'])
            ->exists();
    
        if ($conflictingAppointments) {
            throw new \Exception('Этот специалист уже занят на выбранное время');
        }
    }

    /**
     * Проверка что у пользователя нет других записей на это время
     */
    protected function validateUserAppointments(int $userId, Carbon $startTime, Carbon $endTime): void
    {
        $conflictingAppointments = Appointment::where('user_id', $userId)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('appointment_time', '<', $endTime)
                      ->whereRaw("DATE_ADD(appointment_time, INTERVAL (SELECT duration FROM services WHERE id = service_id) MINUTE) > ?", [$startTime]);
                });
            })
            ->exists();
    
        if ($conflictingAppointments) {
            throw new \Exception('У вас уже есть запись на это время');
        }
    }

    public function cancel(Appointment $appointment)
    {
        // Проверяем, что запись принадлежит текущему пользователю
        if ($appointment->user_id != auth()->id()) {
            return back()->with('error', 'Вы не можете отменить эту запись');
        }

        // Проверяем, что до записи осталось больше 1 часа
        if (now()->diffInHours($appointment->appointment_time, false) < 1) {
            return back()->with('error', 'Отмена возможна не позднее чем за 1 час до записи');
        }

        // Обновляем статус записи
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Запись успешно отменена');
    }
    

    protected function validateUserTimeSlot(int $userId, Carbon $startTime, Carbon $endTime): void
    {
        $conflictingAppointments = Appointment::where('user_id', $userId)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('appointment_time', '<', $endTime)
                      ->whereRaw("DATE_ADD(appointment_time, INTERVAL (SELECT duration FROM services WHERE id = service_id) MINUTE) > ?", [$startTime]);
                });
            })
            ->exists();

        if ($conflictingAppointments) {
            throw new \Exception('У вас уже есть запись на это время к другому специалисту');
        }
    }
    /**
     * Создание записи (без сохранения end_time)
     */
    protected function createAppointment(
        Service $service,
        int $userId,
        Staff $staff,
        Branch $branch,
        Carbon $appointmentTime
    ): Appointment {
        return Appointment::create([
            'service_id' => $service->id,
            'user_id' => $userId,
            'staff_id' => $staff->id,
            'branch_id' => $branch->id,
            'appointment_time' => $appointmentTime,
            'status' => 'active',
        ]);
    }

    /**
     * Отправка email подтверждения
     */
    protected function sendConfirmationEmail(
        Appointment $appointment,
        Service $service,
        Staff $staff,
        Branch $branch,
        int $duration
    ): void {
        if (Auth::user()->email) {
            $endTime = $appointment->appointment_time->copy()->addMinutes($duration);
            Mail::to(Auth::user()->email)->send(
                new AppointmentConfirmation($appointment, $service, $staff, $branch, $duration, $endTime)
            );
        }
    }
}