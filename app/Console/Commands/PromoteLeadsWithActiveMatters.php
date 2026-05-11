<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PromoteLeadsWithActiveMatters extends Command
{
    protected $signature = 'crm:promote-leads-with-active-matters
                            {--dry-run : Only show how many records would update}';

    protected $description = 'Set admins.type to client where type is still lead but an active client_matter exists (fixes visa sheet Lead label vs matter lifecycle).';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $leadIds = Admin::query()
            ->where('type', 'lead')
            ->whereNull('is_deleted')
            ->whereExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('client_matters')
                    ->whereColumn('client_matters.client_id', 'admins.id')
                    ->where('client_matters.matter_status', 1);
            })
            ->orderBy('id')
            ->pluck('id');

        $total = $leadIds->count();
        $this->info('Leads with at least one active client_matter (matter_status = 1): '.$total);

        if ($total === 0) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('Dry run — no rows updated. Omit --dry-run to apply.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($leadIds as $id) {
            if (Admin::promoteLeadWithActiveMatterToClient((int) $id)) {
                ++$updated;
            }
        }

        $this->info("Promoted {$updated} of {$total} admin record(s) to client.");
        return self::SUCCESS;
    }
}
