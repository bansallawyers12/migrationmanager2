<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds is_pinned column to all client reference tables for sheet pinning feature.
     */
    public function up(): void
    {
        $tables = [
            'client_tr_references',
            'client_student_references',
            'client_visitor_references',
            'client_pr_references',
            'client_employer_sponsored_references',
            'client_art_references',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'is_pinned')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->boolean('is_pinned')->default(false)->after('checklist_sent_at')->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'client_tr_references',
            'client_student_references',
            'client_visitor_references',
            'client_pr_references',
            'client_employer_sponsored_references',
            'client_art_references',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'is_pinned')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('is_pinned');
                });
            }
        }
    }
};
