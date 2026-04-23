<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasTable('admins')
            && Schema::hasColumn('admins', 'first_name')
            && Schema::hasColumn('admins', 'last_name')
            && Schema::hasColumn('admins', 'email')
            && Schema::hasColumn('admins', 'client_id')
        ) {
            try {
                Schema::table('admins', function (Blueprint $table) {
                    $table->fullText(
                        ['first_name', 'last_name', 'email', 'client_id'],
                        'admins_global_search_ft'
                    );
                });
            } catch (\Throwable $e) {
                if (! $this->isMysqlDuplicateKeyError($e)) {
                    throw $e;
                }
            }
        }

        if (Schema::hasTable('client_matters')) {
            $cols = ['department_reference', 'other_reference', 'client_unique_matter_no'];
            $missing = false;
            foreach ($cols as $col) {
                if (! Schema::hasColumn('client_matters', $col)) {
                    $missing = true;
                    break;
                }
            }
            if (! $missing) {
                try {
                    Schema::table('client_matters', function (Blueprint $table) {
                        $table->fullText(
                            ['department_reference', 'other_reference', 'client_unique_matter_no'],
                            'client_matters_global_search_ft'
                        );
                    });
                } catch (\Throwable $e) {
                    if (! $this->isMysqlDuplicateKeyError($e)) {
                        throw $e;
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            'admins' => 'admins_global_search_ft',
            'client_matters' => 'client_matters_global_search_ft',
        ] as $table => $indexName) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            try {
                DB::statement('ALTER TABLE `'.$table.'` DROP INDEX `'.$indexName.'`');
            } catch (\Throwable) {
                // index may not exist
            }
        }
    }

    private function isMysqlDuplicateKeyError(\Throwable $e): bool
    {
        $m = strtolower($e->getMessage());

        return Str::contains($m, '1061')
            || Str::contains($m, 'duplicate')
            || Str::contains($m, 'already exists');
    }
};
