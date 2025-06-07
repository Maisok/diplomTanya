<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'duration',
        'category_id',
        'status'
    ];

   // Связь: услуга может предоставляться несколькими сотрудниками
   public function staff()
   {
       return $this->belongsToMany(User::class, 'service_staff', 'service_id', 'staff_id');
   }

   public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function services()
{
    return $this->belongsToMany(Service::class, 'service_staff');
}

   // Связь: филиалы через сотрудников
   public function branches()
   {
       return $this->hasManyThrough(Branch::class, User::class, 'branch_id', 'id', 'staff_id', 'id');
   }

   public function category()
   {
       return $this->belongsTo(Category::class);
   }

   public function appointments()
   {
       return $this->hasMany(Appointment::class);
   }


   // App/Models/User.php

public function getAverageRatingAttribute()
{
    return $this->appointments()
        ->where('status', 'completed')
        ->whereNotNull('rating')
        ->avg('rating');
}

public function getRatingCountAttribute()
{
    return $this->appointments()
        ->where('status', 'completed')
        ->whereNotNull('rating')
        ->count();
}
    

}