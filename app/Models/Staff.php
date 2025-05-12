<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'image',
        'phone',
        'password',
        'branch_id',
    ];

    protected $hidden = [
        'password', // Скрыть пароль при сериализации
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class); // Исправлено пространство имен
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->appointments()
            ->whereNotNull('rating')
            ->avg('rating');
    }
}