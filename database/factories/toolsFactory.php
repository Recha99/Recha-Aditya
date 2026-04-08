<?php

namespace Database\Factories;

use App\Models\tools;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<tools>
 */
class toolsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_alat' => $this->faker->word(),
            'category_id' => Category::factory(),
            'stok' => $this->faker->numberBetween(1, 100),
            'deskripsi' => $this->faker->sentence(),
        ];
    }
}
