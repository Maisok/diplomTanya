<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentConfirmation;

class AppointmentController extends Controller
{
    /**
     * Создание записи
     */
    public function create(Request $request, Service $service)
    {
        try {
            // Получаем данные формы
            $validated = $this->validateRequest($request);
    
            // Получаем пользователя, мастера, филиал, время
            $user = auth()->user();
            $staff = User::findOrFail($validated['staff_id']);
            $branch = Branch::findOrFail($validated['branch_id']);
            $appointmentTime = Carbon::createFromFormat('Y-m-d\TH:i', $validated['appointment_time']);
            $duration = $service->duration;
            $buffer = 10;
    
            // Проверяем график работы филиала и доступность времени
            $this->validateBranchWorkingHours($branch, $appointmentTime, $duration, $buffer);
            
            // Проверяем, что сотрудник принадлежит филиалу
            if ($staff->branch_id !== $branch->id) {
                throw new \Exception('Этот специалист не работает в этом филиале');
            }
    
            // Проверяем, что мастер свободен
            $this->validateStaffAvailability($staff, $appointmentTime, $duration, $buffer);
    
            // Проверяем, что пользователь не делает более 3 записей в день
            $this->validateUserDailyAppointments($user->id, $appointmentTime);
    
            // Создаём запись
            $appointment = Appointment::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'staff_id' => $staff->id,
                'branch_id' => $branch->id,
                'appointment_time' => $appointmentTime,
                'status' => 'active'
            ]);
    
            // Отправляем подтверждение на почту клиенту
            Mail::to($user->email)->send(
                new AppointmentConfirmation($appointment, $service, $staff, $branch)
            );
    
            return redirect()->back()
                ->with('success', 'Запись успешно создана!');
    
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
    
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Валидация формы
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'staff_id' => 'required|exists:users,id',
            'appointment_time' => [
                'required',
                'date_format:Y-m-d\TH:i',
                function ($attribute, $value, $fail) {
                    $selected = Carbon::createFromFormat('Y-m-d\TH:i', $value);
                    if ($selected < now()) {
                        $fail('Нельзя записаться на прошедшее время');
                    }
                    if ($selected > now()->addMonths(3)) {
                        $fail('Максимальный срок записи — 3 месяца');
                    }
                },
            ],
        ]);
    }

    /**
     * Проверка графика работы филиала
     */
    protected function validateBranchWorkingHours(Branch $branch, Carbon $startTime, int $duration, int $buffer = 10): void
    {
        $dayOfWeek = $startTime->dayOfWeek;
        $record = $branch->schedule->firstWhere('day_of_week', $dayOfWeek);
    
        if (!$record || !$record->open_time || !$record->close_time) {
            throw new \Exception("Филиал не работает в этот день");
        }
    
        $branchOpenTime = Carbon::createFromTimeString($record->open_time);
        $branchCloseTime = Carbon::createFromTimeString($record->close_time);
    
        // Устанавливаем начало и конец дня с датой из $startTime
        $startOfDay = $startTime->copy()
            ->setHour($branchOpenTime->hour)
            ->setMinute($branchOpenTime->minute)
            ->setSecond(0);
    
        $endOfDay = $startTime->copy()
            ->setHour($branchCloseTime->hour)
            ->setMinute($branchCloseTime->minute)
            ->setSecond(0);
    
        // Конец вашей записи
        $endTime = $startTime->copy()->addMinutes($duration+10);

        // Проверка начала дня
        if ($startTime < $startOfDay) {
            throw new \Exception("Филиал открывается в {$branchOpenTime->format('H:i')}. Раньше этого времени запись невозможна");
        }
    
        // Проверка конца дня
        if ($endTime > $endOfDay) {
            $lastPossibleStart = $endOfDay->copy()->subMinutes($duration + $buffer);
            
            if ($lastPossibleStart < $startOfDay) {
                throw new \Exception("Филиал закрывается в {$branchCloseTime->format('H:i')}. Первая возможная запись — {$startOfDay->format('H:i')}");
            } else {
                throw new \Exception("Филиал закрывается в {$branchCloseTime->format('H:i')}. Последнее возможное время записи — {$lastPossibleStart->format('H:i')}");
            }
        }
    }

    /**
     * Проверка, что мастер свободен
     */
    protected function validateStaffAvailability(User $staff, Carbon $startTime, int $duration, int $buffer = 10): void
    {
        $endTime = $startTime->copy()->addMinutes($duration);

        $conflicting = Appointment::where('staff_id', $staff->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime, $buffer) {
                $query->where('appointment_time', '<', $endTime->copy()->addMinutes($buffer))
                      ->whereRaw("DATE_ADD(appointment_time, INTERVAL (SELECT duration FROM services WHERE id = service_id) MINUTE) > ?", [$startTime->copy()->subMinutes($buffer)]);
            })
            ->exists();

        if ($conflicting) {
            throw new \Exception("Специалист занят в это время");
        }
    }

    /**
     * Проверка: не более 3 записей в день
     */
    protected function validateUserDailyAppointments(int $userId, Carbon $startTime): void
    {
        $count = Appointment::where('user_id', $userId)
            ->whereDate('appointment_time', $startTime->toDateString())
            ->where('status', '!=', 'cancelled')
            ->count();

        if ($count >= 3) {
            throw new \Exception("Можно сделать максимум 3 записи в день");
        }
    }


    public function rate(Appointment $appointment, Request $request)
    {
        $request->validate(['rating' => 'required|integer|between:1,5']);
        $appointment->update(['rating' => $request->input('rating')]);
        return back()->with('success', 'Спасибо за вашу оценку!');
    }
}