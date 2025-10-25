# Python Outlook Web Environment

This folder contains Python scripts for email management functionality in the Bansal Immigration system.

## Files

- `send_mail.py` - Script for sending emails via SMTP
- `sync_emails.py` - Script for syncing emails from IMAP servers
- `test_network.py` - Script for testing network connectivity to email servers
- `requirements.txt` - Python dependencies
- `run_python.bat` - Windows batch file to run Python scripts with virtual environment

## Virtual Environment

A Python virtual environment is set up in the `venv` folder with the following dependencies:
- boto3 (for AWS S3 integration)
- certifi (for SSL certificate verification)

### Built-in Python Modules Used
The following modules are built-in to Python and don't require installation:
- imaplib (for IMAP email server communication)
- email (for email message parsing and creation)
- smtplib (for SMTP email sending)
- ssl (for secure connections)
- socket (for network communication)
- json (for JSON data handling)
- os, sys, traceback, pathlib (for system operations)

## Usage

### Windows
```bash
# Run a Python script
run_python.bat send_mail.py arg1 arg2

# Or activate virtual environment manually
venv\Scripts\activate
python send_mail.py arg1 arg2
```

### Linux/Mac
```bash
# Activate virtual environment
source venv/bin/activate

# Run Python scripts
python send_mail.py arg1 arg2
python sync_emails.py arg1 arg2
python test_network.py arg1 arg2
```

## Integration with Laravel

The EmailUser controllers in Laravel are configured to use these Python scripts:
- `EmailController.php` - Uses `send_mail.py` and `sync_emails.py`
- `EmailAccountController.php` - Uses `test_network.py` and `sync_emails.py`

All scripts are executed using the virtual environment Python executable to ensure proper dependency management.
