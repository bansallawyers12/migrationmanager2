<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (! Schema::hasColumn('documents', 'hubdoc_sent')) {
                $table->boolean('hubdoc_sent')->nullable()->default(false);
            }
            if (! Schema::hasColumn('documents', 'hubdoc_sent_at')) {
                $table->timestamp('hubdoc_sent_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'hubdoc_sent_at')) {
                $table->dropColumn('hubdoc_sent_at');
            }
            if (Schema::hasColumn('documents', 'hubdoc_sent')) {
                $table->dropColumn('hubdoc_sent');
            }
        });
    }
};
