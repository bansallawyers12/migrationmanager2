@echo off
echo Checking database tables...
C:\xampp\mysql\bin\mysql.exe -u root migration_manager_crm -e "SHOW TABLES;"
echo.
echo Database verification complete!




