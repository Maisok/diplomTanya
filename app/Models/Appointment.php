<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'staff_id', // Добавлено поле для хранения ID специалиста
        'appointment_time',
        'status',
        'branch_id',
        'rating',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
    }

    public function appointments()
{
    return $this->hasMany(Appointment::class);
}

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}