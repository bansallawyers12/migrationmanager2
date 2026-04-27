<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\PublicLeadInquiryAdminNotification;
use App\Models\Admin;
use App\Services\PublicLeadInquiryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicLeadInquiryController extends Controller
{
    public function showForm(Request $request, PublicLeadInquiryService $service): View
    {
        $pending = session($service::SESSION_PENDING_CONFIRM);

        return view('public.lead_inquiry_form', [
            'formAction' => route('public.lead-inquiry.submit'),
            'confirmAction' => route('public.lead-inquiry.confirm'),
            'cancelAction' => route('public.lead-inquiry.cancel'),
            'pendingConfirm' => is_array($pending) ? $pending : null,
        ]);
    }

    public function submit(Request $request, PublicLeadInquiryService $service): RedirectResponse
    {
        $validated = $request->validate(PublicLeadInquiryService::validationRules());

        $normalised = $service->normaliseProposed(
            $validated['name'],
            $validated['phone'],
            $validated['email'],
            $validated['visa_subclass'] ?? null,
            $validated['address'] ?? null
        );
        $emailLower = mb_strtolower($normalised['email']);
        $existing = $service->findClientOrLeadByEmailOrPhone($emailLower, $normalised['phone']);

        if ($existing) {
            $proposedDisplay = $normalised;
            $proposedVisa = $normalised['visa_subclass'] ?? null;
            $proposedDisplay['visa_subclass'] = $proposedVisa !== null && (string) $proposedVisa !== ''
                ? $service->formatVisaTypeForDisplay(trim((string) $proposedVisa))
                : null;
            session()->put($service::SESSION_PENDING_CONFIRM, [
                'admin_id' => (int) $existing->id,
                'proposed' => $normalised,
                'proposed_display' => $proposedDisplay,
                'existing_display' => $service->existingRecordForDisplay($existing),
            ]);

            return redirect()->route('public.lead-inquiry');
        }

        try {
            $record = $service->createNewLead(
                $normalised['name'],
                $normalised['phone'],
                $normalised['email'],
                $normalised['visa_subclass'] ?? null,
                $normalised['address'] ?? null
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Public lead inquiry submit failed', ['exception' => $e]);
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['form' => __('We could not save your request. Please try again later or call our office.')]);
        }

        $this->sendAdminNotification(true, $record, [
            'name' => $normalised['name'],
            'phone' => $normalised['phone'],
            'email' => $normalised['email'],
            'visa_subclass' => $normalised['visa_subclass'] ?? null,
            'address' => $normalised['address'] ?? null,
        ]);

        $message = __(
            'Thanks for providing details. You are a new Lead for our system. Our team will contact you.'
        );

        return redirect()
            ->route('public.lead-inquiry')
            ->with('success', $message);
    }

    public function confirmUpdate(Request $request, PublicLeadInquiryService $service): RedirectResponse
    {
        $data = session($service::SESSION_PENDING_CONFIRM);
        if (! is_array($data) || empty($data['admin_id']) || empty($data['proposed']) || ! is_array($data['proposed'])) {
            return redirect()
                ->route('public.lead-inquiry')
                ->withErrors(['form' => __('This confirmation has expired. Please submit the form again.')]);
        }

        $admin = Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->whereNull('is_deleted')
            ->whereKey($data['admin_id'])
            ->first();

        if (! $admin) {
            session()->forget($service::SESSION_PENDING_CONFIRM);
            return redirect()
                ->route('public.lead-inquiry')
                ->withErrors(['form' => __('We could not find that record. Please start again.')]);
        }

        $p = $data['proposed'];

        try {
            $record = $service->applyUpdateToExisting(
                $admin,
                $p['name'],
                $p['phone'],
                $p['email'],
                $p['visa_subclass'] ?? null,
                $p['address'] ?? null
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->route('public.lead-inquiry')
                ->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('Public lead inquiry confirm update failed', ['exception' => $e]);
            return redirect()
                ->route('public.lead-inquiry')
                ->withErrors(['form' => __('We could not update your details. Please try again or call our office.')]);
        }

        session()->forget($service::SESSION_PENDING_CONFIRM);

        $this->sendAdminNotification(false, $record, [
            'name' => $p['name'],
            'phone' => $p['phone'],
            'email' => $p['email'],
            'visa_subclass' => $p['visa_subclass'] ?? null,
            'address' => $p['address'] ?? null,
        ]);

        return redirect()
            ->route('public.lead-inquiry')
            ->with('success', __('Your details have been updated. Our team will contact you.'));
    }

    public function cancelUpdate(PublicLeadInquiryService $service): RedirectResponse
    {
        session()->forget($service::SESSION_PENDING_CONFIRM);

        return redirect()
            ->route('public.lead-inquiry')
            ->with('info', __('You chose not to update your details. No changes were saved. Our team will contact you.'));
    }

    /**
     * @param  array{name: string, phone: string, email: string, visa_subclass: string|null, address: string|null}  $submitted
     */
    private function sendAdminNotification(bool $isNewLead, Admin $record, array $submitted): void
    {
        try {
            $to = config('public_lead_form.admin_notification_email') ?: config('mail.from.address');
            if (! empty($to)) {
                Mail::to($to)->send(new PublicLeadInquiryAdminNotification($isNewLead, $record, $submitted));
            }
        } catch (\Throwable $e) {
            Log::error('Public lead inquiry: admin email failed', ['exception' => $e]);
        }
    }
}
