<?php
/**
 * Backup and Rollback System for Laravel 12 Route Migration
 * This script provides safe backup and rollback functionality
 */

class RouteBackupSystem
{
    private $backupDir;
    private $projectRoot;

    public function __construct(string $projectRoot = null)
    {
        $this->projectRoot = $projectRoot ?: dirname(__DIR__);
        $this->backupDir = $this->projectRoot . '/migration-backups';
        
        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a backup of the current route files
     */
    public function createBackup(string $description = ''): array
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupId = 'backup_' . $timestamp;
        $backupPath = $this->backupDir . '/' . $backupId;
        
        // Create backup directory
        if (!mkdir($backupPath, 0755, true)) {
            throw new Exception("Failed to create backup directory: {$backupPath}");
        }

        $backupInfo = [
            'id' => $backupId,
            'timestamp' => $timestamp,
            'description' => $description,
            'files' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Files to backup
        $filesToBackup = [
            'routes/web.php',
            'routes/api.php',
            'routes/channels.php',
            'routes/console.php',
            'routes/emailUser.php'
        ];

        foreach ($filesToBackup as $file) {
            $sourcePath = $this->projectRoot . '/' . $file;
            $backupFilePath = $backupPath . '/' . basename($file);
            
            if (file_exists($sourcePath)) {
                if (copy($sourcePath, $backupFilePath)) {
                    $backupInfo['files'][] = [
                        'original' => $file,
                        'backup' => $backupFilePath,
                        'size' => filesize($backupFilePath),
                        'checksum' => md5_file($backupFilePath)
                    ];
                } else {
                    throw new Exception("Failed to backup file: {$file}");
                }
            }
        }

        // Save backup metadata
        $metadataFile = $backupPath . '/backup-info.json';
        file_put_contents($metadataFile, json_encode($backupInfo, JSON_PRETTY_PRINT));

        return $backupInfo;
    }

    /**
     * List all available backups
     */
    public function listBackups(): array
    {
        $backups = [];
        
        if (!is_dir($this->backupDir)) {
            return $backups;
        }

        $directories = scandir($this->backupDir);
        
        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..' || !is_dir($this->backupDir . '/' . $dir)) {
                continue;
            }

            $metadataFile = $this->backupDir . '/' . $dir . '/backup-info.json';
            
            if (file_exists($metadataFile)) {
                $metadata = json_decode(file_get_contents($metadataFile), true);
                if ($metadata) {
                    $backups[] = $metadata;
                }
            }
        }

        // Sort by timestamp (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $backups;
    }

    /**
     * Restore from a specific backup
     */
    public function restoreBackup(string $backupId): bool
    {
        $backupPath = $this->backupDir . '/' . $backupId;
        
        if (!is_dir($backupPath)) {
            throw new Exception("Backup not found: {$backupId}");
        }

        $metadataFile = $backupPath . '/backup-info.json';
        
        if (!file_exists($metadataFile)) {
            throw new Exception("Backup metadata not found: {$backupId}");
        }

        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        if (!$metadata) {
            throw new Exception("Invalid backup metadata: {$backupId}");
        }

        // Restore each file
        foreach ($metadata['files'] as $fileInfo) {
            $sourcePath = $backupPath . '/' . basename($fileInfo['original']);
            $targetPath = $this->projectRoot . '/' . $fileInfo['original'];
            
            if (!file_exists($sourcePath)) {
                throw new Exception("Backup file not found: {$sourcePath}");
            }

            // Verify checksum
            $currentChecksum = md5_file($sourcePath);
            if ($currentChecksum !== $fileInfo['checksum']) {
                throw new Exception("Backup file checksum mismatch: {$sourcePath}");
            }

            // Create target directory if it doesn't exist
            $targetDir = dirname($targetPath);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Copy file back
            if (!copy($sourcePath, $targetPath)) {
                throw new Exception("Failed to restore file: {$fileInfo['original']}");
            }
        }

        return true;
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(string $backupId): bool
    {
        $backupPath = $this->backupDir . '/' . $backupId;
        
        if (!is_dir($backupPath)) {
            throw new Exception("Backup not found: {$backupId}");
        }

        // Remove all files in backup directory
        $files = scandir($backupPath);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $filePath = $backupPath . '/' . $file;
            
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        // Remove backup directory
        return rmdir($backupPath);
    }

    /**
     * Clean up old backups (keep only last N backups)
     */
    public function cleanupOldBackups(int $keepCount = 5): int
    {
        $backups = $this->listBackups();
        $deletedCount = 0;

        if (count($backups) > $keepCount) {
            $backupsToDelete = array_slice($backups, $keepCount);
            
            foreach ($backupsToDelete as $backup) {
                if ($this->deleteBackup($backup['id'])) {
                    $deletedCount++;
                }
            }
        }

        return $deletedCount;
    }

    /**
     * Get backup directory path
     */
    public function getBackupDir(): string
    {
        return $this->backupDir;
    }

    /**
     * Verify backup integrity
     */
    public function verifyBackup(string $backupId): array
    {
        $backupPath = $this->backupDir . '/' . $backupId;
        
        if (!is_dir($backupPath)) {
            return ['valid' => false, 'error' => 'Backup not found'];
        }

        $metadataFile = $backupPath . '/backup-info.json';
        
        if (!file_exists($metadataFile)) {
            return ['valid' => false, 'error' => 'Backup metadata not found'];
        }

        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        if (!$metadata) {
            return ['valid' => false, 'error' => 'Invalid backup metadata'];
        }

        $verification = [
            'valid' => true,
            'files' => [],
            'errors' => []
        ];

        foreach ($metadata['files'] as $fileInfo) {
            $backupFilePath = $backupPath . '/' . basename($fileInfo['original']);
            
            if (!file_exists($backupFilePath)) {
                $verification['valid'] = false;
                $verification['errors'][] = "File not found: {$fileInfo['original']}";
                continue;
            }

            $currentChecksum = md5_file($backupFilePath);
            $fileVerification = [
                'file' => $fileInfo['original'],
                'exists' => true,
                'checksum_match' => $currentChecksum === $fileInfo['checksum'],
                'size_match' => filesize($backupFilePath) === $fileInfo['size']
            ];

            if (!$fileVerification['checksum_match']) {
                $verification['valid'] = false;
                $verification['errors'][] = "Checksum mismatch: {$fileInfo['original']}";
            }

            if (!$fileVerification['size_match']) {
                $verification['valid'] = false;
                $verification['errors'][] = "Size mismatch: {$fileInfo['original']}";
            }

            $verification['files'][] = $fileVerification;
        }

        return $verification;
    }
}

// Usage example (uncomment to test):
/*
$backupSystem = new RouteBackupSystem();

try {
    // Create a backup
    $backup = $backupSystem->createBackup('Pre-migration backup');
    echo "Backup created: {$backup['id']}\n";
    
    // List all backups
    $backups = $backupSystem->listBackups();
    echo "Total backups: " . count($backups) . "\n";
    
    // Verify backup
    $verification = $backupSystem->verifyBackup($backup['id']);
    echo "Backup valid: " . ($verification['valid'] ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
*/
