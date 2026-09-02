<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Services\SesSenderService;
use Illuminate\Http\JsonResponse;

/**
 * Returns SES / Admin Console From identities for the Compose Email "From" dropdown.
 * Used by partials/email-from-sendgrid-script.blade.php (AJAX on page load).
 */
class SendGridSendersController extends Controller
{
    public function __construct(private SesSenderService $sesSenders) {}

    /**
     * Return verified senders as JSON for AJAX (e.g. frontend populating From dropdown).
     * GET /crm/ses-senders (and legacy GET /crm/sendgrid-senders)
     */
    public function senders(): JsonResponse
    {
        $list = $this->sesSenders->getComposeSenders();

        return response()->json([
            'senders' => $list,
            'default_from' => $this->sesSenders->defaultFrom($list),
        ]);
    }
}
