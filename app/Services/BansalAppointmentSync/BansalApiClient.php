<?php

namespace App\Services\BansalAppointmentSync;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Exception;

class BansalApiClient
{
    /**
     * Create a new API client instance.
     */
    public function __construct(
        protected ?string $baseUrl = null,
        protected ?string $apiToken = null,
        protected int $timeout = 30
    ) {
        $this->baseUrl = $baseUrl ?: config('services.bansal_api.url', 'https://bansalimmigration.com/api/crm');
        $this->apiToken = $apiToken ?: config('services.bansal_api.token');
        $this->timeout = $timeout ?: config('services.bansal_api.timeout', 30);

        if (empty($this->apiToken)) {
            throw new Exception('Bansal API token not configured. Set BANSAL_API_TOKEN in .env');
        }
    }

    /**
     * Get configured HTTP client.
     */
    protected function client(): PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withToken($this->apiToken)
            ->acceptJson()
            ->throw(); // Laravel 12: Automatic exception throwing
    }

    /**
     * Fetch recent appointments from Bansal API.
     */
    public function getRecentAppointments(int $minutes = 10): array
    {
        try {
            // Laravel 12: Cleaner HTTP client with throw()
            $response = $this->client()
                ->get("{$this->baseUrl}/appointments/recent", [
                    'minutes' => $minutes
                ]);

            $data = $response->json();

            // Laravel 12: Use throw_if for cleaner validation
            throw_if(
                !isset($data['success']) || $data['success'] !== true,
                new Exception("API returned unsuccessful response: " . json_encode($data))
            );

            return $data['data'] ?? [];
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getRecentAppointments',
                'error' => $e->getMessage(),
                'minutes' => $minutes
            ]);
            throw $e;
        }
    }

    /**
     * Fetch single appointment by ID
     */
    public function getAppointmentById(int $id): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments/{$id}");

            if ($response->status() === 404) {
                return null;
            }

            if ($response->failed()) {
                throw new Exception("API request failed: {$response->status()}");
            }

            $data = $response->json();
            return $data['data'] ?? null;
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getAppointmentById',
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Fetch all appointments in date range (for backfill)
     */
    public function getAppointmentsByDateRange(string $startDate, string $endDate, int $page = 1): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments", [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'page' => $page,
                    'per_page' => 100
                ]);

            if ($response->failed()) {
                throw new Exception("API request failed: {$response->status()}");
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bansal API Client Error', [
                'method' => 'getAppointmentsByDateRange',
                'error' => $e->getMessage(),
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            throw $e;
        }
    }

    /**
     * Test API connection
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiToken)
                ->get("{$this->baseUrl}/appointments/recent", ['minutes' => 1]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Bansal API Connection Test Failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

