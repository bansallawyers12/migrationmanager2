<?php

namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Label;
use App\Models\Admin;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all admins
        $admins = Admin::all();
        
        foreach ($admins as $admin) {
            // Create system labels for each user
            $systemLabels = [
                [
                    'name' => 'Inbox',
                    'color' => '#3B82F6', // Blue
                    'type' => 'system',
                    'icon' => 'fas fa-inbox',
                    'description' => 'Default inbox for incoming emails',
                ],
                [
                    'name' => 'Sent',
                    'color' => '#10B981', // Green
                    'type' => 'system',
                    'icon' => 'fas fa-paper-plane',
                    'description' => 'Emails sent from your domain',
                ],
            ];
            
            foreach ($systemLabels as $labelData) {
                Label::firstOrCreate(
                    [
                        'user_id' => $admin->id,
                        'name' => $labelData['name'],
                    ],
                    [
                        'color' => $labelData['color'],
                        'type' => $labelData['type'],
                        'icon' => $labelData['icon'],
                        'description' => $labelData['description'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
