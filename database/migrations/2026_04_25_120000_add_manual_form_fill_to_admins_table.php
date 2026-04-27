<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admins', 'manual_form_fill')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedTinyInteger('manual_form_fill')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admins', 'manual_form_fill')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('manual_form_fill');
            });
        }
    }
};
