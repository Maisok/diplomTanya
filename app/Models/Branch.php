<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'address',
        'status'
    ];
    protected $appends = ['formatted_schedule'];

    public function getFormattedScheduleAttribute()
    {
        $days = [1, 2, 3, 4, 5, 6, 0];
        $result = [];

        foreach ($days as $day) {
            $record = $this->schedule->firstWhere('day_of_week', $day);
            if ($record && $record->open_time && $record->close_time) {
                $result[$day] = [
                    'open' => substr($record->open_time, 0, 5),
                    'close' => substr($record->close_time, 0, 5),
                ];
            } else {
                $result[$day] = null;
            }
        }

        return $result;
    }


    public function services()
    {
        return $this->hasManyThrough(Service::class, User::class);
    }

    public function users()
{
    return $this->hasMany(User::class, 'branch_id', 'id');
}


public function schedule()
{
    return $this->hasMany(BranchSchedule::class);
}


}