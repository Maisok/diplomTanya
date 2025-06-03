<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSchedule extends Model
{
    protected $fillable = [
        'branch_id',
        'day_of_week', 
        'open_time',
        'close_time'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}