<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SyncDataFromMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:mysql-data
                            {--source=mysql_source : MySQL source connection name}
                            {--target=pgsql : Target PostgreSQL connection name}
                            {--table=* : Specific tables to sync (default: all tables)}
                            {--skip-existing : Skip records that already exist}
                            {--update-existing : Update existing records}
                            {--dry-run : Show what would be imported without actually importing}
                            {--batch-size=500 : Number of records to process per batch}
                            {--limit= : Maximum number of records per table (useful for testing)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from MySQL source database to PostgreSQL target database';

    /**
     * Tables that should be processed first (parent tables with no dependencies)
     */
    protected $priorityTables = [
        'countries',
        'branches',
        'agents',
        'admins',
        'clients',
        'matters',
        'workflows',
        'workflow_stages',
        'anzsco_occupations',
        'sms_templates',
        'crm_email_templates',
        'matter_email_templates',
        'matter_other_email_templates',
        'email_labels',
        'settings',
        'teams',
        'user_roles',
        'countries',
        'appointment_consultants',
    ];

    /**
     * Tables to exclude from sync (system tables or tables that shouldn't be synced)
     */
    protected $excludedTables = [
        'migrations',
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'failed_jobs',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceConnection = $this->option('source');
        $targetConnection = $this->option('target');
        $requestedTables = $this->option('table');
        $skipExisting = $this->option('skip-existing');
        $updateExisting = $this->option('update-existing');
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        if ($dryRun) {
            $this->info("🔍 DRY RUN MODE - No data will be imported");
            $this->newLine();
        }

        $this->info("Starting data sync from MySQL ({$sourceConnection}) to PostgreSQL ({$targetConnection})...");
        $this->newLine();

        // Check connections
        if (!$this->checkConnections($sourceConnection, $targetConnection)) {
            return 1;
        }

        // Get list of tables to sync
        $tablesToSync = $this->getTablesToSync($sourceConnection, $targetConnection, $requestedTables);
        
        if (empty($tablesToSync)) {
            $this->warn("No tables to sync.");
            return 0;
        }

        $this->info("Found " . count($tablesToSync) . " table(s) to sync");
        if ($requestedTables) {
            $this->info("Tables: " . implode(', ', $tablesToSync));
        }
        $this->newLine();

        // Order tables by priority (parent tables first)
        $orderedTables = $this->orderTables($tablesToSync);

        // Confirmation (unless dry-run or no-interaction)
        if (!$dryRun && !$this->option('no-interaction')) {
            if (!$this->confirm('Do you want to continue?', false)) {
                $this->info('Sync cancelled.');
                return 0;
            }
        }

        $this->newLine();

        // Statistics
        $stats = [
            'total_tables' => count($orderedTables),
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $tableStats = [];

        // Sync each table
        foreach ($orderedTables as $table) {
            $this->info("Processing table: {$table}");
            
            $result = $this->syncTable(
                $sourceConnection, 
                $targetConnection, 
                $table, 
                $skipExisting, 
                $updateExisting,
                $dryRun,
                $batchSize,
                $limit
            );

            $stats['imported'] += $result['imported'];
            $stats['updated'] += $result['updated'];
            $stats['skipped'] += $result['skipped'];
            $stats['errors'] += $result['errors'];
            
            $tableStats[$table] = $result;

            if ($result['imported'] > 0 || $result['updated'] > 0 || $result['skipped'] > 0) {
                $this->info("  ✓ {$table}: {$result['imported']} imported, {$result['updated']} updated, {$result['skipped']} skipped, {$result['errors']} errors");
            } else {
                $this->warn("  ⚠ {$table}: No changes");
            }
            $this->newLine();
        }

        // Summary
        $this->newLine();
        $this->info("=== Sync Summary ===");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Tables Processed', $stats['total_tables']],
                ['Records Imported', $stats['imported']],
                ['Records Updated', $stats['updated']],
                ['Records Skipped', $stats['skipped']],
                ['Errors', $stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info("🔍 This was a dry run. Run without --dry-run to actually import data.");
        }

        return $stats['errors'] > 0 ? 1 : 0;
    }

    /**
     * Check database connections
     */
    protected function checkConnections($sourceConnection, $targetConnection)
    {
        try {
            DB::connection($sourceConnection)->getPdo();
            $this->info("✓ Connected to MySQL source database");
        } catch (\Exception $e) {
            $this->error("✗ Cannot connect to MySQL source: " . $e->getMessage());
            return false;
        }

        try {
            DB::connection($targetConnection)->getPdo();
            $this->info("✓ Connected to PostgreSQL target database");
        } catch (\Exception $e) {
            $this->error("✗ Cannot connect to PostgreSQL target: " . $e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Get list of tables to sync
     */
    protected function getTablesToSync($sourceConnection, $targetConnection, $requestedTables)
    {
        // Get all tables from PostgreSQL (target)
        $targetTables = [];
        try {
            $tables = DB::connection($targetConnection)->select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_type = 'BASE TABLE'
                ORDER BY table_name
            ");
            
            foreach ($tables as $table) {
                $targetTables[] = $table->table_name;
            }
        } catch (\Exception $e) {
            $this->error("Error getting target tables: " . $e->getMessage());
            return [];
        }

        // Filter by requested tables if specified
        if (!empty($requestedTables)) {
            $targetTables = array_intersect($targetTables, $requestedTables);
        }

        // Exclude system tables
        $targetTables = array_diff($targetTables, $this->excludedTables);

        // Verify tables exist in source
        $validTables = [];
        foreach ($targetTables as $table) {
            if (Schema::connection($sourceConnection)->hasTable($table)) {
                $validTables[] = $table;
            } else {
                $this->warn("  ⚠ Table '{$table}' does not exist in source database");
            }
        }

        return $validTables;
    }

    /**
     * Order tables so parent tables are processed first
     */
    protected function orderTables($tables)
    {
        $ordered = [];
        $remaining = $tables;

        // Add priority tables first
        foreach ($this->priorityTables as $priorityTable) {
            if (in_array($priorityTable, $remaining)) {
                $ordered[] = $priorityTable;
                $remaining = array_diff($remaining, [$priorityTable]);
            }
        }

        // Add remaining tables
        $ordered = array_merge($ordered, $remaining);

        return $ordered;
    }

    /**
     * Sync a single table
     */
    protected function syncTable($sourceConnection, $targetConnection, $table, $skipExisting, $updateExisting, $dryRun, $batchSize, $limit)
    {
        $stats = ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

        try {
            // Get total count first (for progress bar)
            $countQuery = DB::connection($sourceConnection)->table($table);
            if ($limit) {
                $countQuery->limit($limit);
            }
            $total = $countQuery->count();

            if ($total === 0) {
                return $stats;
            }

            if ($dryRun) {
                // Get target count
                $targetCount = DB::connection($targetConnection)->table($table)->count();
                $this->line("  Would import {$total} records (target currently has {$targetCount})");
                return ['imported' => $total, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
            }

            $bar = $this->output->createProgressBar($total);
            $bar->setFormat('  %current%/%max% [%bar%] %percent:3s%%');
            $bar->start();

            $batch = [];
            $primaryKey = $this->getPrimaryKey($targetConnection, $table);
            
            // Process in chunks to avoid memory issues for large tables
            $chunkSize = 1000;
            $processed = 0;
            $offset = 0;
            
            // Check if table has an id column for ordering
            $hasIdColumn = Schema::connection($sourceConnection)->hasColumn($table, 'id');
            $orderColumn = $hasIdColumn ? 'id' : $primaryKey;
            
            while ($processed < $total) {
                // Get records in chunks
                $query = DB::connection($sourceConnection)->table($table);
                
                if ($orderColumn) {
                    $query->orderBy($orderColumn);
                }
                
                if ($limit && ($offset + $chunkSize) > $limit) {
                    $query->limit($limit - $offset)->offset($offset);
                } else {
                    $query->limit($chunkSize)->offset($offset);
                }
                
                $sourceRecords = $query->get();
                
                if ($sourceRecords->isEmpty()) {
                    break;
                }

                // Batch check which records already exist (much faster than individual checks)
                $existingIds = [];
                if ($skipExisting && $primaryKey) {
                    $sourceIds = $sourceRecords->pluck($primaryKey)->filter()->toArray();
                    if (!empty($sourceIds)) {
                        $existingIds = DB::connection($targetConnection)
                            ->table($table)
                            ->whereIn($primaryKey, $sourceIds)
                            ->pluck($primaryKey)
                            ->toArray();
                    }
                }

                foreach ($sourceRecords as $record) {
                    try {
                        $recordData = (array) $record;
                        $recordId = $recordData['id'] ?? null;

                        // Sanitize data
                        $recordData = $this->sanitizeData($recordData);

                        // Check if record exists (using pre-fetched list)
                        $exists = false;
                        if ($primaryKey && isset($recordData[$primaryKey])) {
                            $exists = in_array($recordData[$primaryKey], $existingIds);
                        }

                        if ($exists) {
                            if ($skipExisting) {
                                $stats['skipped']++;
                                $bar->advance();
                                $processed++;
                                continue;
                            }

                            if ($updateExisting) {
                                // Update existing record
                                unset($recordData['created_at']); // Preserve original created_at
                                
                                DB::connection($targetConnection)->beginTransaction();
                                try {
                                    DB::connection($targetConnection)
                                        ->table($table)
                                        ->where($primaryKey, $recordData[$primaryKey])
                                        ->update($recordData);
                                    
                                    DB::connection($targetConnection)->commit();
                                    $stats['updated']++;
                                } catch (\Exception $e) {
                                    DB::connection($targetConnection)->rollBack();
                                    $stats['errors']++;
                                    Log::error("Error updating {$table} (ID: {$recordId}): " . $e->getMessage());
                                }
                            } else {
                                $stats['skipped']++;
                            }
                        } else {
                            // Add to batch for insert
                            // Preserve ID if it doesn't exist in target
                            if ($primaryKey === 'id' && $recordId && !$this->idExists($targetConnection, $table, $recordId)) {
                                $recordData['id'] = $recordId;
                            }
                            
                            $batch[] = $recordData;

                            // Insert batch when it reaches batch size
                            if (count($batch) >= $batchSize) {
                                $result = $this->insertBatch($targetConnection, $table, $batch, $primaryKey);
                                $stats['imported'] += $result['imported'];
                                $stats['errors'] += $result['errors'];
                                $batch = [];
                            }
                        }

                        $bar->advance();
                        $processed++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        Log::error("Error processing {$table} record: " . $e->getMessage());
                        $bar->advance();
                        $processed++;
                    }
                }
                
                $offset += $chunkSize;
            }

            // Insert remaining batch
            if (!empty($batch)) {
                $result = $this->insertBatch($targetConnection, $table, $batch, $primaryKey);
                $stats['imported'] += $result['imported'];
                $stats['errors'] += $result['errors'];
            }

            $bar->finish();
            $this->newLine();

        } catch (\Exception $e) {
            $this->error("  ✗ Error syncing table {$table}: " . $e->getMessage());
            $stats['errors']++;
        }

        return $stats;
    }

    /**
     * Get primary key column name for a table
     */
    protected function getPrimaryKey($connection, $table)
    {
        try {
            $driver = DB::connection($connection)->getDriverName();
            
            if ($driver === 'pgsql') {
                $result = DB::connection($connection)->selectOne("
                    SELECT column_name
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.key_column_usage kcu
                        ON tc.constraint_name = kcu.constraint_name
                        AND tc.table_schema = kcu.table_schema
                    WHERE tc.constraint_type = 'PRIMARY KEY'
                        AND tc.table_name = ?
                        AND tc.table_schema = 'public'
                ", [$table]);
                
                return $result ? $result->column_name : 'id';
            } else {
                // MySQL - assume 'id' for most tables
                return 'id';
            }
        } catch (\Exception $e) {
            // Default to 'id' if we can't determine
            return 'id';
        }
    }

    /**
     * Check if an ID exists in target table
     */
    protected function idExists($connection, $table, $id)
    {
        try {
            return DB::connection($connection)
                ->table($table)
                ->where('id', $id)
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sanitize record data (dates, nulls, etc.)
     */
    protected function sanitizeData($data)
    {
        foreach ($data as $key => $value) {
            // Handle invalid MySQL dates
            if (is_string($value) && (strpos($value, '0000-00-00') !== false || $value === '0000-00-00' || $value === '0000-00-00 00:00:00')) {
                $data[$key] = null;
            }
            
            // Handle empty strings in nullable fields (convert to null for consistency)
            if ($value === '') {
                // Only convert to null if it's a date/datetime field
                if (strpos($key, '_at') !== false || strpos($key, '_date') !== false || strpos($key, 'date') !== false) {
                    $data[$key] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Insert a batch of records
     */
    protected function insertBatch($connection, $table, $batch, $primaryKey)
    {
        $imported = 0;
        $errors = 0;

        // Process in larger chunks for better performance
        $chunks = array_chunk($batch, 500);
        
        foreach ($chunks as $chunk) {
            try {
                // Try simple bulk insert first (fastest)
                DB::connection($connection)->table($table)->insert($chunk);
                $imported += count($chunk);
            } catch (\Exception $e) {
                // If bulk insert fails, try with ON CONFLICT for PostgreSQL (handles duplicates)
                if (DB::connection($connection)->getDriverName() === 'pgsql' && $primaryKey) {
                    try {
                        $columns = array_keys($chunk[0]);
                        $columnList = implode(', ', array_map(function($col) {
                            return '"' . $col . '"';
                        }, $columns));
                        
                        $placeholders = [];
                        $bindings = [];
                        foreach ($chunk as $record) {
                            $recordPlaceholders = [];
                            foreach ($columns as $column) {
                                $recordPlaceholders[] = '?';
                                $bindings[] = $record[$column] ?? null;
                            }
                            $placeholders[] = '(' . implode(', ', $recordPlaceholders) . ')';
                        }
                        
                        $sql = "INSERT INTO {$table} ({$columnList}) VALUES " . 
                               implode(', ', $placeholders) . 
                               " ON CONFLICT ({$primaryKey}) DO NOTHING";
                        
                        $result = DB::connection($connection)->affectingStatement($sql, $bindings);
                        $imported += $result;
                    } catch (\Exception $e2) {
                        // ON CONFLICT failed (no unique constraint), fall back to individual inserts
                        $this->fallbackIndividualInserts($connection, $table, $chunk, $primaryKey, $imported, $errors);
                    }
                } else {
                    // MySQL or ON CONFLICT not available, fall back to individual inserts
                    $this->fallbackIndividualInserts($connection, $table, $chunk, $primaryKey, $imported, $errors);
                }
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
    
    /**
     * Fall back to individual inserts when bulk insert fails
     */
    protected function fallbackIndividualInserts($connection, $table, $chunk, $primaryKey, &$imported, &$errors)
    {
        foreach ($chunk as $record) {
            try {
                DB::connection($connection)->table($table)->insert($record);
                $imported++;
            } catch (\Exception $e) {
                // Duplicate key or other error - skip it
                $errors++;
                if (!str_contains($e->getMessage(), 'Duplicate') && !str_contains($e->getMessage(), 'unique')) {
                    $recordId = $record[$primaryKey] ?? 'unknown';
                    Log::error("Error inserting into {$table} (ID: {$recordId}): " . $e->getMessage());
                }
            }
        }
    }
}

