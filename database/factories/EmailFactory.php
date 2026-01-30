<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password123',
            'display_name' => fake()->name(),
            'smtp_host' => 'smtp.zoho.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'status' => true,
            'user_id' => null,
        ];
    }

    /**
     * Indicate that the email account is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the email uses custom SMTP settings.
     */
    public function customSmtp(string $host, int $port = 587, string $encryption = 'tls'): static
    {
        return $this->state(fn (array $attributes) => [
            'smtp_host' => $host,
            'smtp_port' => $port,
            'smtp_encryption' => $encryption,
        ]);
    }
}

