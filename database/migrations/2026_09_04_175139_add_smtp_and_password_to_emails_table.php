<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store per-mailbox Zoho SMTP credentials on emails.
     *
     * Columns are nullable with Zoho defaults so existing compose/send
     * (SES → Zoho failover from .env) and Admin Console saves keep working.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            if (! Schema::hasColumn('emails', 'password')) {
                $table->string('password')->nullable();
            }
            if (! Schema::hasColumn('emails', 'smtp_host')) {
                $table->string('smtp_host')->nullable()->default('smtp.zoho.com');
            }
            if (! Schema::hasColumn('emails', 'smtp_port')) {
                $table->integer('smtp_port')->nullable()->default(587);
            }
            if (! Schema::hasColumn('emails', 'smtp_encryption')) {
                $table->string('smtp_encryption', 10)->nullable()->default('tls');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $columns = ['smtp_encryption', 'smtp_port', 'smtp_host', 'password'];
            $existing = array_values(array_filter(
                $columns,
                fn (string $col): bool => Schema::hasColumn('emails', $col)
            ));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
