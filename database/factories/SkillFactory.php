<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected static array $skills = [
        'Laravel Development', 'Vue.js Framework', 'Flutter Mobile App',
        'UI/UX Design', 'Digital Marketing SEO', 'Content Writing',
        'Data Analysis with Python', 'Graphic Design Photoshop', 'Copywriting'
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(self::$skills),
            'description' => fake()->sentence(),
        ];
    }
}
