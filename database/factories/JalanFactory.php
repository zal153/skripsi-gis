<?php

namespace Database\Factories;

use App\Models\Jalan;
use App\Models\TitikJalan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jalan>
 */
class JalanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titik_awal_id' => TitikJalan::factory(),
            'titik_akhir_id' => TitikJalan::factory(),
            'jarak' => fake()->randomFloat(2, 0.1, 50.0),
        ];
    }
}
