<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Order;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\StudyProgram;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void
    {
        // 1. Buat Data Universitas Khas Indonesia (Pasti Unik)
        $universityData = [
            ['name' => 'Universitas Indonesia', 'code' => 'UI', 'city' => 'Depok', 'address' => 'Jl. Lingkar Kampus UI, Depok', 'latitude' => -6.3600, 'longitude' => 106.8283],
            ['name' => 'Universitas Gadjah Mada', 'code' => 'UGM', 'city' => 'Sleman', 'address' => 'Bulaksumur, Caturtunggal, Depok, Sleman', 'latitude' => -7.7706, 'longitude' => 110.3788],
            ['name' => 'Institut Teknologi Bandung', 'code' => 'ITB', 'city' => 'Bandung', 'address' => 'Jl. Ganesa No.10, Coblong, Bandung', 'latitude' => -6.8915, 'longitude' => 107.6107],
            ['name' => 'Institut Teknologi Sepuluh Nopember', 'code' => 'ITS', 'city' => 'Surabaya', 'address' => 'Jl. Arief Rahman Hakim, Sukolilo, Surabaya', 'latitude' => -7.2824, 'longitude' => 112.7949],
            ['name' => 'Universitas Airlangga', 'code' => 'UNAIR', 'city' => 'Surabaya', 'address' => 'Jl. Mulyorejo, Kampus C UNAIR, Surabaya', 'latitude' => -7.2658, 'longitude' => 112.7842],
        ];

        foreach ($universityData as $data) {
            $uni = University::create($data);

            // Ambil 3 prodi acak tanpa duplikat untuk setiap universitas
            $prodiNames = fake()->randomElements([
                'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer',
                'Teknik Elektro', 'Teknik Industri', 'Manajemen', 'Akuntansi',
                'Ilmu Hukum', 'Kedokteran', 'Ilmu Komunikasi', 'Psikologi'
            ], 3);

            foreach ($prodiNames as $prodiName) {
                StudyProgram::create([
                    'university_id' => $uni->id,
                    'name' => $prodiName,
                    'code' => strtoupper(fake()->bothify('PRODI-###')),
                ]);
            }
        }

        // 2. Buat Kategori & Skill
        $categories = SkillCategory::factory(5)->create();
        $skills = Skill::factory(8)->create();

        // 3. Buat User & Lokasi
        $users = User::factory(15)->create();

        $users->each(function ($user) use ($skills) {
            Location::create([
                'user_id' => $user->id,
                'latitude' => fake()->latitude(-7.8, -6.1),
                'longitude' => fake()->longitude(106.8, 112.8),
                'address' => fake()->streetAddress() . ', ' . fake()->city(),
            ]);

            $user->skills()->attach(
                $skills->random(rand(1, 3))->pluck('id')->toArray(),
                [
                    'proficiency_level' => rand(1, 5),
                    'years_experience' => rand(1, 5),
                    'is_verified' => true,
                ]
            );
        });

        // 4. Buat Services & Orders
        $services = Service::factory(10)->create();

        $services->each(function ($service) use ($users) {
            $customer = $users->where('id', '!=', $service->user_id)->random();

            Order::factory()->create([
                'customer_id' => $customer->id,
                'provider_id' => $service->user_id,
                'service_id' => $service->id,
                'price' => $service->price,
                'total_amount' => $service->price + 10000,
            ]);
        });
    }
}
