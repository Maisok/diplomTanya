<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'middle_name' => $this->faker->optional()->firstName,
            'image' => $this->faker->optional()->imageUrl(),
            'phone' => '+7' . $this->faker->numerify('##########'), // Российский номер
            'password' => Hash::make('password123'),
        ];
    }
}