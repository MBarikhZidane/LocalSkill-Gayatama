<?php

namespace Database\Factories;

use App\Models\StudyProgram;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyProgram>
 */
class StudyProgramFactory extends Factory
{
    protected static array $prodi = [
        'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer',
        'Teknik Elektro', 'Teknik Industri', 'Manajemen', 'Akuntansi',
        'Ilmu Hukum', 'Kedokteran', 'Ilmu Komunikasi', 'Psikologi'
    ];

    public function definition(): array
    {
        return [
            'university_id' => University::factory(),
            'name' => fake()->randomElement(self::$prodi),
            'code' => strtoupper(fake()->bothify('PRODI-###')),
        ];
    }
}
