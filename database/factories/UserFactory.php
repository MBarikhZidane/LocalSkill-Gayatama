<?php

namespace Database\Factories;

use App\Models\StudyProgram;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'university_id' => University::inRandomOrder()->first()?->id ?? University::factory(),
            'study_program_id' => StudyProgram::inRandomOrder()->first()?->id ?? StudyProgram::factory(),
            'name' => "$firstName $lastName",
            'email' => strtolower("$firstName.$lastName" . fake()->unique()->numberBetween(10, 99) . '@gmail.com'),
            'phone' => '08' . fake()->numerify('##########'),
            'bio' => fake()->sentence(10),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
