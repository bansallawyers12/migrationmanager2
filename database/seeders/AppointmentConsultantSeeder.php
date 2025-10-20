<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentConsultantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consultants = [
            [
                'name' => 'Arun Kumar (Pr_complex matters)',
                'email' => 'arun@bansalimmigration.com',
                'calendar_type' => 'paid',
                'location' => 'melbourne',
                'specializations' => json_encode([1, 6, 7, 8]),
                'is_active' => true,
            ],
            [
                'name' => 'Shubham/Yadwinder (JRP)',
                'email' => 'shubham@bansalimmigration.com',
                'calendar_type' => 'jrp',
                'location' => 'melbourne',
                'specializations' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'name' => 'Education Team',
                'email' => 'education@bansalimmigration.com',
                'calendar_type' => 'education',
                'location' => 'melbourne',
                'specializations' => json_encode([5]),
                'is_active' => true,
            ],
            [
                'name' => 'Tourist Visa Team',
                'email' => 'tourist@bansalimmigration.com',
                'calendar_type' => 'tourist',
                'location' => 'melbourne',
                'specializations' => json_encode([4]),
                'is_active' => true,
            ],
            [
                'name' => 'Adelaide Office',
                'email' => 'adelaide@bansalimmigration.com',
                'calendar_type' => 'adelaide',
                'location' => 'adelaide',
                'specializations' => json_encode([1, 2, 3, 4, 5, 6, 7, 8]),
                'is_active' => true,
            ],
        ];

        foreach ($consultants as $consultant) {
            DB::table('appointment_consultants')->insert(array_merge($consultant, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✓ Created 5 appointment consultants');
    }
}

