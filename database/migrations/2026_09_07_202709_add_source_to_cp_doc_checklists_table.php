<?php

use App\Enums\ChecklistSource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || Schema::hasColumn('cp_doc_checklists', 'source')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->string('source', 32)->default(ChecklistSource::Workflow->value);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cp_doc_checklists') || ! Schema::hasColumn('cp_doc_checklists', 'source')) {
            return;
        }

        Schema::table('cp_doc_checklists', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
