<?php

namespace Database\Factories;

use App\Models\Desa;
use App\Models\Posyandu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Posyandu>
 */
class PosyanduFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'desa_id' => Desa::factory(),
            'nama_posyandu' => 'Posyandu '.fake()->unique()->word(),
            'alamat' => fake()->address(),
            'latitude' => fake()->latitude(-8.5, -7.5),
            'longitude' => fake()->longitude(110.0, 111.0),
            'status' => fake()->randomElement(['aktif', 'tidak aktif']),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }
}
