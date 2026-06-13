<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optionally seed art-matters rows in client_matter_references from client_art_references.
     * Does not drop client_art_references (still used by ART Submission and Hearing Files sheet).
     */
    public function up(): void
    {
        if (! Schema::hasTable('client_art_references') || ! Schema::hasTable('client_matter_references')) {
            return;
        }

        $hasIsPinned = Schema::hasColumn('client_art_references', 'is_pinned');
        $rows = DB::table('client_art_references')->get();

        foreach ($rows as $row) {
            $exists = DB::table('client_matter_references')
                ->where('type', 'art-matters')
                ->where('client_id', $row->client_id)
                ->where('client_matter_id', $row->client_matter_id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('client_matter_references')->insert([
                'type' => 'art-matters',
                'client_id' => $row->client_id,
                'client_matter_id' => $row->client_matter_id,
                'current_status' => $row->status_of_file ?? null,
                'comments' => $row->comments ?? null,
                'checklist_sent_at' => $row->submission_last_date ?? null,
                'is_pinned' => $hasIsPinned ? ($row->is_pinned ?? false) : false,
                'created_by' => $row->created_by,
                'updated_by' => $row->updated_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('client_matter_references')) {
            return;
        }

        DB::table('client_matter_references')->where('type', 'art-matters')->delete();
    }
};
