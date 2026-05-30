<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_eoi_references', function (Blueprint $table) {
            if (! Schema::hasColumn('client_eoi_references', 'sheet_comments')) {
                $table->text('sheet_comments')->nullable()->after('updated_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_eoi_references', function (Blueprint $table) {
            if (Schema::hasColumn('client_eoi_references', 'sheet_comments')) {
                $table->dropColumn('sheet_comments');
            }
        });
    }
};
