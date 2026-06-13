<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move ART sheet rows from client_art_references into unified client_matter_references (type = art-matters).
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

        Schema::dropIfExists('client_art_references');
    }

    public function down(): void
    {
        if (Schema::hasTable('client_art_references')) {
            return;
        }

        Schema::create('client_art_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('client_matter_id')->index();
            $table->date('submission_last_date')->nullable();
            $table->string('status_of_file', 50)->default('submission_pending');
            $table->string('hearing_time')->nullable();
            $table->string('member_name')->nullable();
            $table->string('outcome')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_pinned')->default(false)->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('client_matter_references')) {
            return;
        }

        $rows = DB::table('client_matter_references')->where('type', 'art')->get();
        foreach ($rows as $row) {
            DB::table('client_art_references')->insert([
                'client_id' => $row->client_id,
                'client_matter_id' => $row->client_matter_id,
                'submission_last_date' => $row->checklist_sent_at,
                'status_of_file' => $row->current_status ?? 'submission_pending',
                'comments' => $row->comments,
                'is_pinned' => $row->is_pinned ?? false,
                'created_by' => $row->created_by,
                'updated_by' => $row->updated_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        DB::table('client_matter_references')->where('type', 'art-matters')->delete();
    }
};
