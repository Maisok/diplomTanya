<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'image',
        'address',
        'monday_open', 'monday_close',
        'tuesday_open', 'tuesday_close',
        'wednesday_open', 'wednesday_close',
        'thursday_open', 'thursday_close',
        'friday_open', 'friday_close',
        'saturday_open', 'saturday_close',
        'sunday_open', 'sunday_close'
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, Staff::class);
    }
}