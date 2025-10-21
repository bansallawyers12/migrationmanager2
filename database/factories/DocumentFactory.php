<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'file_name' => $this->faker->word() . '.pdf',
            'filetype' => 'application/pdf',
            'myfile' => 'documents/' . $this->faker->uuid() . '.pdf',
            'file_size' => $this->faker->numberBetween(1000, 5000000),
            'status' => $this->faker->randomElement(['draft', 'sent', 'signed']),
            'created_by' => Admin::factory(),
            'origin' => 'ad_hoc',
            'title' => $this->faker->sentence(3),
            'document_type' => $this->faker->randomElement(['agreement', 'nda', 'general', 'contract']),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high']),
            'primary_signer_email' => $this->faker->safeEmail(),
            'signer_count' => 1,
            'last_activity_at' => now(),
            'documentable_type' => null,
            'documentable_id' => null,
            'due_at' => null,
            'archived_at' => null,
        ];
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function sent(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function signed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'signed',
        ]);
    }

    public function overdue(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'due_at' => now()->subDays(5),
        ]);
    }

    public function archived(): self
    {
        return $this->state(fn (array $attributes) => [
            'archived_at' => now(),
        ]);
    }
}

