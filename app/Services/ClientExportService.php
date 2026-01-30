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
use App\Models\ActivitiesLog;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ClientExportService
{
    /**
     * Export client data to JSON format
     * 
     * @param int $clientId
     * @return array
     */
    public function exportClient($clientId)
    {
        try {
            $client = Admin::where('id', $clientId)
                ->where('role', 7) // Only clients
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            $exportData = [
                'version' => '1.0',
                'exported_at' => now()->toIso8601String(),
                'exported_from' => 'migrationmanager2',
                'client' => $this->getClientBasicData($client),
                // 'addresses' => $this->getClientAddresses($clientId), // Skipped: bansalcrm2 doesn't have separate client_addresses table - only primary address in admins table
                'contacts' => $this->getClientContacts($clientId),
                'emails' => $this->getClientEmails($clientId),
                'passport' => $this->getClientPassport($clientId),
                'travel' => $this->getClientTravel($clientId),
                'visa_countries' => $this->getClientVisaCountries($clientId),
                'character' => $this->getClientCharacter($clientId),
                'activities' => $this->getClientActivities($clientId),
            ];

            return $exportData;
        } catch (\Exception $e) {
            Log::error('Client export error: ' . $e->getMessage(), [
                'client_id' => $clientId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get basic client data (fields that exist in both systems)
     */
    private function getClientBasicData($client)
    {
        return [
            // Basic Identity
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'country_code' => $client->country_code,
            'telephone' => $client->telephone ?? null,
            
            // Personal Information
            'dob' => $client->dob,
            'age' => $client->age,
            'gender' => $client->gender,
            'marital_status' => $client->marital_status,
            
            // Address
            'address' => $client->address,
            'city' => $client->city,
            'state' => $client->state,
            'country' => $client->country,
            'zip' => $client->zip,
            
            // Passport
            'country_passport' => $client->country_passport ?? null,
            
            // Additional Contact (if exists in both systems)
            'att_email' => $client->att_email ?? null,
            'att_phone' => $client->att_phone ?? null,
            'att_country_code' => $client->att_country_code ?? null,
            
            // Email and Contact Type (stored in admins table)
            'email_type' => $client->email_type ?? null,
            'contact_type' => $client->contact_type ?? null,
            
            // Other
            'source' => $client->source,
            'type' => $client->type,
            'status' => $client->status,
            'profile_img' => $client->profile_img,
            'agent_id' => $client->agent_id ?? null,
        ];
    }

    /**
     * Get client addresses
     */
    private function getClientAddresses($clientId)
    {
        return ClientAddress::where('client_id', $clientId)
            ->get()
            ->map(function ($address) {
                return [
                    'address' => $address->address,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'suburb' => $address->suburb,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country,
                    'zip' => $address->zip,
                    'regional_code' => $address->regional_code,
                    'start_date' => $address->start_date,
                    'end_date' => $address->end_date,
                    'is_current' => $address->is_current,
                ];
            })
            ->toArray();
    }

    /**
     * Get client contacts (phone numbers)
     */
    private function getClientContacts($clientId)
    {
        return ClientContact::where('client_id', $clientId)
            ->get()
            ->map(function ($contact) {
                return [
                    'contact_type' => $contact->contact_type,
                    'country_code' => $contact->country_code,
                    'phone' => $contact->phone,
                    'is_verified' => $contact->is_verified,
                    'verified_at' => $contact->verified_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get client emails
     */
    private function getClientEmails($clientId)
    {
        return ClientEmail::where('client_id', $clientId)
            ->get()
            ->map(function ($email) {
                return [
                    'email_type' => $email->email_type,
                    'email' => $email->email,
                    'is_verified' => $email->is_verified,
                    'verified_at' => $email->verified_at,
                ];
            })
            ->toArray();
    }

    /**
     * Get client passport information
     */
    private function getClientPassport($clientId)
    {
        $passport = ClientPassportInformation::where('client_id', $clientId)->first();
        
        if (!$passport) {
            return null;
        }

        return [
            'passport_number' => $passport->passport, // Field is 'passport' in DB but represents passport_number
            'passport_country' => $passport->passport_country,
            'passport_issue_date' => $passport->passport_issue_date,
            'passport_expiry_date' => $passport->passport_expiry_date,
        ];
    }

    /**
     * Get client travel information
     */
    private function getClientTravel($clientId)
    {
        return ClientTravelInformation::where('client_id', $clientId)
            ->get()
            ->map(function ($travel) {
                return [
                    'travel_country_visited' => $travel->travel_country_visited,
                    'travel_arrival_date' => $travel->travel_arrival_date,
                    'travel_departure_date' => $travel->travel_departure_date,
                    'travel_purpose' => $travel->travel_purpose,
                ];
            })
            ->toArray();
    }

    /**
     * Get client visa countries.
     * Exports visa_type (Matter ID), plus portable visa_type_matter_title and visa_type_matter_nick_name
     * so import can resolve correct Matter in target system (e.g. bansalcrm2) when IDs differ.
     * visa_expiry_date is normalised to Y-m-d for consistent import.
     */
    private function getClientVisaCountries($clientId)
    {
        return ClientVisaCountry::with('matter')
            ->where('client_id', $clientId)
            ->orderBy('id')
            ->get()
            ->map(function ($visa) {
                $matter = $visa->matter;
                $expiry = $visa->visa_expiry_date;
                if ($expiry instanceof \DateTimeInterface) {
                    $expiry = $expiry->format('Y-m-d');
                } elseif (is_string($expiry) && $expiry !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry)) {
                    try {
                        $expiry = Carbon::parse($expiry)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiry = $visa->visa_expiry_date;
                    }
                }
                $grant = $visa->visa_grant_date;
                if ($grant instanceof \DateTimeInterface) {
                    $grant = $grant->format('Y-m-d');
                } elseif (is_string($grant) && $grant !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $grant)) {
                    try {
                        $grant = Carbon::parse($grant)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $grant = $visa->visa_grant_date;
                    }
                }
                return [
                    'visa_country' => $visa->visa_country,
                    'visa_type' => $visa->visa_type,
                    'visa_type_matter_title' => $matter ? $matter->title : null,
                    'visa_type_matter_nick_name' => $matter ? $matter->nick_name : null,
                    'visa_description' => $visa->visa_description,
                    'visa_expiry_date' => $expiry ?: null,
                    'visa_grant_date' => $grant ?: null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client character information
     */
    private function getClientCharacter($clientId)
    {
        return ClientCharacter::where('client_id', $clientId)
            ->get()
            ->map(function ($character) {
                return [
                    'type_of_character' => $character->type_of_character,
                    'character_detail' => $character->character_detail,
                    'character_date' => $character->character_date,
                ];
            })
            ->toArray();
    }

    /**
     * Get client activities (if structure matches)
     */
    private function getClientActivities($clientId)
    {
        return ActivitiesLog::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->limit(100) // Limit to recent 100 activities
            ->get()
            ->map(function ($activity) {
                return [
                    'subject' => $activity->subject,
                    'description' => $activity->description,
                    'activity_type' => $activity->activity_type,
                    'followup_date' => $activity->followup_date,
                    'task_group' => $activity->task_group,
                    'task_status' => $activity->task_status,
                    'created_at' => $activity->created_at,
                ];
            })
            ->toArray();
    }
}
