@echo off
echo Creating database migration_manager_crm...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS migration_manager_crm;"

echo Importing SQL file...
C:\xampp\mysql\bin\mysql.exe -u root migration_manager_crm < database\migratio_bansal_immigration.sql

echo Database import completed!
pause




