<?php

use App\Support\WorkflowStageChecklistSync;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        WorkflowStageChecklistSync::backfillPortalSource();
    }

    public function down(): void
    {
        // Data backfill is not reversed; source default remains workflow.
    }
};
