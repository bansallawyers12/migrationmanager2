<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\HomeController;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerCrmExtraSlotsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.bansal_api.token' => 'test-token',
            'services.bansal_api.url' => 'https://www.bansalimmigration.com.au/api/crm',
            'services.bansal_api.timeout' => 30,
        ]);
    }

    #[Test]
    public function getdatetimebackend_asks_bansal_for_crm_extra_slots(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://www.bansalimmigration.com.au/api/crm/appointments/get-datetime-backend' => Http::response([
                'success' => true,
                'duration' => 20,
                'start_time' => '10:20',
                'end_time' => '15:40',
                'weeks' => [0, 5, 6],
                'disabledatesarray' => [],
            ], 200),
        ]);

        $response = json_decode($this->homeController()->getdatetimebackend($this->scheduleRequest([
            'id' => 1,
            'enquiry_item' => 3,
            'inperson_address' => 2,
            'slot_overwrite' => 0,
        ])), true);

        $this->assertTrue($response['success'] ?? false);
        $this->assertSame('15:40', $response['end_time']);

        Http::assertSent(function (HttpRequest $request) {
            $payload = $request->data();

            return str_contains($request->url(), '/appointments/get-datetime-backend')
                && ($payload['include_crm_extra_slots'] ?? null) === 1
                && ($payload['service_type'] ?? null) === 'jrp-skill-assessment'
                && ($payload['specific_service'] ?? null) === 'consultation';
        });
    }

    #[Test]
    public function getdisableddatetime_does_not_ask_bansal_to_treat_extra_slots_as_disabled(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://www.bansalimmigration.com.au/api/crm/appointments/get-disabled-datetime' => Http::response([
                'success' => true,
                'disabledtimeslotes' => ['11:00 AM', '11:40 AM'],
            ], 200),
        ]);

        $response = json_decode($this->homeController()->getdisableddatetime($this->scheduleRequest([
            'service_id' => 1,
            'enquiry_item' => 3,
            'inperson_address' => 2,
            'sel_date' => '24/09/2026',
            'slot_overwrite' => 0,
        ])), true);

        $this->assertTrue($response['success'] ?? false);
        $this->assertSame(['11:00 AM', '11:40 AM'], $response['disabledtimeslotes']);

        Http::assertSent(function (HttpRequest $request) {
            $payload = $request->data();

            return str_contains($request->url(), '/appointments/get-disabled-datetime')
                && ! array_key_exists('include_crm_extra_slots', $payload)
                && ($payload['service_type'] ?? null) === 'jrp-skill-assessment'
                && ($payload['sel_date'] ?? null) === '24/09/2026';
        });
    }

    private function homeController(): HomeController
    {
        return new HomeController(Request::create('/getdatetimebackend', 'POST'));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function scheduleRequest(array $payload): Request
    {
        return Request::create('/schedule', 'POST', $payload);
    }
}
