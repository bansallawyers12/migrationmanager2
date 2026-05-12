<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicPhoneCallController extends Controller
{
    /**
     * Minimal HTTPS page that immediately navigates to tel:, so desktop/mobile browsers
     * show the same “open application / dial” prompt as on the public website.
     */
    public function __invoke(Request $request)
    {
        $raw = (string) $request->query('n', '');
        $digits = preg_replace('/\D/', '', $raw) ?? '';
        if ($digits === '' || strlen($digits) > 15) {
            $digits = '61396021330';
        }
        $telUri = 'tel:+'.$digits;

        return response()->view('public.phone-call-bridge', [
            'telUri' => $telUri,
            'displayPhone' => $this->formatDisplayPhone($digits),
        ]);
    }

    protected function formatDisplayPhone(string $digits): string
    {
        if ($digits === '61396021330') {
            return '+61 3 9602 1330';
        }

        if ($digits === '61883171340') {
            return '0883171340';
        }

        if ($digits === '611300859368') {
            return '1300 859 368';
        }

        return '+'.$digits;
    }
}
