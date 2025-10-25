@echo off
cd /d "%~dp0"
call venv\Scripts\activate.bat
python %*

REM Example usage:
REM run_python.bat test_email_modules.py
REM run_python.bat send_mail.py zoho user@example.com password recipient@example.com "Subject" "Body"
REM run_python.bat sync_emails.py zoho user@example.com password inbox 50
REM run_python.bat test_network.py imap.zoho.com 993
