<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cp_doc_checklist', function (Blueprint $table) {
            $table->dropColumn(['make_mandatory', 'date', 'time']);
        });
    }

    public function down(): void
    {
        Schema::table('cp_doc_checklist', function (Blueprint $table) {
            $table->string('make_mandatory')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
        });
    }
};
