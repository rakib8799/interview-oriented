<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdBy = fake()->numberBetween(2, 10);
        return [
            'title' => fake()->sentence(10),
            'description' => fake()->paragraph(),
            'is_active' => fake()->boolean(),
            'created_by' => $createdBy,
            'updated_by' => $createdBy
        ];
    }
}
