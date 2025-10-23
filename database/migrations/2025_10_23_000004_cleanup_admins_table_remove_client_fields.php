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
     * Cleans up the admins table by:
     * 1. Deleting client/lead records
     * 2. Dropping client/lead specific columns
     * 3. Keeping only staff-related fields
     */
    public function up(): void
    {
        echo "Starting admins table cleanup...\n";

        // Step 1: Delete migrated client/lead records from admins table
        $deletedCount = DB::table('admins')
            ->whereIn('id', function($query) {
                $query->select('old_admin_id')
                      ->from('admin_to_client_mapping');
            })
            ->delete();
        
        echo "Deleted {$deletedCount} client/lead records from admins table\n";

        // Step 2: Drop client/lead specific columns
        Schema::table('admins', function (Blueprint $table) {
            // Drop client identifiers
            if (Schema::hasColumn('admins', 'client_id')) {
                $table->dropColumn('client_id');
            }
            if (Schema::hasColumn('admins', 'client_counter')) {
                $table->dropColumn('client_counter');
            }
            if (Schema::hasColumn('admins', 'lead_id')) {
                $table->dropColumn('lead_id');
            }
            
            // Drop type column (no longer needed, all are staff)
            if (Schema::hasColumn('admins', 'type')) {
                $table->dropColumn('type');
            }
            
            // Drop immigration/visa fields
            if (Schema::hasColumn('admins', 'passport_number')) {
                $table->dropColumn([
                    'passport_number',
                    'country_passport',
                    'visa_type',
                    'visaExpiry',
                    'visaGrant',
                    'visa_opt',
                    'prev_visa',
                    'preferredIntake',
                    'applications',
                    'is_visa_expire_mail_sent'
                ]);
            }
            
            // Drop personal client info
            if (Schema::hasColumn('admins', 'dob')) {
                $table->dropColumn('dob');
            }
            if (Schema::hasColumn('admins', 'age')) {
                $table->dropColumn('age');
            }
            if (Schema::hasColumn('admins', 'gender')) {
                $table->dropColumn('gender');
            }
            
            // Note: Keep marital_status as it might be needed for staff too
            
            // Drop alternative contact fields (att_*)
            if (Schema::hasColumn('admins', 'att_phone')) {
                $table->dropColumn([
                    'att_phone',
                    'att_email',
                    'att_country_code'
                ]);
            }
            
            // Drop emergency contact fields
            if (Schema::hasColumn('admins', 'emergency_country_code')) {
                $table->dropColumn([
                    'emergency_country_code',
                    'emergency_contact_no',
                    'emergency_contact_type'
                ]);
            }
            
            // Drop EOI/Skills assessment fields
            if (Schema::hasColumn('admins', 'nomi_occupation')) {
                $table->dropColumn([
                    'nomi_occupation',
                    'skill_assessment',
                    'high_quali_aus',
                    'high_quali_overseas',
                    'relevant_work_exp_aus',
                    'relevant_work_exp_over',
                    'naati_test',
                    'py_test',
                    'naati_date',
                    'py_date',
                    'naati_py',
                    'married_partner',
                    'total_points',
                    'start_process',
                    'qualification_level',
                    'qualification_name',
                    'experience_job_title',
                    'experience_country',
                    'nati_language',
                    'py_field',
                    'regional_points'
                ]);
            }
            
            // Drop Australian study fields
            if (Schema::hasColumn('admins', 'australian_study')) {
                $table->dropColumn([
                    'australian_study',
                    'australian_study_date',
                    'specialist_education',
                    'specialist_education_date',
                    'regional_study',
                    'regional_study_date'
                ]);
            }
            
            // Drop CRM/Lead management fields
            if (Schema::hasColumn('admins', 'lead_status')) {
                $table->dropColumn([
                    'lead_status',
                    'lead_quality',
                    'service',
                    'source',
                    'assignee',
                    'followers',
                    'tagname',
                    'tags',
                    'rating',
                    'comments_note',
                    'followup_date'
                ]);
            }
            
            // Drop client-specific status fields
            if (Schema::hasColumn('admins', 'is_archived')) {
                $table->dropColumn([
                    'is_archived',
                    'archived_on',
                    'is_deleted',
                    'is_star_client'
                ]);
            }
            
            // Drop verification fields (client-specific)
            if (Schema::hasColumn('admins', 'dob_verified_date')) {
                $table->dropColumn([
                    'dob_verified_date',
                    'dob_verified_by',
                    'phone_verified_date',
                    'phone_verified_by',
                    'dob_verify_document'
                ]);
            }
            
            // Keep visa_expiry_verified_at and visa_expiry_verified_by as staff might verify documents
            
            // Drop contact type fields (client-specific)
            if (Schema::hasColumn('admins', 'contact_type')) {
                $table->dropColumn([
                    'contact_type',
                    'email_type'
                ]);
            }
            
            // Drop misc client fields
            if (Schema::hasColumn('admins', 'wp_customer_id')) {
                $table->dropColumn([
                    'wp_customer_id',
                    'not_picked_call'
                ]);
            }
            
            // Drop user_id if it exists (ambiguous relationship)
            if (Schema::hasColumn('admins', 'user_id')) {
                $table->dropColumn('user_id');
            }
            
            // Drop related_files (move to documents system)
            if (Schema::hasColumn('admins', 'related_files')) {
                $table->dropColumn('related_files');
            }
            
            // Drop latitude/longitude (keep only in clients for mapping)
            if (Schema::hasColumn('admins', 'latitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }
        });

        echo "Dropped client/lead specific columns from admins table\n";
        echo "Admins table cleanup complete!\n";
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: This will add back the columns but NOT restore the data!
     * Use database backup to fully restore.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Add back essential columns only (cannot restore all data)
            $table->string('client_id')->nullable();
            $table->string('type', 20)->nullable();
            $table->integer('lead_id')->nullable();
            $table->string('lead_status', 100)->nullable();
            $table->integer('is_archived')->default(0);
            $table->integer('is_deleted')->default(0);
            
            // Other columns omitted for brevity
            // Full restoration requires database backup
        });
        
        echo "WARNING: Columns added back but data NOT restored.\n";
        echo "Please restore from database backup for full recovery.\n";
    }
};

