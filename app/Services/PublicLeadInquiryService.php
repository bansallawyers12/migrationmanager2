<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientContact;
use App\Models\Matter;
use App\Models\ClientEmail;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Public web form: create a new lead or update an existing client/lead (after confirm).
 */
class PublicLeadInquiryService
{
    /** @var string Type label stored in `client_contacts.contact_type` for this form. */
    public const FORM_CONTACT_TYPE = 'Personal';

    /** @var string Type label stored in `client_emails.email_type` for this form. */
    public const FORM_EMAIL_TYPE = 'Personal';

    public const SESSION_PENDING_CONFIRM = 'public_lead_form_pending_confirm';

    public function __construct(
        protected ClientReferenceService $clientReferenceService
    ) {
    }

    /**
     * Normalise fields the same way as create/update.
     */
    public function normaliseProposed(
        string $name,
        string $phone,
        string $email,
        ?string $visaSubclass,
        ?string $address
    ): array {
        $email = trim($email);
        $phone = trim($phone);
        if ($address !== null) {
            $address = trim($address);
        }
        if ($visaSubclass !== null) {
            $visaSubclass = trim($visaSubclass) ?: null;
        }
        $name = trim($name);

        return [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'visa_subclass' => $visaSubclass,
        ];
    }

    /**
     * Snapshot of what is currently stored (for the confirm screen).
     *
     * @return array{name: string, email: string, phone: string, visa_subclass: string|null, address: string|null}
     */
    public function existingRecordForDisplay(Admin $admin): array
    {
        $rawVisa = $admin->visa_type;
        if ($rawVisa !== null && $rawVisa !== '') {
            $rawVisa = (string) $rawVisa;
        } else {
            $rawVisa = null;
        }

        return [
            'name' => trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? '')) ?: '—',
            'email' => (string) ($admin->email ?? ''),
            'phone' => (string) ($admin->phone ?? ''),
            'visa_subclass' => $this->formatVisaTypeForDisplay($rawVisa),
            'address' => $admin->address !== null && $admin->address !== '' ? (string) $admin->address : null,
        ];
    }

    /**
     * When `admins.visa_type` stores a `matters.id`, show the matter label (nick_name or title); otherwise the raw value.
     */
    public function formatVisaTypeForDisplay(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $t = trim($raw);
        if ($t === '') {
            return null;
        }
        $matterId = filter_var($t, FILTER_VALIDATE_INT);
        if ($matterId !== false && $matterId > 0) {
            $matter = Matter::query()
                ->select('id', 'title', 'nick_name')
                ->whereKey($matterId)
                ->first();
            if ($matter) {
                $label = trim((string) ($matter->nick_name ?? '')) ?: trim((string) ($matter->title ?? ''));

                return $label !== '' ? $label : $t;
            }
        }

        return $t;
    }

    /**
     * Create a new lead (public form, no match on email/phone).
     */
    public function createNewLead(
        string $name,
        string $phone,
        string $email,
        ?string $visaSubclass,
        ?string $address
    ): Admin {
        $row = $this->normaliseProposed($name, $phone, $email, $visaSubclass, $address);
        $name = $row['name'];
        $phone = $row['phone'];
        $email = $row['email'];
        $address = $row['address'] ?? null;
        $visaSubclass = $row['visa_subclass'] ?? null;

        $assigneeStaffId = $this->resolveDefaultAssigneeStaffId();
        $firstLast = $this->splitName($name);

        DB::beginTransaction();
        try {
            $ref = $this->clientReferenceService->generateClientReference($firstLast['first_name'] ?: 'Lead');

            $adminData = [
                'user_id' => $assigneeStaffId,
                'password' => Hash::make('LEAD_PLACEHOLDER'),
                'client_counter' => $ref['client_counter'],
                'client_id' => $ref['client_id'],
                'status' => LeadFollowUpNoteService::adminsStatusForLeadStatus('new'),
                'lead_status' => 'new',
                'followup_date' => null,
                'type' => 'lead',
                'is_archived' => 0,
                'is_deleted' => null,
                'verified' => 0,
                'cp_status' => 0,
                'cp_code_verify' => 0,
                'australian_study' => 0,
                'specialist_education' => 0,
                'regional_study' => 0,
                'is_company' => 0,
                'first_name' => $firstLast['first_name'] ?: 'Unknown',
                'last_name' => $firstLast['last_name'] ?: '',
                'gender' => null,
                'dob' => null,
                'marital_status' => null,
                'phone' => $phone,
                'email' => $email,
                'address' => $address ?: null,
                'visa_type' => $visaSubclass ?: null,
                'manual_form_fill' => 1,
                'source' => 'public_lead_inquiry',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $adminId = DB::table('admins')->insertGetId($adminData);
            $admin = Admin::query()->findOrFail($adminId);

            if ($phone) {
                ClientContact::create([
                    'admin_id' => $assigneeStaffId,
                    'client_id' => $admin->id,
                    'contact_type' => self::FORM_CONTACT_TYPE,
                    'phone' => $phone,
                    'country_code' => null,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($email) {
                ClientEmail::create([
                    'admin_id' => $assigneeStaffId,
                    'client_id' => $admin->id,
                    'email_type' => self::FORM_EMAIL_TYPE,
                    'email' => $email,
                    'is_verified' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $lead = \App\Models\Lead::query()->find($admin->id);
            if ($lead) {
                app(LeadFollowUpNoteService::class)->syncNotesForLead($lead, null);
            }

            DB::commit();

            return $admin->fresh();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Public lead inquiry: failed to create lead', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Apply form values to an existing client/lead (after user confirms).
     */
    public function applyUpdateToExisting(Admin $existing, string $name, string $phone, string $email, ?string $visaSubclass, ?string $address): Admin
    {
        $row = $this->normaliseProposed($name, $phone, $email, $visaSubclass, $address);
        $name = $row['name'];
        $phone = $row['phone'];
        $email = $row['email'];
        $address = $row['address'] ?? null;
        $visaSubclass = $row['visa_subclass'] ?? null;

        DB::transaction(function () use ($existing, $name, $email, $phone, $address, $visaSubclass): void {
            $this->assertEmailUniqueForUpdate($existing->id, $email);

            $firstLast = $this->splitName($name);
            $existing->first_name = $firstLast['first_name'];
            $existing->last_name = $firstLast['last_name'];
            $existing->phone = $phone;
            $existing->email = $email;
            $existing->address = ($address !== null && $address !== '') ? $address : $existing->address;
            if ($visaSubclass !== null) {
                $existing->visa_type = $visaSubclass;
            }
            $existing->manual_form_fill = 1;
            $existing->updated_at = now();
            $existing->save();

            $assigneeStaffId = $this->resolveDefaultAssigneeStaffId();
            $this->syncPersonalClientContact($existing->id, $assigneeStaffId, $phone);
            $this->syncPersonalClientEmail($existing->id, $assigneeStaffId, $email);
        });

        $fresh = $existing->fresh();
        if (! $fresh) {
            throw new \RuntimeException('Client record not found after update.');
        }

        return $fresh;
    }

    public function findClientOrLeadByEmailOrPhone(string $emailLower, string $phone): ?Admin
    {
        $q = Admin::query()
            ->whereIn('type', ['client', 'lead'])
            ->whereNull('is_deleted')
            ->where(function ($q) use ($emailLower, $phone) {
                $q->whereRaw('LOWER(TRIM(email)) = ?', [$emailLower]);
                if ($phone !== '') {
                    $q->orWhere('phone', $phone);
                }
            });

        return $q->orderBy('id')->first();
    }

    /**
     * @return array{first_name: string, last_name: string}
     */
    public function splitName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['first_name' => '', 'last_name' => ''];
        }
        $parts = preg_split('/\s+/', $name, 2, PREG_SPLIT_NO_EMPTY);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Ensure `client_contacts` has a Personal row for this lead/client with the submitted phone.
     */
    protected function syncPersonalClientContact(int $clientId, int $assigneeStaffId, string $phone): void
    {
        if ($phone === '') {
            return;
        }
        $personal = ClientContact::query()
            ->where('client_id', $clientId)
            ->where('contact_type', self::FORM_CONTACT_TYPE)
            ->first();
        if ($personal) {
            $personal->phone = $phone;
            $personal->admin_id = $assigneeStaffId;
            $personal->is_verified = false;
            $personal->save();

            return;
        }
        $legacy = ClientContact::query()
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->first();
        if ($legacy) {
            $legacy->contact_type = self::FORM_CONTACT_TYPE;
            $legacy->phone = $phone;
            $legacy->admin_id = $assigneeStaffId;
            $legacy->save();

            return;
        }
        ClientContact::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'contact_type' => self::FORM_CONTACT_TYPE,
            'phone' => $phone,
            'country_code' => null,
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Ensure `client_emails` has a Personal row for this lead/client with the submitted email.
     */
    protected function syncPersonalClientEmail(int $clientId, int $assigneeStaffId, string $email): void
    {
        if ($email === '') {
            return;
        }
        $emailLower = mb_strtolower($email);
        $personal = ClientEmail::query()
            ->where('client_id', $clientId)
            ->where('email_type', self::FORM_EMAIL_TYPE)
            ->first();
        if ($personal) {
            $personal->email = $email;
            $personal->admin_id = $assigneeStaffId;
            if ($personal->is_verified === null) {
                $personal->is_verified = 0;
            }
            $personal->save();

            return;
        }
        $byEmail = ClientEmail::query()
            ->where('client_id', $clientId)
            ->whereRaw('LOWER(TRIM(email)) = ?', [$emailLower])
            ->orderBy('id')
            ->first();
        if ($byEmail) {
            $byEmail->email_type = self::FORM_EMAIL_TYPE;
            $byEmail->email = $email;
            $byEmail->admin_id = $assigneeStaffId;
            if ($byEmail->is_verified === null) {
                $byEmail->is_verified = 0;
            }
            $byEmail->save();

            return;
        }
        $legacy = ClientEmail::query()
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->first();
        if ($legacy) {
            $legacy->email_type = self::FORM_EMAIL_TYPE;
            $legacy->email = $email;
            $legacy->admin_id = $assigneeStaffId;
            if ($legacy->is_verified === null) {
                $legacy->is_verified = 0;
            }
            $legacy->save();

            return;
        }
        ClientEmail::create([
            'admin_id' => $assigneeStaffId,
            'client_id' => $clientId,
            'email_type' => self::FORM_EMAIL_TYPE,
            'email' => $email,
            'is_verified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function assertEmailUniqueForUpdate(int $id, string $email): void
    {
        $exists = Admin::query()
            ->where('id', '!=', $id)
            ->where('email', $email)
            ->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'email' => [__('This email is already in use. Please use the email on file or contact us.')],
            ]);
        }
    }

    public function resolveDefaultAssigneeStaffId(): int
    {
        $configured = config('public_lead_form.default_assignee_staff_id');
        if ($configured !== null && $configured !== '') {
            $sid = (int) $configured;
            if (Staff::query()->whereKey($sid)->exists()) {
                return $sid;
            }
        }
        $first = Staff::query()->where('status', 1)->orderBy('id')->value('id');
        if ($first) {
            return (int) $first;
        }

        return Staff::query()->orderBy('id')->value('id') ?? 1;
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'visa_subclass' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
