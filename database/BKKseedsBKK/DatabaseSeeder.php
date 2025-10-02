<?php

namespace Database\Seeds;

use App\Models\Admin;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin::factory(10)->create();

        // Create a test admin if none exists
        if (Admin::count() === 0) {
            Admin::create([
                'email' => 'admin@bansalimmigration.com.au',
                'password' => bcrypt('password'),
            ]);
        }
        
        // Seed system labels
        $this->call([
            LabelSeeder::class,
        ]);
    }
}
