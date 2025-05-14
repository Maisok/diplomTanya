<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Models\Staff;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        return [
            'service_id' => Service::factory(),
            'user_id' => User::factory(),
            'staff_id' => Staff::factory(),
            'branch_id' => Branch::factory(),
            'appointment_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['active', 'completed', 'cancelled']),
            'rating' => $this->faker->optional()->numberBetween(1, 5),
        ];
    }
}