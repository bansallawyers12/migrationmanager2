<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || Schema::hasColumn('cp_doc_checklists', 'task_type')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->string('task_type', 32)->default('upload');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || ! Schema::hasColumn('cp_doc_checklists', 'task_type')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->dropColumn('task_type');
        });
    }
};
