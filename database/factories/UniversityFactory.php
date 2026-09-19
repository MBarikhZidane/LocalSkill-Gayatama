<?php

namespace Database\Factories;

use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<University>
 */
class UniversityFactory extends Factory
{
    protected static array $universities = [
        ['name' => 'Universitas Indonesia', 'code' => 'UI', 'city' => 'Depok', 'address' => 'Jl. Lingkar Kampus UI, Depok, Jawa Barat', 'lat' => -6.3600, 'lng' => 106.8283],
        ['name' => 'Universitas Gadjah Mada', 'code' => 'UGM', 'city' => 'Sleman', 'address' => 'Bulaksumur, Caturtunggal, Depok, Sleman, Yogyakarta', 'lat' => -7.7706, 'lng' => 110.3788],
        ['name' => 'Institut Teknologi Bandung', 'code' => 'ITB', 'city' => 'Bandung', 'address' => 'Jl. Ganesa No.10, Lb. Siliwangi, Coblong, Bandung, Jawa Barat', 'lat' => -6.8915, 'lng' => 107.6107],
        ['name' => 'Institut Teknologi Sepuluh Nopember', 'code' => 'ITS', 'city' => 'Surabaya', 'address' => 'Jl. Arief Rahman Hakim, Keputih, Sukolilo, Surabaya, Jawa Timur', 'lat' => -7.2824, 'lng' => 112.7949],
        ['name' => 'Universitas Airlangga', 'code' => 'UNAIR', 'city' => 'Surabaya', 'address' => 'Jl. Mulyorejo, Kampus C UNAIR, Surabaya, Jawa Timur', 'lat' => -7.2658, 'lng' => 112.7842],
        ['name' => 'Universitas Brawijaya', 'code' => 'UB', 'city' => 'Malang', 'address' => 'Jl. Veteran, Ketawanggede, Lowokwaru, Malang, Jawa Timur', 'lat' => -7.9546, 'lng' => 112.6140],
        ['name' => 'Universitas Diponegoro', 'code' => 'UNDIP', 'city' => 'Semarang', 'address' => 'Jl. Prof. H. Soedarto, S.H., Tembalang, Semarang, Jawa Tengah', 'lat' => -7.0560, 'lng' => 110.4392],
    ];

    public function definition(): array
    {
        $uni = fake()->randomElement(self::$universities);

        return [
            'name' => $uni['name'],
            'code' => $uni['code'],
            'address' => $uni['address'],
            'city' => $uni['city'],
            'latitude' => $uni['lat'],
            'longitude' => $uni['lng'],
        ];
    }
}
