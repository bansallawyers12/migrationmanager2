<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds personal_document_types.type: personal (default), company, both.
     * Backfill: all existing -> personal; title General -> both; ensure Financial (global, company).
     */
    public function up(): void
    {
        if (!Schema::hasTable('personal_document_types')) {
            return;
        }

        Schema::table('personal_document_types', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_document_types', 'type')) {
                $table->string('type', 20)->default('personal');
            }
        });

        DB::table('personal_document_types')->update([
            'type' => 'personal',
            'updated_at' => now(),
        ]);

        DB::table('personal_document_types')
            ->whereRaw('LOWER(TRIM(title)) = ?', ['general'])
            ->update([
                'type' => 'both',
                'updated_at' => now(),
            ]);

        $financial = DB::table('personal_document_types')
            ->whereRaw('LOWER(TRIM(title)) = ?', ['financial'])
            ->orderBy('id')
            ->first();

        if ($financial) {
            DB::table('personal_document_types')
                ->where('id', $financial->id)
                ->update([
                    'type' => 'company',
                    'client_id' => null,
                    'status' => 1,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('personal_document_types')->insert([
                'title' => 'Financial',
                'status' => 1,
                'client_id' => null,
                'type' => 'company',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('personal_document_types')) {
            return;
        }

        if (Schema::hasColumn('personal_document_types', 'type')) {
            Schema::table('personal_document_types', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
