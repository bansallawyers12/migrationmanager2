<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PostgreSQL does not support ALTER TABLE ... AFTER <column>.
 * The only way to reorder columns is to recreate the table.
 *
 * Target column order:
 *   id, user_id, client_id, client_matter_id, typename, type,
 *   document_type, description, allow_client, created_at, updated_at
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // 1. Create temp table with desired column order
            DB::statement('
                CREATE TABLE cp_doc_checklist_reordered (
                    id              SERIAL PRIMARY KEY,
                    user_id         INTEGER,
                    client_id       INTEGER,
                    client_matter_id BIGINT,
                    typename        VARCHAR(255),
                    type            VARCHAR(100),
                    document_type   VARCHAR(255),
                    description     TEXT,
                    allow_client    INTEGER,
                    created_at      TIMESTAMP,
                    updated_at      TIMESTAMP
                )
            ');

            // 2. Copy all existing data
            DB::statement('
                INSERT INTO cp_doc_checklist_reordered
                    (id, user_id, client_id, client_matter_id, typename, type,
                     document_type, description, allow_client, created_at, updated_at)
                SELECT
                    id, user_id, client_id, client_matter_id, typename, type,
                    document_type, description, allow_client, created_at, updated_at
                FROM cp_doc_checklist
            ');

            // 3. Sync the sequence to the current max id so auto-increment continues correctly
            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('cp_doc_checklist_reordered', 'id'),
                    COALESCE((SELECT MAX(id) FROM cp_doc_checklist_reordered), 1)
                )
            ");

            // 4. Drop original table
            DB::statement('DROP TABLE cp_doc_checklist');

            // 5. Rename temp table to the original name
            DB::statement('ALTER TABLE cp_doc_checklist_reordered RENAME TO cp_doc_checklist');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            // Restore original column order (client_matter_id back at the end)
            DB::statement('
                CREATE TABLE cp_doc_checklist_restore (
                    id              SERIAL PRIMARY KEY,
                    user_id         INTEGER,
                    client_id       INTEGER,
                    typename        VARCHAR(255),
                    type            VARCHAR(100),
                    document_type   VARCHAR(255),
                    description     TEXT,
                    allow_client    INTEGER,
                    created_at      TIMESTAMP,
                    updated_at      TIMESTAMP,
                    client_matter_id BIGINT
                )
            ');

            DB::statement('
                INSERT INTO cp_doc_checklist_restore
                    (id, user_id, client_id, typename, type,
                     document_type, description, allow_client, created_at, updated_at, client_matter_id)
                SELECT
                    id, user_id, client_id, typename, type,
                    document_type, description, allow_client, created_at, updated_at, client_matter_id
                FROM cp_doc_checklist
            ');

            DB::statement("
                SELECT setval(
                    pg_get_serial_sequence('cp_doc_checklist_restore', 'id'),
                    COALESCE((SELECT MAX(id) FROM cp_doc_checklist_restore), 1)
                )
            ");

            DB::statement('DROP TABLE cp_doc_checklist');

            DB::statement('ALTER TABLE cp_doc_checklist_restore RENAME TO cp_doc_checklist');
        });
    }
};
