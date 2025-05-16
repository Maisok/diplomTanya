<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

// app/Models/Service.php
protected $fillable = [
    'name',
    'description',
    'price',
    'duration', // Добавляем
    'image',
    'staff_id',
    'category_id', 
    'status'
];

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branches()
    {
        return Branch::whereHas('staff.services', function($query) {
            $query->where('services.id', $this->id);
        })->distinct()->get();
    }

    public function getAverageRatingAttribute()
    {
        return $this->appointments()
            ->whereNotNull('rating')
            ->avg('rating');
    }
    
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}