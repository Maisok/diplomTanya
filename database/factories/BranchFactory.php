<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition()
    {
        return [
            'image' => $this->faker->imageUrl(),
            'address' => 'г.' . $this->faker->city . ', ул.' . $this->faker->streetName . ', д.' . $this->faker->buildingNumber,
            'monday_open' => '09:00',
            'monday_close' => '18:00',
            // Остальные дни можно оставить null или задать аналогично
            'tuesday_open' => '09:00',
            'tuesday_close' => '18:00',
            'wednesday_open' => '09:00',
            'wednesday_close' => '18:00',
            'thursday_open' => '09:00',
            'thursday_close' => '18:00',
            'friday_open' => '09:00',
            'friday_close' => '18:00',
            'saturday_open' => $this->faker->optional()->time('10:00'),
            'saturday_close' => $this->faker->optional()->time('15:00'),
            'sunday_open' => null, // Воскресенье - выходной
            'sunday_close' => null,
        ];
    }
}