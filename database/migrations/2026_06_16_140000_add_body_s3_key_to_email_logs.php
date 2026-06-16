<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('email_logs')) {
            return;
        }

        Schema::table('email_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('email_logs', 'body_s3_key')) {
                $table->string('body_s3_key', 500)->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_logs')) {
            return;
        }

        Schema::table('email_logs', function (Blueprint $table) {
            if (Schema::hasColumn('email_logs', 'body_s3_key')) {
                $table->dropColumn('body_s3_key');
            }
        });
    }
};
