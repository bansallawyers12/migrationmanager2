#!/bin/bash

echo "=== Setting up Linux Python Environment for Email System ==="
echo ""

# Check if we're in the right directory
if [ ! -f "requirements.txt" ]; then
    echo "❌ Error: Please run this script from the python_outlook_web directory"
    exit 1
fi

echo "✅ Python requirements file detected"
echo ""

# Check if Python 3 is installed
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is not installed. Please install Python 3.6+ first."
    echo "Ubuntu/Debian: sudo apt update && sudo apt install python3 python3-pip python3-venv"
    echo "CentOS/RHEL: sudo yum install python3 python3-pip"
    exit 1
fi

echo "✅ Python 3 found: $(python3 --version)"
echo ""

# Create virtual environment if it doesn't exist
if [ ! -d "venv" ]; then
    echo "📁 Creating virtual environment..."
    python3 -m venv venv
    echo "✅ Virtual environment created"
else
    echo "✅ Virtual environment already exists"
fi

echo ""
echo "🔧 Activating virtual environment and installing dependencies..."

# Activate virtual environment
source venv/bin/activate

echo "📦 Upgrading pip..."
pip install --upgrade pip

echo "📦 Installing dependencies from requirements.txt..."
pip install -r requirements.txt

echo ""
echo "🧪 Testing module imports..."
python -c "
import smtplib, imaplib, email, ssl, socket, json, os, sys, traceback, pathlib
from datetime import datetime, timedelta
from email.utils import parsedate_to_datetime
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email import encoders
import boto3
import certifi
print('✅ All required modules imported successfully')
"

echo ""
echo "🧪 Testing email modules..."
python test_email_modules.py

echo ""
echo "🔧 Setting proper permissions..."
# Make the run script executable
chmod +x run_python.sh

echo ""
echo "✅ Linux Python environment setup complete!"
echo ""
echo "To run Python scripts, use:"
echo "  ./run_python.sh send_mail.py zoho user@example.com password recipient@example.com 'Subject' 'Body'"
echo "  ./run_python.sh sync_emails.py zoho user@example.com password inbox 50"
echo "  ./run_python.sh test_network.py imap.zoho.com 993"
