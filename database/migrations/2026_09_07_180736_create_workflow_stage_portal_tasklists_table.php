<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('workflow_stage_portal_tasklists')) {
            return;
        }

        Schema::create('workflow_stage_portal_tasklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id')->index();
            $table->unsignedBigInteger('workflow_stage_id')->index();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('task_type', 32)->default('upload');
            $table->boolean('allow_client')->default(true);
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['workflow_id', 'workflow_stage_id'], 'idx_wf_stage_portal_tasklists');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_stage_portal_tasklists');
    }
};
