# Import SQL file with foreign key checks disabled
$sqlFile = "C:\Users\ajay\Downloads\migration_manager_crm (6)\migration_manager_crm (6).sql"
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$database = "migration_manager_crm"

Write-Host "Disabling foreign key checks and importing database..."

# Read SQL file and prepend/append foreign key commands
$sqlContent = Get-Content $sqlFile -Raw
$fullSql = "SET FOREIGN_KEY_CHECKS=0;`n" + $sqlContent + "`nSET FOREIGN_KEY_CHECKS=1;"

# Pipe to MySQL
$fullSql | & $mysqlPath -u root $database

if ($LASTEXITCODE -eq 0) {
    Write-Host "Database imported successfully!"
} else {
    Write-Host "Error importing database. Exit code: $LASTEXITCODE"
}




