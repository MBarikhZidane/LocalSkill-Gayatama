<?php

namespace Database\Factories;

use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillCategory>
 */
class SkillCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected static array $categories = [
        'Pemrograman & Teknologi', 'Desain Grafis & Multimedia', 
        'Pemasaran Digital', 'Penulisan & Terjemahan', 'Bisnis & Konsultasi'
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(self::$categories),
            'description' => fake()->sentence(),
        ];
    }
}
