<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     * Genera datos de prueba realistas para productos
     * 
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true), // Genera nombres de 2 palabras
            'sku' => fake()->unique()->regexify('[A-Z]{2}[0-9]{4}'), // Formato: AA1234
            'price' => fake()->randomFloat(2, 10, 1000), // Precio entre $10 y $1000
            'stock' => fake()->numberBetween(0, 500), // Stock entre 0 y 500
        ];
    }
}
