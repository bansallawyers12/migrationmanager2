<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cp_doc_checklist', function (Blueprint $table) {
            $table->renameColumn('document_type', 'cp_checklist_name');
        });
    }

    public function down(): void
    {
        Schema::table('cp_doc_checklist', function (Blueprint $table) {
            $table->renameColumn('cp_checklist_name', 'document_type');
        });
    }
};
