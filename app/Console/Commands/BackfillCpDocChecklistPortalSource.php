<?php

namespace App\Console\Commands;

use App\Support\WorkflowStageChecklistSync;
use Illuminate\Console\Command;

class BackfillCpDocChecklistPortalSource extends Command
{
    protected $signature = 'checklists:backfill-portal-source
                            {--client-matter-id= : Limit to one client_matters.id}';

    protected $description = 'Set cp_doc_checklists.source=portal for rows copied from workflow_stage_portal_tasklists';

    public function handle(): int
    {
        $matterId = $this->option('client-matter-id');
        $clientMatterId = is_numeric($matterId) ? (int) $matterId : null;

        $updated = WorkflowStageChecklistSync::backfillPortalSource($clientMatterId);

        if ($clientMatterId) {
            $this->info("Updated {$updated} cp_doc_checklists row(s) to source=portal for client_matter_id={$clientMatterId}.");
        } else {
            $this->info("Updated {$updated} cp_doc_checklists row(s) to source=portal.");
        }

        return self::SUCCESS;
    }
}
