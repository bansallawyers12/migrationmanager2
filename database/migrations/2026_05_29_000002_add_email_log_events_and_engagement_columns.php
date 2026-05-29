<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_logs')) {
            Schema::table('email_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('email_logs', 'opened_at')) {
                    $table->timestamp('opened_at')->nullable()->after('delivered_at');
                }
                if (! Schema::hasColumn('email_logs', 'clicked_at')) {
                    $table->timestamp('clicked_at')->nullable()->after('opened_at');
                }
                if (! Schema::hasColumn('email_logs', 'spam_reported_at')) {
                    $table->timestamp('spam_reported_at')->nullable()->after('clicked_at');
                }
            });
        }

        if (! Schema::hasTable('email_log_events')) {
            Schema::create('email_log_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('email_log_id');
                $table->string('event_type', 32);
                $table->timestamp('occurred_at');
                $table->json('metadata')->nullable();
                $table->string('sendgrid_event_id', 64)->nullable();
                $table->timestamps();

                $table->foreign('email_log_id')
                    ->references('id')
                    ->on('email_logs')
                    ->cascadeOnDelete();

                $table->index(['email_log_id', 'occurred_at'], 'email_log_events_log_occurred_idx');
                $table->unique('sendgrid_event_id', 'email_log_events_sg_event_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_log_events');

        if (! Schema::hasTable('email_logs')) {
            return;
        }

        Schema::table('email_logs', function (Blueprint $table) {
            foreach (['spam_reported_at', 'clicked_at', 'opened_at'] as $column) {
                if (Schema::hasColumn('email_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
