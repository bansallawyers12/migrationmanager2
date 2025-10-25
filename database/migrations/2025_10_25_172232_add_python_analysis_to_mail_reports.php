<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            // Python Analysis Fields
            $table->json('python_analysis')->nullable()->after('message');
            $table->json('python_rendering')->nullable()->after('python_analysis');
            
            // AI Categorization
            $table->string('category')->nullable()->after('python_rendering')->index();
            $table->enum('priority', ['low', 'medium', 'high'])->default('low')->after('category')->index();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->default('neutral')->after('priority');
            $table->string('language')->nullable()->after('sentiment');
            
            // Enhanced Content
            $table->longText('enhanced_html')->nullable()->after('language');
            $table->longText('rendered_html')->nullable()->after('enhanced_html');
            $table->text('text_preview')->nullable()->after('rendered_html');
            
            // Security & Threading
            $table->json('security_issues')->nullable()->after('text_preview');
            $table->json('thread_info')->nullable()->after('security_issues');
            
            // Metadata
            $table->string('message_id')->nullable()->after('thread_info')->index();
            $table->string('thread_id')->nullable()->after('message_id')->index();
            $table->timestamp('received_date')->nullable()->after('thread_id');
            $table->timestamp('processed_at')->nullable()->after('received_date');
            $table->timestamp('last_accessed_at')->nullable()->after('processed_at');
            $table->string('file_hash')->nullable()->after('last_accessed_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_reports', function (Blueprint $table) {
            $table->dropColumn([
                'python_analysis',
                'python_rendering',
                'category',
                'priority',
                'sentiment',
                'language',
                'enhanced_html',
                'rendered_html',
                'text_preview',
                'security_issues',
                'thread_info',
                'message_id',
                'thread_id',
                'received_date',
                'processed_at',
                'last_accessed_at',
                'file_hash'
            ]);
        });
    }
};
