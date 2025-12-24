# PostgreSQL database backup using pg_dump
# Usage: .\backup_db.ps1
#        .\backup_db.ps1 -DatabaseName "custom_db"
#        .\backup_db.ps1 -DatabaseName "custom_db" -DbHost "localhost" -Username "user"

param(
    [string]$DatabaseName = "",
    [string]$DbHost = "",
    [string]$DbPort = "",
    [string]$Username = "",
    [string]$Password = ""
)

# Function to read .env file
function Read-EnvFile {
    param([string]$FilePath)
    $envVars = @{}
    if (Test-Path $FilePath) {
        Get-Content $FilePath | ForEach-Object {
            if ($_ -match '^\s*([^#][^=]+)\s*=\s*(.+)$') {
                $key = $matches[1].Trim()
                $value = $matches[2].Trim()
                # Remove quotes if present
                $value = $value -replace '^["'']|["'']$', ''
                $envVars[$key] = $value
            }
        }
    }
    return $envVars
}

# Read .env file if it exists
$envFile = Join-Path $PSScriptRoot ".env"
$envVars = Read-EnvFile -FilePath $envFile

# Get database credentials from .env or use defaults
if ([string]::IsNullOrEmpty($DatabaseName)) {
    $DatabaseName = if ($envVars.ContainsKey("DB_DATABASE")) { $envVars["DB_DATABASE"] } else { "" }
}
if ([string]::IsNullOrEmpty($DbHost)) {
    $DbHost = if ($envVars.ContainsKey("DB_HOST")) { $envVars["DB_HOST"] } else { "127.0.0.1" }
}
if ([string]::IsNullOrEmpty($DbPort)) {
    $DbPort = if ($envVars.ContainsKey("DB_PORT")) { $envVars["DB_PORT"] } else { "5432" }
}
if ([string]::IsNullOrEmpty($Username)) {
    $Username = if ($envVars.ContainsKey("DB_USERNAME")) { $envVars["DB_USERNAME"] } else { "postgres" }
}
if ([string]::IsNullOrEmpty($Password)) {
    $Password = if ($envVars.ContainsKey("DB_PASSWORD")) { $envVars["DB_PASSWORD"] } else { "" }
}

# Validate required parameters
if ([string]::IsNullOrEmpty($DatabaseName)) {
    Write-Host "Error: Database name is required." -ForegroundColor Red
    Write-Host "Please provide -DatabaseName parameter or set DB_DATABASE in .env file" -ForegroundColor Yellow
    exit 1
}

# Prompt for password if still not provided
if ([string]::IsNullOrEmpty($Password)) {
    $SecurePassword = Read-Host "Enter database password" -AsSecureString
    $BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($SecurePassword)
    $Password = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($BSTR)
}

# Check if pg_dump is available
$pgDumpPath = Get-Command pg_dump -ErrorAction SilentlyContinue
if (-not $pgDumpPath) {
    Write-Host "Error: pg_dump command not found!" -ForegroundColor Red
    Write-Host "Please ensure PostgreSQL client tools are installed and in your PATH." -ForegroundColor Yellow
    Write-Host "You can download PostgreSQL from: https://www.postgresql.org/download/" -ForegroundColor Yellow
    exit 1
}

# Create backups directory
if (-not (Test-Path "backups")) {
    New-Item -ItemType Directory -Path "backups" | Out-Null
    Write-Host "Created backups directory" -ForegroundColor Gray
}

# Generate backup filename with timestamp
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = "backups\${DatabaseName}_backup_${Timestamp}.sql"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "PostgreSQL Database Backup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Database: $DatabaseName" -ForegroundColor White
Write-Host "Host: $DbHost" -ForegroundColor White
Write-Host "Port: $DbPort" -ForegroundColor White
Write-Host "Username: $Username" -ForegroundColor White
Write-Host "Output file: $BackupFile" -ForegroundColor Gray
Write-Host ""

# Set password environment variable
$env:PGPASSWORD = $Password

# Run pg_dump directly
pg_dump -h $DbHost -p $DbPort -U $Username -d $DatabaseName -F p -f $BackupFile --verbose --no-owner --no-privileges

# Check result
if ($LASTEXITCODE -eq 0) {
    $FileSize = (Get-Item $BackupFile).Length / 1MB
    Write-Host ""
    Write-Host "✓ Backup completed successfully!" -ForegroundColor Green
    Write-Host "  File: $BackupFile" -ForegroundColor Green
    Write-Host "  Size: $([math]::Round($FileSize, 2)) MB" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "✗ Backup failed with exit code: $LASTEXITCODE" -ForegroundColor Red
}

# Clear password
Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue

