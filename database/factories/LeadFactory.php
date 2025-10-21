<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' => bcrypt('password'),
            'role' => 7, // Lead role
            'type' => 'lead', // Lead type
            'user_id' => Admin::factory()->create(['role' => 2])->id, // Assigned to staff
            'lead_quality' => $this->faker->randomElement(['hot', 'warm', 'cold']),
            'lead_status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'lost']),
            'source' => $this->faker->optional()->randomElement(['website', 'referral', 'social_media', 'direct']),
            'remember_token' => Str::random(10),
            'is_archived' => 0,
            'is_deleted' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function new(): self
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'new',
        ]);
    }

    public function qualified(): self
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'qualified',
        ]);
    }

    public function converted(): self
    {
        return $this->state(fn (array $attributes) => [
            'lead_status' => 'converted',
            'type' => 'client',
        ]);
    }
}

