<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spy>
 */
class SpyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>'Namae',
            'surname'=>'Myoji',
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'country_of_operation'=>'GR'
        ];
    }
}
