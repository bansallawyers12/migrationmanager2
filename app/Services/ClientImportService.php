<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientVisaCountry;
use App\Models\ClientTestScore;
use App\Models\ActivitiesLog;
use App\Models\Matter;
use App\Services\ClientReferenceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ClientImportService
{
    protected $referenceService;

    public function __construct(ClientReferenceService $referenceService)
    {
        $this->referenceService = $referenceService;
    }

    /**
     * Import client data from JSON
     * 
     * @param array $importData
     * @param bool $skipDuplicates
     * @return array ['success' => bool, 'client_id' => int|null, 'message' => string]
     */
    public function importClient(array $importData, $skipDuplicates = true)
    {
        DB::beginTransaction();

        try {
            // Validate import data structure
            if (!isset($importData['client'])) {
                throw new \Exception('Invalid import file: missing client data');
            }

            $clientData = $importData['client'];

            // Check for duplicate email if skip_duplicates is enabled
            if ($skipDuplicates) {
                $email = isset($clientData['email']) ? trim((string) $clientData['email']) : '';
                $phone = isset($clientData['phone']) ? trim((string) $clientData['phone']) : '';

                $query = Admin::whereIn('type', ['client', 'lead']);
                if ($email !== '') {
                    $query->where('email', $email);
                }
                if ($phone !== '') {
                    $query->orWhere('phone', $phone);
                }

                if ($email !== '' || $phone !== '') {
                    $existingClient = $query->first();
                    if ($existingClient) {
                        DB::rollBack();
                        $match = $email !== '' && $existingClient->email === $email
                            ? 'email ' . $email
                            : 'phone ' . $phone;
                        return [
                            'success' => false,
                            'client_id' => null,
                            'message' => 'Client with same ' . $match . ' already exists. Import skipped.'
                        ];
                    }
                }
            }

            // Generate new client reference
            $reference = $this->referenceService->generateClientReference($clientData['first_name']);
            $client_id = $reference['client_id'];
            $client_current_counter = $reference['client_counter'];

            // Create the client
            $client = new Admin();
            $client->first_name = $clientData['first_name']; // Required field
            $client->last_name = $clientData['last_name'] ?? null;
            $client->email = $clientData['email']; // Required field (unique, NOT NULL)
            $client->phone = $clientData['phone'] ?? null;
            $client->country_code = $clientData['country_code'] ?? null;
            
            // Personal Information
            $client->dob = $this->parseDate($clientData['dob'] ?? null);
            $client->age = $clientData['age'] ?? null;
            $client->gender = $clientData['gender'] ?? null;
            $client->marital_status = $clientData['marital_status'] ?? null;
            
            // Address
            $client->address = $clientData['address'] ?? null;
            $client->city = $clientData['city'] ?? null;
            $client->state = $this->mapState($clientData['state'] ?? null);
            $client->country = $this->mapCountry($clientData['country'] ?? null);
            $client->zip = $clientData['zip'] ?? null;
            
            // Passport
            $client->country_passport = $clientData['country_passport'] ?? null;
            if (Schema::hasColumn('admins', 'passport_number') && isset($clientData['passport_number'])) {
                $client->passport_number = $clientData['passport_number'];
            }
            
            // Additional Contact (if exists in both systems)
            
            // Email and Contact Type (stored in admins table)
            $client->email_type = $clientData['email_type'] ?? null;
            $client->contact_type = $clientData['contact_type'] ?? null;
            
            // Optional bansalcrm2-style fields (if columns exist)
            $bansalOptional = [
                'att_email', 'att_phone', 'att_country_code',
                'nomi_occupation', 'skill_assessment', 'high_quali_aus', 'high_quali_overseas',
                'relevant_work_exp_aus', 'relevant_work_exp_over',
                'naati_py', 'total_points',
                'service', 'assignee', 'lead_quality', 'comments_note', 'married_partner',
                'tagname', 'related_files',
            ];
            foreach ($bansalOptional as $field) {
                if (Schema::hasColumn('admins', $field) && array_key_exists($field, $clientData)) {
                    $client->{$field} = $clientData[$field];
                }
            }
            if (Schema::hasColumn('admins', 'visa_type') && array_key_exists('visa_type', $clientData)) {
                $client->visa_type = $clientData['visa_type'];
            }
            if (Schema::hasColumn('admins', 'visa_opt') && array_key_exists('visa_opt', $clientData)) {
                $client->visa_opt = $clientData['visa_opt'];
            }
            if (Schema::hasColumn('admins', 'visaExpiry') && array_key_exists('visaExpiry', $clientData)) {
                $client->visaExpiry = $this->parseDate($clientData['visaExpiry']);
            }

            // Other
            $client->naati_test = $clientData['naati_test'] ?? null;
            $client->naati_date = $this->parseDate($clientData['naati_date'] ?? null);
            $client->py_test = $clientData['py_test'] ?? null;
            $client->py_date = $this->parseDate($clientData['py_date'] ?? null);
            $client->source = $clientData['source'] ?? null;
            $client->type = $clientData['type'] ?? 'client';
            $client->status = $clientData['status'] ?? 1;
            $client->agent_id = $clientData['agent_id'] ?? null;
            
            // Verification metadata (dates only, not staff IDs)
            $client->dob_verified_date = $this->parseDateTime($clientData['dob_verified_date'] ?? null);
            $client->dob_verify_document = $clientData['dob_verify_document'] ?? null;
            $client->phone_verified_date = $this->parseDateTime($clientData['phone_verified_date'] ?? null);
            $client->visa_expiry_verified_at = $this->parseDateTime($clientData['visa_expiry_verified_at'] ?? null);
            
            // System fields
            $client->client_counter = $client_current_counter;
            $client->client_id = $client_id;
            $client->password = Hash::make('CLIENT_IMPORT_' . time()); // Temporary password
            $client->verified = 0;
            $client->cp_status = 0;
            $client->cp_code_verify = 0;
            $client->australian_study = 0;
            $client->specialist_education = 0;
            $client->regional_study = 0;
            $client->is_archived = 0;
            // Note: archived_by is not set during import - imported clients are not archived
            // archived_by will be null for imported clients
            
            $client->save();
            $newClientId = $client->id;

            // Import addresses
            if (isset($importData['addresses']) && is_array($importData['addresses'])) {
                foreach ($importData['addresses'] as $addressData) {
                    ClientAddress::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'address' => $addressData['address'] ?? null,
                        'address_line_1' => $addressData['address_line_1'] ?? null,
                        'address_line_2' => $addressData['address_line_2'] ?? null,
                        'suburb' => $addressData['suburb'] ?? $addressData['city'] ?? null,
                        'state' => $addressData['state'] ?? null,
                        'country' => $addressData['country'] ?? null,
                        'zip' => $addressData['zip'] ?? null,
                        'regional_code' => $addressData['regional_code'] ?? null,
                        'start_date' => $this->parseDate($addressData['start_date'] ?? null),
                        'end_date' => $this->parseDate($addressData['end_date'] ?? null),
                        'is_current' => $addressData['is_current'] ?? 0,
                    ]);
                }
            }

            // Import contacts (phone numbers)
            if (isset($importData['contacts']) && is_array($importData['contacts'])) {
                foreach ($importData['contacts'] as $contactData) {
                    ClientContact::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'contact_type' => $contactData['contact_type'] ?? null,
                        'country_code' => $contactData['country_code'] ?? null,
                        'phone' => $contactData['phone'] ?? null,
                        'is_verified' => $contactData['is_verified'] ?? false,
                        'verified_at' => $this->parseDateTime($contactData['verified_at'] ?? null),
                    ]);
                }
            }

            // Import emails
            if (isset($importData['emails']) && is_array($importData['emails'])) {
                foreach ($importData['emails'] as $emailData) {
                    ClientEmail::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'email_type' => $emailData['email_type'] ?? null,
                        'email' => $emailData['email'] ?? null,
                        'is_verified' => $emailData['is_verified'] ?? false,
                        'verified_at' => $this->parseDateTime($emailData['verified_at'] ?? null),
                    ]);
                }
            }

            // Import passport
            if (isset($importData['passport']) && is_array($importData['passport'])) {
                ClientPassportInformation::create([
                    'client_id' => $newClientId,
                    'admin_id' => Auth::id(),
                    'passport' => $importData['passport']['passport_number'] ?? $importData['passport']['passport'] ?? null, // Support both field names
                    'passport_country' => $importData['passport']['passport_country'] ?? null,
                    'passport_issue_date' => $this->parseDate($importData['passport']['passport_issue_date'] ?? null),
                    'passport_expiry_date' => $this->parseDate($importData['passport']['passport_expiry_date'] ?? null),
                ]);
            }

            // Import travel information
            if (isset($importData['travel']) && is_array($importData['travel'])) {
                foreach ($importData['travel'] as $travelData) {
                    ClientTravelInformation::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'travel_country_visited' => $travelData['travel_country_visited'] ?? null,
                        'travel_arrival_date' => $this->parseDate($travelData['travel_arrival_date'] ?? null),
                        'travel_departure_date' => $this->parseDate($travelData['travel_departure_date'] ?? null),
                        'travel_purpose' => $travelData['travel_purpose'] ?? null,
                    ]);
                }
            }

            // Import visa countries; resolve visa_type by matter title/nick_name when provided (cross-system portability)
            $lastVisaType = null;
            $lastVisaExpiry = null;
            if (isset($importData['visa_countries']) && is_array($importData['visa_countries'])) {
                foreach ($importData['visa_countries'] as $visaData) {
                    if (!is_array($visaData)) {
                        continue;
                    }
                    $resolvedType = $this->resolveVisaType($visaData);
                    $expiry = $this->parseDate($visaData['visa_expiry_date'] ?? null);
                    $grant = $this->parseDate($visaData['visa_grant_date'] ?? null);
                    ClientVisaCountry::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'visa_type' => $resolvedType,
                        'visa_description' => $visaData['visa_description'] ?? null,
                        'visa_expiry_date' => $expiry,
                        'visa_grant_date' => $grant,
                    ]);
                    $lastVisaType = $resolvedType;
                    $lastVisaExpiry = $expiry;
                }
                // Sync last visa to client (visa_type, visaExpiry) for sidebar/summary display
                if (Schema::hasColumn('admins', 'visa_type') && Schema::hasColumn('admins', 'visaExpiry')) {
                    $client->visa_type = $lastVisaType ?? '';
                    $client->visaExpiry = $lastVisaExpiry;
                    $client->save();
                }
            }

            // Import character information
            if (isset($importData['character']) && is_array($importData['character'])) {
                foreach ($importData['character'] as $characterData) {
                    ClientCharacter::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'type_of_character' => $characterData['type_of_character'] ?? null,
                        'character_detail' => $characterData['character_detail'] ?? null,
                        'character_date' => $this->parseDate($characterData['character_date'] ?? null),
                    ]);
                }
            }

            // Import test scores (unified format: test_type, listening, reading, writing, speaking, overall_score, test_date)
            if (isset($importData['test_scores']) && is_array($importData['test_scores'])) {
                foreach ($importData['test_scores'] as $testData) {
                    ClientTestScore::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'test_type' => $testData['test_type'] ?? null,
                        'listening' => $testData['listening'] ?? null,
                        'reading' => $testData['reading'] ?? null,
                        'writing' => $testData['writing'] ?? null,
                        'speaking' => $testData['speaking'] ?? null,
                        'overall_score' => $testData['overall_score'] ?? null,
                        'test_date' => $this->parseDate($testData['test_date'] ?? null),
                        'relevant_test' => $testData['relevant_test'] ?? 1,
                    ]);
                }
            }

            // Import activities (supports both migrationmanager2 and bansalcrm2 formats)
            if (isset($importData['activities']) && is_array($importData['activities'])) {
                foreach ($importData['activities'] as $activityData) {
                    $activityAttrs = [
                        'client_id' => $newClientId,
                        'created_by' => $activityData['created_by'] ?? Auth::id(),
                        'subject' => $activityData['subject'] ?? 'Imported Activity',
                        'description' => $activityData['description'] ?? null,
                        'activity_type' => $activityData['activity_type'] ?? 'activity',
                        'followup_date' => $this->parseDateTime($activityData['followup_date'] ?? null),
                        'task_group' => $activityData['task_group'] ?? null,
                        'task_status' => $activityData['task_status'] ?? 0,
                        'pin' => $activityData['pin'] ?? 0,
                    ];
                    if (Schema::hasColumn('activities_logs', 'use_for') && array_key_exists('use_for', $activityData)) {
                        $activityAttrs['use_for'] = $activityData['use_for'];
                    }
                    ActivitiesLog::create($activityAttrs);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'client_id' => $newClientId,
                'client_id_reference' => $client_id,
                'message' => 'Client imported successfully. Client ID: ' . $client_id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Client import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'client_id' => null,
                'message' => 'Failed to import client: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse date string to Y-m-d format
     * Handles multiple date formats: Y-m-d, d/m/Y, ISO8601, etc.
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // If already in Y-m-d format, return as is
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }
            
            // Try to parse with Carbon (handles most formats)
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $date, ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse datetime string
     */
    private function parseDateTime($datetime)
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            return Carbon::parse($datetime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map state (may need conversion from string to ID or vice versa)
     */
    private function mapState($state)
    {
        // If state is already an integer ID, return as is
        if (is_numeric($state)) {
            return $state;
        }

        // If state is a string (like "New South Wales"), try to find ID
        // For now, return as string - may need to implement state mapping
        return $state;
    }

    /**
     * Map country (may need conversion from string sortname to ID)
     */
    private function mapCountry($country)
    {
        // If country is already an integer ID, return as is
        if (is_numeric($country)) {
            return $country;
        }

        // If country is a string sortname (like "AU"), try to find ID
        if (is_string($country) && strlen($country) <= 3) {
            $countryModel = \App\Models\Country::where('sortname', $country)->first();
            if ($countryModel) {
                return $countryModel->id;
            }
        }

        // Return as is if no mapping found
        return $country;
    }

    /**
     * Resolve visa_type (Matter ID) for import.
     * Prefer portable identifiers so target system (e.g. bansalcrm2) maps correctly when matter IDs differ:
     * 1. visa_type_matter_nick_name -> lookup Matter by nick_name
     * 2. visa_type_matter_title -> lookup Matter by title
     * 3. Fall back to numeric visa_type (backwards compat with older exports)
     *
     * @param array $visaData
     * @return int|string|null
     */
    private function resolveVisaType(array $visaData)
    {
        $nick = isset($visaData['visa_type_matter_nick_name']) ? trim((string) $visaData['visa_type_matter_nick_name']) : null;
        if ($nick !== null && $nick !== '') {
            $matter = Matter::where('nick_name', $nick)->first();
            if ($matter) {
                return $matter->id;
            }
        }

        $title = isset($visaData['visa_type_matter_title']) ? trim((string) $visaData['visa_type_matter_title']) : null;
        if ($title !== null && $title !== '') {
            $matter = Matter::where('title', $title)->first();
            if ($matter) {
                return $matter->id;
            }
        }

        $id = $visaData['visa_type'] ?? null;
        if ($id !== null && $id !== '' && (is_int($id) || (is_string($id) && is_numeric($id)))) {
            return is_numeric($id) ? (int) $id : $id;
        }

        $label = $visaData['visa_type'] ?? null;
        if (is_string($label)) {
            $label = trim($label);
            if ($label !== '') {
                $labelLower = mb_strtolower($label);
                $matter = Matter::whereRaw('LOWER(title) = ?', [$labelLower])
                    ->orWhereRaw('LOWER(nick_name) = ?', [$labelLower])
                    ->first();
                if ($matter) {
                    return $matter->id;
                }
            }
        }

        return null;
    }
}
