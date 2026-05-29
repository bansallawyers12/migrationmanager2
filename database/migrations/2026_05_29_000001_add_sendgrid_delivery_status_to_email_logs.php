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
            if (! Schema::hasColumn('email_logs', 'sendgrid_message_id')) {
                $table->string('sendgrid_message_id', 255)->nullable()->after('message_id');
            }
            if (! Schema::hasColumn('email_logs', 'delivery_status')) {
                $table->string('delivery_status', 32)->default('pending')->after('sendgrid_message_id');
            }
            if (! Schema::hasColumn('email_logs', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('delivery_status');
            }
            if (! Schema::hasColumn('email_logs', 'status_reason')) {
                $table->text('status_reason')->nullable()->after('delivered_at');
            }
        });

        if (Schema::hasColumn('email_logs', 'delivery_status') && ! Schema::hasIndex('email_logs', 'email_logs_delivery_status_idx')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->index('delivery_status', 'email_logs_delivery_status_idx');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('email_logs')) {
            return;
        }

        if (Schema::hasIndex('email_logs', 'email_logs_delivery_status_idx')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->dropIndex('email_logs_delivery_status_idx');
            });
        }

        Schema::table('email_logs', function (Blueprint $table) {
            $columns = ['status_reason', 'delivered_at', 'delivery_status', 'sendgrid_message_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('email_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
