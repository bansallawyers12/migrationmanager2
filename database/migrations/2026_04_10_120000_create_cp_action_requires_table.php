<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Client Portal "action required" queue.
     *
     * Column set mirrors `notifications` (same names and roles), plus:
     * - type: discriminates action kind (e.g. checklist_upload)
     * - client_id, client_matter_id: explicit scoping for queries (often align with receiver_id / module_id)
     */
    public function up(): void
    {
        Schema::create('cp_action_requires', function (Blueprint $table) {
            $table->id();

            $table->string('type', 255)->index();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('client_matter_id')->index();

            $table->unsignedBigInteger('sender_id')->nullable()->index();
            $table->unsignedBigInteger('receiver_id')->nullable()->index();
            $table->unsignedBigInteger('module_id')->nullable()->index();
            $table->string('url', 500)->nullable();
            $table->string('notification_type', 255)->nullable()->index();
            $table->text('message')->nullable();

            $table->timestamps();

            $table->integer('sender_status')->default(1);
            $table->integer('receiver_status')->default(0);
            $table->integer('seen')->default(0);

            $table->index(['client_id', 'receiver_status', 'created_at'], 'idx_cp_action_requires_client_status_created');
            $table->index(['client_id', 'client_matter_id', 'type'], 'idx_cp_action_requires_client_matter_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_action_requires');
    }
};
