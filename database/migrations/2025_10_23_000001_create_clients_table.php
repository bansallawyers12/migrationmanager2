<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the new clients table for storing client and lead information,
     * separating them from the admins (staff) table.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // Unique Identifiers
            $table->string('client_id')->unique()->nullable()->comment('Unique client identifier');
            $table->string('client_counter')->nullable()->comment('Client counter/reference number');
            
            // Type & Classification
            $table->string('type', 20)->default('client')->index()->comment('Type: client or lead');
            
            // Core Identity
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('password')->nullable()->comment('For client portal login');
            $table->string('decrypt_password')->nullable();
            $table->rememberToken();
            
            // Personal Information
            $table->date('dob')->nullable()->comment('Date of birth');
            $table->string('age', 50)->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('marital_status', 100)->nullable();
            $table->text('profile_img')->nullable();
            
            // Contact Information - Primary
            $table->string('phone', 40)->nullable();
            $table->string('country_code', 20)->nullable();
            $table->string('contact_type', 100)->nullable();
            $table->string('email_type', 50)->nullable();
            
            // Contact Information - Alternative
            $table->string('att_email')->nullable()->comment('Alternative email');
            $table->string('att_country_code', 50)->nullable();
            $table->string('att_phone', 190)->nullable();
            
            // Contact Information - Emergency
            $table->string('emergency_country_code', 25)->nullable();
            $table->string('emergency_contact_no', 25)->nullable();
            $table->string('emergency_contact_type', 50)->nullable();
            
            // Address
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('zip', 40)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            
            // Immigration/Visa Information
            $table->string('passport_number')->nullable();
            $table->string('country_passport', 200)->nullable();
            $table->string('visa_type')->nullable();
            $table->date('visaExpiry')->nullable();
            $table->date('visaGrant')->nullable();
            $table->string('visa_opt')->nullable();
            $table->text('prev_visa')->nullable();
            $table->date('preferredIntake')->nullable();
            $table->string('applications')->nullable();
            $table->integer('is_visa_expire_mail_sent')->nullable();
            
            // Verification Fields
            $table->date('dob_verified_date')->nullable();
            $table->unsignedInteger('dob_verified_by')->nullable();
            $table->date('phone_verified_date')->nullable();
            $table->unsignedInteger('phone_verified_by')->nullable();
            $table->date('visa_expiry_verified_at')->nullable();
            $table->unsignedInteger('visa_expiry_verified_by')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('dob_verify_document', 225)->nullable();
            
            // EOI/Skills Assessment - Basic
            $table->string('nomi_occupation')->nullable()->comment('Nominated occupation');
            $table->string('skill_assessment')->nullable();
            $table->string('total_points')->nullable();
            $table->string('start_process')->nullable();
            
            // EOI/Skills Assessment - Qualifications
            $table->string('high_quali_aus')->nullable()->comment('Highest qualification in Australia');
            $table->string('high_quali_overseas')->nullable()->comment('Highest qualification overseas');
            $table->string('qualification_level')->nullable();
            $table->string('qualification_name')->nullable();
            
            // EOI/Skills Assessment - Experience
            $table->string('relevant_work_exp_aus')->nullable();
            $table->string('relevant_work_exp_over')->nullable();
            $table->string('experience_job_title')->nullable();
            $table->string('experience_country')->nullable();
            
            // EOI/Skills Assessment - Language Tests
            $table->string('naati_test')->nullable();
            $table->date('naati_date')->nullable();
            $table->string('nati_language', 225)->nullable();
            $table->string('py_test')->nullable();
            $table->date('py_date')->nullable();
            $table->string('py_field', 225)->nullable();
            $table->string('naati_py')->nullable();
            
            // EOI/Skills Assessment - Partner
            $table->string('married_partner')->nullable();
            
            // EOI/Skills Assessment - Australian Study
            $table->boolean('australian_study')->default(0)->comment('Has Australian study requirement (2+ years)');
            $table->date('australian_study_date')->nullable();
            $table->boolean('specialist_education')->default(0)->comment('Has specialist education (STEM)');
            $table->date('specialist_education_date')->nullable();
            $table->boolean('regional_study')->default(0)->comment('Has regional study');
            $table->date('regional_study_date')->nullable();
            $table->string('regional_points', 225)->nullable();
            
            // CRM/Lead Management
            $table->integer('lead_id')->nullable()->comment('Legacy lead ID if migrated');
            $table->string('lead_status', 100)->nullable()->index();
            $table->integer('lead_quality')->nullable();
            $table->string('service', 200)->nullable()->comment('Service requested');
            $table->string('source')->nullable()->index()->comment('Lead source');
            $table->string('assignee')->nullable()->comment('Assigned staff member');
            $table->string('followers')->nullable()->comment('Following staff members');
            $table->text('tagname')->nullable();
            $table->text('tags')->nullable();
            $table->string('rating', 50)->nullable();
            $table->text('comments_note')->nullable();
            $table->dateTime('followup_date')->nullable();
            
            // Status & Management
            $table->string('status', 50)->default('active');
            $table->integer('verified')->default(0);
            $table->integer('is_archived')->default(0)->index();
            $table->date('archived_on')->nullable();
            $table->integer('is_deleted')->default(0)->index();
            $table->string('is_star_client', 25)->nullable();
            
            // Client Portal
            $table->integer('cp_status')->default(0)->comment('Client portal status');
            $table->string('cp_random_code', 15)->nullable();
            $table->timestamp('cp_token_generated_at')->nullable();
            $table->integer('cp_code_verify')->default(0);
            
            // Relationships/References
            $table->integer('user_id')->nullable()->comment('Related user ID');
            $table->unsignedInteger('agent_id')->nullable()->comment('Assigned agent');
            $table->unsignedInteger('office_id')->nullable()->comment('Associated office');
            $table->integer('wp_customer_id')->nullable();
            $table->integer('not_picked_call')->nullable();
            
            // Files & Documents
            $table->text('related_files')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('agent_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('dob_verified_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('phone_verified_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('visa_expiry_verified_by')->references('id')->on('admins')->onDelete('set null');
            
            // Indexes for performance
            $table->index('created_at');
            $table->index('updated_at');
            $table->index(['type', 'status']);
            $table->index(['lead_status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

