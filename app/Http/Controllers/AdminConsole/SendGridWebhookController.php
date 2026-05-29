<?php

namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use App\Services\SendGridWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SendGridWebhookController extends Controller
{
    public function __construct(private SendGridWebhookService $webhookService)
    {
    }

    /**
     * Receive SendGrid Event Webhook POSTs (delivery events for Phase 1).
     */
    public function events(Request $request)
    {
        if (! $this->authorizeRequest($request)) {
            return response('Unauthorized', 401);
        }

        $payload = $request->getContent();
        $events = json_decode($payload, true);

        if (! is_array($events)) {
            Log::warning('SendGrid webhook: invalid JSON payload');

            return response('Invalid payload', 400);
        }

        Log::info('SendGrid Event Webhook received', ['count' => count($events)]);

        $stats = $this->webhookService->processEvents($events);

        Log::info('SendGrid Event Webhook processed', $stats);

        return response('OK', 200);
    }

    private function authorizeRequest(Request $request): bool
    {
        $token = config('services.sendgrid.webhook_token');
        if ($token !== null && $token !== '') {
            $provided = $request->query('token', $request->header('X-Webhook-Token'));

            return is_string($provided) && hash_equals($token, $provided);
        }

        return true;
    }
}
