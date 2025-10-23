<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrates client and lead data from admins table to new clients table.
     * This migration should be run AFTER creating the clients table.
     */
    public function up(): void
    {
        // Get all records from admins table that are clients or leads
        // We'll identify them by type field or by presence of client-specific data
        $clientLeadRecords = DB::table('admins')
            ->where(function($query) {
                $query->where('type', 'client')
                      ->orWhere('type', 'lead')
                      ->orWhereNotNull('client_id')
                      ->orWhereNotNull('lead_id')
                      ->orWhereNotNull('lead_status');
            })
            ->get();

        echo "Found " . $clientLeadRecords->count() . " client/lead records to migrate\n";

        $migratedCount = 0;
        $skippedCount = 0;

        foreach ($clientLeadRecords as $record) {
            try {
                // Prepare data for clients table
                $clientData = [
                    // Identifiers
                    'client_id' => $record->client_id,
                    'client_counter' => $record->client_counter ?? null,
                    'type' => $record->type ?? ($record->lead_id ? 'lead' : 'client'),
                    
                    // Core Identity
                    'first_name' => $record->first_name,
                    'last_name' => $record->last_name,
                    'email' => $record->email,
                    'password' => $record->password,
                    'decrypt_password' => $record->decrypt_password ?? null,
                    'remember_token' => $record->remember_token ?? null,
                    
                    // Personal Information
                    'dob' => $record->dob ?? null,
                    'age' => $record->age ?? null,
                    'gender' => $record->gender ?? null,
                    'marital_status' => $record->marital_status ?? null,
                    'profile_img' => $record->profile_img ?? null,
                    
                    // Contact Information
                    'phone' => $record->phone ?? null,
                    'country_code' => $record->country_code ?? null,
                    'contact_type' => $record->contact_type ?? null,
                    'email_type' => $record->email_type ?? null,
                    'att_phone' => $record->att_phone ?? null,
                    'att_email' => $record->att_email ?? null,
                    'att_country_code' => $record->att_country_code ?? null,
                    'emergency_country_code' => $record->emergency_country_code ?? null,
                    'emergency_contact_no' => $record->emergency_contact_no ?? null,
                    'emergency_contact_type' => $record->emergency_contact_type ?? null,
                    
                    // Address
                    'country' => $record->country ?? null,
                    'state' => $record->state ?? null,
                    'city' => $record->city ?? null,
                    'address' => $record->address ?? null,
                    'zip' => $record->zip ?? null,
                    'latitude' => $record->latitude ?? null,
                    'longitude' => $record->longitude ?? null,
                    
                    // Immigration/Visa
                    'passport_number' => $record->passport_number ?? null,
                    'country_passport' => $record->country_passport ?? null,
                    'visa_type' => $record->visa_type ?? null,
                    'visaExpiry' => $record->visaExpiry ?? null,
                    'visaGrant' => $record->visaGrant ?? null,
                    'visa_opt' => $record->visa_opt ?? null,
                    'prev_visa' => $record->prev_visa ?? null,
                    'preferredIntake' => $record->preferredIntake ?? null,
                    'applications' => $record->applications ?? null,
                    'is_visa_expire_mail_sent' => $record->is_visa_expire_mail_sent ?? null,
                    
                    // Verification - Check if foreign keys are valid
                    'dob_verified_date' => $record->dob_verified_date ?? null,
                    'dob_verified_by' => ($record->dob_verified_by && DB::table('admins')->where('id', $record->dob_verified_by)->exists()) ? $record->dob_verified_by : null,
                    'phone_verified_date' => $record->phone_verified_date ?? null,
                    'phone_verified_by' => ($record->phone_verified_by && DB::table('admins')->where('id', $record->phone_verified_by)->exists()) ? $record->phone_verified_by : null,
                    'visa_expiry_verified_at' => $record->visa_expiry_verified_at ?? null,
                    'visa_expiry_verified_by' => ($record->visa_expiry_verified_by && DB::table('admins')->where('id', $record->visa_expiry_verified_by)->exists()) ? $record->visa_expiry_verified_by : null,
                    'email_verified_at' => $record->email_verified_at ?? null,
                    'dob_verify_document' => $record->dob_verify_document ?? null,
                    
                    // EOI/Skills Assessment
                    'nomi_occupation' => $record->nomi_occupation ?? null,
                    'skill_assessment' => $record->skill_assessment ?? null,
                    'high_quali_aus' => $record->high_quali_aus ?? null,
                    'high_quali_overseas' => $record->high_quali_overseas ?? null,
                    'relevant_work_exp_aus' => $record->relevant_work_exp_aus ?? null,
                    'relevant_work_exp_over' => $record->relevant_work_exp_over ?? null,
                    'naati_test' => $record->naati_test ?? null,
                    'py_test' => $record->py_test ?? null,
                    'naati_date' => $record->naati_date ?? null,
                    'py_date' => $record->py_date ?? null,
                    'naati_py' => $record->naati_py ?? null,
                    'married_partner' => $record->married_partner ?? null,
                    'total_points' => $record->total_points ?? null,
                    'start_process' => $record->start_process ?? null,
                    'qualification_level' => $record->qualification_level ?? null,
                    'qualification_name' => $record->qualification_name ?? null,
                    'experience_job_title' => $record->experience_job_title ?? null,
                    'experience_country' => $record->experience_country ?? null,
                    'nati_language' => $record->nati_language ?? null,
                    'py_field' => $record->py_field ?? null,
                    'regional_points' => $record->regional_points ?? null,
                    'australian_study' => $record->australian_study ?? 0,
                    'australian_study_date' => $record->australian_study_date ?? null,
                    'specialist_education' => $record->specialist_education ?? 0,
                    'specialist_education_date' => $record->specialist_education_date ?? null,
                    'regional_study' => $record->regional_study ?? 0,
                    'regional_study_date' => $record->regional_study_date ?? null,
                    
                    // CRM/Lead Management
                    'lead_id' => $record->lead_id ?? null,
                    'lead_status' => $record->lead_status ?? null,
                    'lead_quality' => $record->lead_quality ?? null,
                    'service' => $record->service ?? null,
                    'source' => $record->source ?? null,
                    'assignee' => $record->assignee ?? null,
                    'followers' => $record->followers ?? null,
                    'tagname' => $record->tagname ?? null,
                    'tags' => $record->tags ?? null,
                    'rating' => $record->rating ?? null,
                    'comments_note' => $record->comments_note ?? null,
                    'followup_date' => $record->followup_date ?? null,
                    
                    // Status
                    'status' => $record->status ?? 'active',
                    'verified' => $record->verified ?? 0,
                    'is_archived' => $record->is_archived ?? 0,
                    'archived_on' => $record->archived_on ?? null,
                    'is_deleted' => $record->is_deleted ?? 0,
                    'is_star_client' => $record->is_star_client ?? null,
                    
                    // Client Portal
                    'cp_status' => $record->cp_status ?? 0,
                    'cp_random_code' => $record->cp_random_code ?? null,
                    'cp_token_generated_at' => $record->cp_token_generated_at ?? null,
                    'cp_code_verify' => $record->cp_code_verify ?? 0,
                    
                    // Relationships - Check if foreign keys are valid
                    'user_id' => $record->user_id ?? null,
                    'agent_id' => ($record->agent_id && DB::table('admins')->where('id', $record->agent_id)->exists()) ? $record->agent_id : null,
                    'office_id' => $record->office_id ?? null,
                    'wp_customer_id' => $record->wp_customer_id ?? null,
                    'not_picked_call' => $record->not_picked_call ?? null,
                    
                    // Files
                    'related_files' => $record->related_files ?? null,
                    
                    // Timestamps
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ];

                // Insert into clients table
                $newClientId = DB::table('clients')->insertGetId($clientData);

                // Create mapping entry for reference
                DB::table('admin_to_client_mapping')->insert([
                    'old_admin_id' => $record->id,
                    'new_client_id' => $newClientId,
                    'type' => $clientData['type'],
                    'migrated_at' => now()
                ]);

                $migratedCount++;

            } catch (\Exception $e) {
                echo "Error migrating record ID {$record->id}: " . $e->getMessage() . "\n";
                $skippedCount++;
            }
        }

        echo "Migration complete: {$migratedCount} migrated, {$skippedCount} skipped\n";
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: This will delete all data from clients table!
     */
    public function down(): void
    {
        // Delete all migrated data from clients table
        DB::table('clients')->truncate();
        
        // Clear the mapping table
        DB::table('admin_to_client_mapping')->truncate();
        
        echo "Clients table data deleted. Original data still exists in admins table.\n";
    }
};

