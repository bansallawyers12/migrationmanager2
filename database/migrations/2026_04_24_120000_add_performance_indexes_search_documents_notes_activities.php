<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private bool $trgmEnabled = false;

    /**
     * Complements prior migrations (client_matters, email_log_attachments, account_client_receipts,
     * notifications). Adds: pg_trgm search indexes, document folder lookups, note/action counts,
     * activity log task filters, admin list by type, exempt-grant day lookup.
     * Application behavior is unchanged; only plans and I/O for existing queries.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->upNonPgsql();
            return;
        }

        $this->tryEnablePgTrgm();
        if ($this->trgmEnabled) {
            $this->createGinTrgmForText('admins', 'first_name', 'admins_first_name_gin_trgm_idx');
            $this->createGinTrgmForText('admins', 'last_name', 'admins_last_name_gin_trgm_idx');
            $this->createGinTrgmForText('admins', 'email', 'admins_email_gin_trgm_idx');
            $this->createGinTrgmForText('admins', 'phone', 'admins_phone_gin_trgm_idx');
            $this->createGinTrgmForText('client_contacts', 'phone', 'client_contacts_phone_gin_trgm_idx');
            $this->createGinTrgmForText('client_emails', 'email', 'client_emails_email_gin_trgm_idx');
            if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'company_name')) {
                $this->createGinTrgmForText('companies', 'company_name', 'companies_company_name_gin_trgm_idx');
            }
        }

        $this->upDocuments();
        $this->upNotes();
        $this->upActivitiesLogs();
        $this->upAdmins();
        $this->upClientAccessGrantsPartial();
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $all = [
                'cag_exempt_dedup_support_idx',
                'admins_type_is_archived_idx',
                'activities_logs_task_status_created_by_created_at_idx',
                'activities_logs_task_status_client_id_created_at_idx',
                'idx_notes_type_client_id_action_status',
                'idx_notes_assigned_type_action_status',
                'idx_notes_app_count_cover',
                'documents_client_folder_type_updated_at_idx',
                'companies_company_name_gin_trgm_idx',
                'client_emails_email_gin_trgm_idx',
                'client_contacts_phone_gin_trgm_idx',
                'admins_phone_gin_trgm_idx',
                'admins_email_gin_trgm_idx',
                'admins_last_name_gin_trgm_idx',
                'admins_first_name_gin_trgm_idx',
            ];
            foreach ($all as $idx) {
                if (! $this->pgIndexExists($idx)) {
                    continue;
                }
                DB::statement('DROP INDEX IF EXISTS '.$this->quoteId($idx));
            }
        } else {
            if (Schema::hasTable('notes')) {
                Schema::table('notes', function (Blueprint $table) {
                    if (Schema::hasIndex('notes', 'idx_notes_type_client_id_action_status')) {
                        $table->dropIndex('idx_notes_type_client_id_action_status');
                    }
                    if (Schema::hasIndex('notes', 'idx_notes_assigned_type_action_status')) {
                        $table->dropIndex('idx_notes_assigned_type_action_status');
                    }
                    if (Schema::hasIndex('notes', 'idx_notes_app_count_cover')) {
                        $table->dropIndex('idx_notes_app_count_cover');
                    }
                });
            }
            if (Schema::hasTable('activities_logs')) {
                Schema::table('activities_logs', function (Blueprint $table) {
                    if (Schema::hasIndex('activities_logs', 'activities_logs_task_status_created_by_created_at_idx')) {
                        $table->dropIndex('activities_logs_task_status_created_by_created_at_idx');
                    }
                    if (Schema::hasIndex('activities_logs', 'activities_logs_task_status_client_id_created_at_idx')) {
                        $table->dropIndex('activities_logs_task_status_client_id_created_at_idx');
                    }
                });
            }
            if (Schema::hasTable('admins')) {
                Schema::table('admins', function (Blueprint $table) {
                    if (Schema::hasIndex('admins', 'admins_type_is_archived_idx')) {
                        $table->dropIndex('admins_type_is_archived_idx');
                    }
                });
            }
            if (Schema::hasTable('documents') && Schema::hasIndex('documents', 'documents_client_folder_type_updated_at_idx')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->dropIndex('documents_client_folder_type_updated_at_idx');
                });
            }
        }
    }

    private function upNonPgsql(): void
    {
        if (Schema::hasTable('documents')) {
            $reqDoc = ['client_id', 'not_used_doc', 'type', 'doc_type', 'folder_name', 'updated_at'];
            $allCols = true;
            foreach ($reqDoc as $c) {
                if (! Schema::hasColumn('documents', $c)) {
                    $allCols = false;
                    break;
                }
            }
            if ($allCols && ! Schema::hasIndex('documents', 'documents_client_folder_type_updated_at_idx')) {
                Schema::table('documents', function (Blueprint $table) {
                    $table->index(
                        ['client_id', 'not_used_doc', 'type', 'doc_type', 'folder_name', 'updated_at'],
                        'documents_client_folder_type_updated_at_idx'
                    );
                });
            }
        }

        $this->upNotes();
        $this->upActivitiesLogs();
        $this->upAdmins();
    }

    private function tryEnablePgTrgm(): void
    {
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
            $this->trgmEnabled = (bool) DB::selectOne("SELECT 1 AS ok FROM pg_extension WHERE extname = 'pg_trgm'");
        } catch (\Throwable) {
            $this->trgmEnabled = false;
        }
    }

    private function createGinTrgmForText(string $table, string $column, string $indexName): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }
        if ($this->pgIndexExists($indexName)) {
            return;
        }
        $t = $this->quoteId($table);
        $c = $this->quoteId($column);
        $i = $this->quoteId($indexName);
        DB::statement("CREATE INDEX {$i} ON {$t} USING gin ({$c} gin_trgm_ops)");
    }

    private function upDocuments(): void
    {
        if (! Schema::hasTable('documents')) {
            return;
        }
        $required = ['client_id', 'type', 'doc_type', 'folder_name', 'updated_at'];
        foreach ($required as $col) {
            if (! Schema::hasColumn('documents', $col)) {
                return;
            }
        }
        if (! Schema::hasColumn('documents', 'not_used_doc')) {
            return;
        }
        if (Schema::hasIndex('documents', 'documents_client_folder_type_updated_at_idx')) {
            return;
        }
        if (DB::getDriverName() === 'pgsql') {
            // Quote "type" (reserved keyword in some contexts).
            DB::statement(
                'CREATE INDEX documents_client_folder_type_updated_at_idx ON documents '
                . '(client_id, not_used_doc, "type", doc_type, folder_name, updated_at DESC)'
            );
        } else {
            Schema::table('documents', function (Blueprint $table) {
                $table->index(
                    ['client_id', 'not_used_doc', 'type', 'doc_type', 'folder_name', 'updated_at'],
                    'documents_client_folder_type_updated_at_idx'
                );
            });
        }
    }

    private function upNotes(): void
    {
        if (! Schema::hasTable('notes')) {
            return;
        }
        if (! Schema::hasColumn('notes', 'type') || ! Schema::hasColumn('notes', 'client_id')
            || ! Schema::hasColumn('notes', 'is_action') || ! Schema::hasColumn('notes', 'status')) {
            return;
        }

        if (! Schema::hasIndex('notes', 'idx_notes_type_client_id_action_status')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->index(
                    ['type', 'client_id', 'is_action', 'status'],
                    'idx_notes_type_client_id_action_status'
                );
            });
        }

        if (Schema::hasColumn('notes', 'assigned_to') && ! Schema::hasIndex('notes', 'idx_notes_assigned_type_action_status')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->index(
                    ['assigned_to', 'type', 'is_action', 'status'],
                    'idx_notes_assigned_type_action_status'
                );
            });
        }

        if (Schema::hasColumn('notes', 'application_id') && ! Schema::hasIndex('notes', 'idx_notes_app_count_cover')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->index(
                    ['type', 'client_id', 'is_action', 'status', 'application_id'],
                    'idx_notes_app_count_cover'
                );
            });
        }
    }

    private function upActivitiesLogs(): void
    {
        if (! Schema::hasTable('activities_logs')) {
            return;
        }
        $hasTask = Schema::hasColumn('activities_logs', 'task_status');
        $hasCreated = Schema::hasColumn('activities_logs', 'created_at');
        if (! $hasTask || ! $hasCreated) {
            return;
        }
        if (Schema::hasColumn('activities_logs', 'created_by') && ! Schema::hasIndex('activities_logs', 'activities_logs_task_status_created_by_created_at_idx')) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement(
                    'CREATE INDEX activities_logs_task_status_created_by_created_at_idx ON activities_logs '
                    . '(task_status, created_by, created_at DESC)'
                );
            } else {
                Schema::table('activities_logs', function (Blueprint $table) {
                    $table->index(
                        ['task_status', 'created_by', 'created_at'],
                        'activities_logs_task_status_created_by_created_at_idx'
                    );
                });
            }
        }
        if (Schema::hasColumn('activities_logs', 'client_id') && ! Schema::hasIndex('activities_logs', 'activities_logs_task_status_client_id_created_at_idx')) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement(
                    'CREATE INDEX activities_logs_task_status_client_id_created_at_idx ON activities_logs '
                    . '(task_status, client_id, created_at DESC)'
                );
            } else {
                Schema::table('activities_logs', function (Blueprint $table) {
                    $table->index(
                        ['task_status', 'client_id', 'created_at'],
                        'activities_logs_task_status_client_id_created_at_idx'
                    );
                });
            }
        }
    }

    private function upAdmins(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }
        if (! Schema::hasColumn('admins', 'type') || ! Schema::hasColumn('admins', 'is_archived')) {
            return;
        }
        if (Schema::hasIndex('admins', 'admins_type_is_archived_idx')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            $table->index(['type', 'is_archived'], 'admins_type_is_archived_idx');
        });
    }

    private function upClientAccessGrantsPartial(): void
    {
        if (DB::getDriverName() !== 'pgsql' || ! Schema::hasTable('client_access_grants')) {
            return;
        }
        if (! Schema::hasColumn('client_access_grants', 'grant_type')
            || ! Schema::hasColumn('client_access_grants', 'staff_id')
            || ! Schema::hasColumn('client_access_grants', 'admin_id')
            || ! Schema::hasColumn('client_access_grants', 'created_at')) {
            return;
        }
        if ($this->pgIndexExists('cag_exempt_dedup_support_idx')) {
            return;
        }
        DB::statement(
            "CREATE INDEX cag_exempt_dedup_support_idx ON client_access_grants (staff_id, admin_id, created_at) "
            . "WHERE grant_type = 'exempt'"
        );
    }

    private function pgIndexExists(string $name): bool
    {
        if (DB::getDriverName() !== 'pgsql') {
            return false;
        }
        $row = DB::selectOne('SELECT 1 AS ok FROM pg_indexes WHERE indexname = ?', [$name]);

        return (bool) $row;
    }

    private function quoteId(string $name): string
    {
        return '"'.str_replace('"', '""', $name).'"';
    }
};
