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
            if (! Schema::hasColumn('email_logs', 'system_email_category')) {
                $table->string('system_email_category', 64)->nullable()->after('conversion_type');
            }
        });

        if (Schema::hasColumn('email_logs', 'system_email_category')
            && ! Schema::hasIndex('email_logs', 'email_logs_system_category_created_idx')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->index(['conversion_type', 'system_email_category', 'created_at'], 'email_logs_system_category_created_idx');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_logs')) {
            return;
        }

        if (Schema::hasIndex('email_logs', 'email_logs_system_category_created_idx')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->dropIndex('email_logs_system_category_created_idx');
            });
        }

        if (Schema::hasColumn('email_logs', 'system_email_category')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->dropColumn('system_email_category');
            });
        }
    }
};
