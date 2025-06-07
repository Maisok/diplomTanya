<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'image',
        'yandex_id',
        'branch_id',
    ];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Отношение к услугам
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_staff', 'staff_id', 'service_id');
    }


    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }
    
    public function staffAppointments()
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    
    // App/Models/User.php

public function getAverageRatingAttribute()
{
    return $this->staffAppointments()
        ->where('status', 'completed')
        ->whereNotNull('rating')
        ->avg('rating');
}

public function getRatingCountAttribute()
{
    return $this->staffAppointments()
        ->where('status', 'completed')
        ->whereNotNull('rating')
        ->count();
}

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'string',
    ];
    
    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }


    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone;

        // Удаляем все нецифровые символы
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 11) {
            // Заменяем первую цифру на '8'
            $digits = '8' . substr($digits, 1);
            return substr($digits, 0, 1) . ' ' .
                substr($digits, 1, 3) . ' ' .
                substr($digits, 4, 3) . ' ' .
                substr($digits, 7, 2) . ' ' .
                substr($digits, 9, 2);
        }

        return '8 000 000 00 00'; // Формат по умолчанию
    }
}
