<?php

namespace Database\Factories;

use App\Models\TitikJalan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TitikJalan>
 */
class TitikJalanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_titik' => 'Titik '.fake()->unique()->word(),
            'latitude' => fake()->latitude(-8.5, -7.5),
            'longitude' => fake()->longitude(110.0, 111.0),
        ];
    }
}
