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
use App\Models\Branch;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

// Главная и публичные маршруты
Route::get('/', [SpecialistController::class, 'index'])->name('home');
Route::get('/services', [ServiceShowController::class, 'index'])->name('showservice');
Route::get('/services/{service}', [ServiceShowController::class, 'show'])->name('services.show');
Route::get('/all-specialists', [SpecialistController::class, 'all'])->name('all.specialists');
Route::get('/services/{service}/appointments2', function(Service $service) {
    $branchId = request()->input('branch_id');
    
    $appointments = Appointment::with(['staff'])
        ->where('service_id', $service->id)
        ->where('status', '!=', 'cancelled') // <-- Исключаем отменённые
        ->when($branchId, function($query) use ($branchId) {
            $query->whereHas('staff', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        })
        ->where('appointment_time', '>=', now())
        ->orderBy('appointment_time')
        ->get();
    
    return response()->json($appointments);
});

    Route::get('/branches/{branch}/schedule', function(Branch $branch) {
        return response()->json([
            'monday_open' => $branch->monday_open ? substr($branch->monday_open, 0, 5) : null,
            'monday_close' => $branch->monday_close ? substr($branch->monday_close, 0, 5) : null,
            'tuesday_open' => $branch->tuesday_open ? substr($branch->tuesday_open, 0, 5) : null,
            'tuesday_close' => $branch->tuesday_close ? substr($branch->tuesday_close, 0, 5) : null,
            'wednesday_open' => $branch->wednesday_open ? substr($branch->wednesday_open, 0, 5) : null,
            'wednesday_close' => $branch->wednesday_close ? substr($branch->wednesday_close, 0, 5) : null,
            'thursday_open' => $branch->thursday_open ? substr($branch->thursday_open, 0, 5) : null,
            'thursday_close' => $branch->thursday_close ? substr($branch->thursday_close, 0, 5) : null,
            'friday_open' => $branch->friday_open ? substr($branch->friday_open, 0, 5) : null,
            'friday_close' => $branch->friday_close ? substr($branch->friday_close, 0, 5) : null,
            'saturday_open' => $branch->saturday_open ? substr($branch->saturday_open, 0, 5) : null,
            'saturday_close' => $branch->saturday_close ? substr($branch->saturday_close, 0, 5) : null,
            'sunday_open' => $branch->sunday_open ? substr($branch->sunday_open, 0, 5) : null,
            'sunday_close' => $branch->sunday_close ? substr($branch->sunday_close, 0, 5) : null
        ]);
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

    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
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
    try {
        // Проверяем наличие service_id
        if (!$request->has('service_id')) {
            return response()->json(['error' => 'Service ID is required'], 400);
        }

        // Получаем сотрудников по филиалу и сервису, с учетом статуса
        $staff = $branch->staff()
            ->where('status', 'active') // <-- Учет статуса
            ->whereHas('services', function($query) use ($request) {
                $query->where('services.id', $request->service_id);
            })
            ->select('id', 'first_name', 'last_name')
            ->get();

        return response()->json($staff);

    } catch (\Exception $e) {
        Log::error("Error in /branches/{branch}/staff: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
});