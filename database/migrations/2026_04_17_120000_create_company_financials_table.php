<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Multiple financial-year rows per company. Legacy columns on `companies`
     * remain and are kept in sync from the primary row (lowest sort_order).
     */
    public function up(): void
    {
        Schema::create('company_financials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('financial_year', 64)->nullable();
            $table->decimal('annual_turnover', 15, 2)->nullable();
            $table->decimal('wages_expenditure', 15, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

        // Copy existing single-company financial figures into one row each (no data loss).
        if (Schema::hasTable('companies')) {
            $companies = DB::table('companies')
                ->where(function ($q) {
                    $q->whereNotNull('annual_turnover')->orWhereNotNull('wages_expenditure');
                })
                ->get(['id', 'annual_turnover', 'wages_expenditure']);

            $now = now();
            foreach ($companies as $c) {
                DB::table('company_financials')->insert([
                    'company_id' => $c->id,
                    'financial_year' => null,
                    'annual_turnover' => $c->annual_turnover,
                    'wages_expenditure' => $c->wages_expenditure,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_financials');
    }
};
