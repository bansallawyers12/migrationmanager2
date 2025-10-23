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
     * Updates foreign key references in related tables from admins table to clients table.
     * Uses the admin_to_client_mapping table to translate IDs.
     */
    public function up(): void
    {
        echo "Starting foreign key updates...\n";

        // Table: forms_956
        // Update client_id references (if they reference admins.id)
        if (Schema::hasTable('forms_956')) {
            $updated = DB::statement("
                UPDATE forms_956 f
                INNER JOIN admin_to_client_mapping m ON f.client_id = m.old_admin_id
                SET f.client_id = m.new_client_id
                WHERE f.client_id IS NOT NULL
            ");
            echo "Updated forms_956 table\n";
        }

        // Table: client_eoi_references
        if (Schema::hasTable('client_eoi_references')) {
            $updated = DB::statement("
                UPDATE client_eoi_references c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_eoi_references table\n";
        }

        // Table: client_test_scores
        if (Schema::hasTable('client_test_scores')) {
            $updated = DB::statement("
                UPDATE client_test_scores c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_test_scores table\n";
        }

        // Table: client_experiences
        if (Schema::hasTable('client_experiences')) {
            $updated = DB::statement("
                UPDATE client_experiences c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_experiences table\n";
        }

        // Table: client_qualifications
        if (Schema::hasTable('client_qualifications')) {
            $updated = DB::statement("
                UPDATE client_qualifications c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_qualifications table\n";
        }

        // Table: client_spouse_details
        if (Schema::hasTable('client_spouse_details')) {
            $updated = DB::statement("
                UPDATE client_spouse_details c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_spouse_details table\n";
        }

        // Table: client_occupations
        if (Schema::hasTable('client_occupations')) {
            $updated = DB::statement("
                UPDATE client_occupations c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_occupations table\n";
        }

        // Table: client_relationships
        if (Schema::hasTable('client_relationships')) {
            $updated = DB::statement("
                UPDATE client_relationships c
                INNER JOIN admin_to_client_mapping m ON c.client_id = m.old_admin_id
                SET c.client_id = m.new_client_id
                WHERE c.client_id IS NOT NULL
            ");
            echo "Updated client_relationships table\n";
        }

        // Table: lead_followups
        // Update lead_id to reference clients table
        if (Schema::hasTable('lead_followups')) {
            $updated = DB::statement("
                UPDATE lead_followups l
                INNER JOIN admin_to_client_mapping m ON l.lead_id = m.old_admin_id
                SET l.lead_id = m.new_client_id
                WHERE l.lead_id IS NOT NULL
            ");
            echo "Updated lead_followups.lead_id\n";
        }

        // Table: documents
        // Update client_id if it exists and references clients (not staff)
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'client_id')) {
            $updated = DB::statement("
                UPDATE documents d
                INNER JOIN admin_to_client_mapping m ON d.client_id = m.old_admin_id
                SET d.client_id = m.new_client_id
                WHERE d.client_id IS NOT NULL
            ");
            echo "Updated documents.client_id\n";
        }

        // Table: sms_log
        // This table might reference both staff and clients, so we need to be careful
        // Only update if the reference is to a client/lead
        if (Schema::hasTable('sms_log')) {
            if (Schema::hasColumn('sms_log', 'client_id')) {
                $updated = DB::statement("
                    UPDATE sms_log s
                    INNER JOIN admin_to_client_mapping m ON s.client_id = m.old_admin_id
                    SET s.client_id = m.new_client_id
                    WHERE s.client_id IS NOT NULL
                ");
                echo "Updated sms_log.client_id\n";
            }
            
            if (Schema::hasColumn('sms_log', 'user_id')) {
                // Only update user_id if it references a client, not staff
                $updated = DB::statement("
                    UPDATE sms_log s
                    INNER JOIN admin_to_client_mapping m ON s.user_id = m.old_admin_id
                    SET s.user_id = m.new_client_id
                    WHERE s.user_id IS NOT NULL AND m.type IN ('client', 'lead')
                ");
                echo "Updated sms_log.user_id (for clients only)\n";
            }
        }

        // Table: email_uploads (if exists)
        if (Schema::hasTable('email_uploads') && Schema::hasColumn('email_uploads', 'user_id')) {
            $updated = DB::statement("
                UPDATE email_uploads e
                INNER JOIN admin_to_client_mapping m ON e.user_id = m.old_admin_id
                SET e.user_id = m.new_client_id
                WHERE e.user_id IS NOT NULL AND m.type IN ('client', 'lead')
            ");
            echo "Updated email_uploads.user_id (for clients only)\n";
        }

        // Table: invoices (if exists)
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'client_id')) {
            $updated = DB::statement("
                UPDATE invoices i
                INNER JOIN admin_to_client_mapping m ON i.client_id = m.old_admin_id
                SET i.client_id = m.new_client_id
                WHERE i.client_id IS NOT NULL
            ");
            echo "Updated invoices.client_id\n";
        }

        echo "Foreign key updates complete!\n";
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: Cannot easily reverse this migration as it would require
     * keeping track of all original values. Use database backup to restore.
     */
    public function down(): void
    {
        echo "WARNING: Cannot automatically reverse foreign key updates.\n";
        echo "Please restore from database backup if needed.\n";
    }
};

