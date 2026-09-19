<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => SkillCategory::inRandomOrder()->first()?->id ?? SkillCategory::factory(),
            'title' => 'Jasa Pembuatan ' . fake()->randomElement(['Website Company Profile', 'Aplikasi Mobile Android', 'Desain Logo & Branding', 'Artikel SEO Friendly']),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomElement([150000, 350000, 500000, 1500000, 2500000]),
            'estimated_days' => fake()->numberBetween(2, 14),
            'status' => 'active',
        ];
    }
}
