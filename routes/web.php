<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ServiceShowController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Middleware\PreventConcurrentLogins;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StaffAppointmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffExportController;
use App\Models\Branch;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\BranchSchedule;

Route::middleware(['auth'])->group(function () {
    Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('/admins/{user}/edit', [AdminController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{user}', [AdminController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy');

    Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('staff.appointments.index');

    Route::get('/clients/export', [StaffExportController::class, 'index'])->name('staff.exports.clients.index');
    Route::get('/clients/export/download', [StaffExportController::class, 'export'])->name('staff.exports.clients.export');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/reports/completed', [ReportController::class, 'completed'])->name('admin.reports.completed');
});

// Экспорт
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/exports/completed', [ExportController::class, 'exportCompleted'])->name('export.completed.appointments');
});

// Главная и публичные маршруты
Route::get('/', [SpecialistController::class, 'index'])->name('home');
Route::get('/services', [ServiceShowController::class, 'index'])->name('showservice');
Route::get('/services/{service}', [ServiceShowController::class, 'show'])->name('services.show');
Route::get('/all-specialists', [SpecialistController::class, 'all'])->name('all.specialists');
Route::get('/services/{service}/appointments2', function(Service $service) {
    $branchId = request()->input('branch_id');

    $appointments = $service->appointments()
        ->with(['staff' => function($query) use ($branchId) {
            $query->where('role', 'staff');
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
        }])
        ->where('status', '!=', 'cancelled')
        ->where('appointment_time', '>=', now())
        ->orderBy('appointment_time')
        ->get();

    return response()->json($appointments);
});

Route::get('/branches/{branch}/schedule', function(Branch $branch) {
    $days = [0, 1, 2, 3, 4, 5, 6];
    
    $schedule = [];

    foreach ($days as $day) {
        $record = $branch->schedule->firstWhere('day_of_week', $day);
        
        switch ($day) {
            case 1: $dayName = 'monday'; break;
            case 2: $dayName = 'tuesday'; break;
            case 3: $dayName = 'wednesday'; break;
            case 4: $dayName = 'thursday'; break;
            case 5: $dayName = 'friday'; break;
            case 6: $dayName = 'saturday'; break;
            case 0: $dayName = 'sunday'; break;
        }

        if ($record && $record->open_time && $record->close_time) {
            $schedule["{$dayName}_open"] = substr($record->open_time, 0, 5);
            $schedule["{$dayName}_close"] = substr($record->close_time, 0, 5);
        } else {
            $schedule["{$dayName}_open"] = null;
            $schedule["{$dayName}_close"] = null;
        }
    }

    return response()->json($schedule);
});

// Социальная аутентификация
Route::get('login/yandex', [AuthController::class, 'yandex'])->name('yandex');
Route::get('login/yandex/redirect', [AuthController::class, 'yandexRedirect'])->name('yandexRedirect');
Route::get('auth/vk', [AuthController::class, 'redirectToVK'])->name('redirectToVK');
Route::get('auth/vk/callback', [AuthController::class, 'handleProviderCallback']);

// Группа для неаутентифицированных пользователей
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware(PreventConcurrentLogins::class.':web');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
});

// Группа для аутентифицированных пользователей
Route::middleware(['auth', PreventConcurrentLogins::class.':web'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::post('/services/{service}/appointments', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/services/{service}/appointments', [AppointmentController::class, 'show'])->name('appointments.show');

    Route::post('/profile/send-verification-code', [ProfileController::class, 'sendVerificationCode'])->name('profile.send-verification-code');
    Route::post('/profile/update-email', [ProfileController::class, 'updateEmail'])->name('profile.update-email');

    Route::post('/appointments/{appointment}/rate', [AppointmentController::class, 'rate'])
    ->name('appointments.rate')
    ->middleware('auth');





});

// Группа для персонала
Route::prefix('staff')->group(function () {
    Route::middleware(['staff.guest', PreventConcurrentLogins::class.':staff'])->group(function () {
        Route::get('login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
        Route::post('login', [StaffAuthController::class, 'login']);
    });

    Route::middleware(['staff.auth', PreventConcurrentLogins::class.':staff'])->group(function () {
        Route::post('logout', [StaffAuthController::class, 'logout'])->name('staff.logout');
        Route::get('dashboard', [StaffAuthController::class, 'dashboard'])->name('staff.dashboard');
        
        Route::post('/appointment/{id}/complete', [StaffController::class, 'completeAppointment'])->name('staff.appointment.complete');
        Route::post('/appointment/{id}/cancel', [StaffController::class, 'cancelAppointment'])->name('staff.appointment.cancel');
    });
});

// Админ-панель
Route::prefix('admin')->middleware(['auth', PreventConcurrentLogins::class.':web'])->group(function () {

    Route::get('/', [AdminController::class, 'index_main'])->name('admin.dashboard');
    Route::get('/export/active-appointments', [ExportController::class, 'exportActiveAppointments'])->name('export.active-appointments');
    Route::get('/export/completed-appointments', [ExportController::class, 'exportCompletedAppointments'])->name('export.completed-appointments');
    // Управление персоналом
    Route::get('/staff', [StaffController::class, 'index'])->name('admin.staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');
    
    // Управление услугами
    Route::get('/service', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/service/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/service', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/service/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/service/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
    
    // Управление записями
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('admin.all-appointments');
    Route::post('/appointments/{id}/activate', [AdminAppointmentController::class, 'activate'])->name('admin.appointments.activate');
    Route::post('/appointments/{id}/complete', [AdminAppointmentController::class, 'complete'])->name('admin.appointments.complete');
    Route::post('/appointments/{id}/cancel', [AdminAppointmentController::class, 'cancel'])->name('admin.appointments.cancel');
    
    // Экспорт
    Route::get('/export/cancelled-appointments', [ExportController::class, 'cancelledAppointments'])->name('export.cancelled-appointments');

    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');


    Route::get('/branches', [BranchController::class, 'index'])->name('admin.branches.index');
    Route::get('/branches/create', [BranchController::class, 'create'])->name('admin.branches.create');
    Route::post('/branches', [BranchController::class, 'store'])->name('admin.branches.store');
    Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('admin.branches.edit');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('admin.branches.update');
    Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');


   

    Route::get('/branches/{branch}/working-hours', [AppointmentController::class, 'getBranchWorkingHours']);
    Route::get('/staff/{staff}/availability', [AppointmentController::class, 'checkStaffAvailability']);

   
    
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');

   
});
 
Route::get('/branches/{branch}/staff', function(Branch $branch, Request $request) {
    if (!$request->has('service_id')) {
        return response()->json(['error' => 'Service ID обязателен'], 400);
    }

    $staff = $branch->users()
        ->where('role', 'staff')
        ->where('status', 'active') // если есть статус у пользователя
        ->whereHas('services', function ($query) use ($request) {
            $query->where('services.id', $request->input('service_id'));
        })
        ->get(['id', 'name', 'surname']);

    return response()->json($staff->map(function ($user) {
        return [
            'id' => $user->id,
            'first_name' => $user->name,
            'last_name' => $user->surname,
        ];
    }));
});