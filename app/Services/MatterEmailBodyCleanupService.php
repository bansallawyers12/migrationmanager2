<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MatterEmailBodyCleanupService
{
    /**
     * Clear stored email body content for all emails linked to a matter.
     * S3 files, attachment rows, and email metadata (subject, from, etc.) are left intact.
     */
    public function clearBodiesForMatter(int $matterId): int
    {
        if ($matterId <= 0) {
            return 0;
        }

        $updates = ['message' => null];

        foreach ($this->bodyContentColumns() as $column) {
            if (Schema::hasColumn('email_logs', $column)) {
                $updates[$column] = null;
            }
        }

        $affected = EmailLog::where('client_matter_id', $matterId)->update($updates);

        if ($affected > 0) {
            Log::info('Cleared email body content for discontinued matter', [
                'client_matter_id' => $matterId,
                'email_logs_updated' => $affected,
            ]);
        }

        return $affected;
    }

    /**
     * @return list<string>
     */
    private function bodyContentColumns(): array
    {
        return [
            'enhanced_html',
            'rendered_html',
            'text_preview',
            'python_analysis',
            'python_rendering',
        ];
    }
}
