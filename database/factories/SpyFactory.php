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
        $agencies = \Illuminate\Support\Facades\Config::get('agencies');
        $agency = $agencies[array_rand($agencies)];
       
        return [
            'name'=>'Namae'.(int)rand(),
            'surname'=>'Myoji'.(int)rand(),
            'birth_date'=>'1980-1-1',
            'death_date'=>'1985-1-1',
            'country_of_operation'=>'GR',
            'agency'=>$agency
        ];
    }
}
