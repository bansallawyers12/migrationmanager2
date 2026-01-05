#!/bin/bash
# Migration Manager Python Services - Linux Startup Script
# This script starts the unified Python services on Linux

echo "============================================================"
echo "Migration Manager Python Services - Linux Startup"
echo "============================================================"

# Check if Python is available
if ! command -v python3 &> /dev/null
then
    echo "❌ Error: Python 3 is not installed or not in PATH"
    echo "   Please install Python 3.7+ using your package manager:"
    echo "   Ubuntu/Debian: sudo apt install python3 python3-pip python3-venv"
    echo "   CentOS/RHEL: sudo yum install python3 python3-pip"
    echo "   Arch: sudo pacman -S python python-pip"
    exit 1
fi

# Check Python version
PYTHON_VERSION=$(python3 --version 2>&1 | awk '{print $2}')
echo "✅ Python version: $PYTHON_VERSION"

# Get the directory where this script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Check if main.py exists
if [ ! -f "main.py" ]; then
    echo "❌ Error: main.py not found in $SCRIPT_DIR"
    echo "   Please run this script from the python_services directory"
    exit 1
fi

# Check if requirements.txt exists
if [ ! -f "requirements.txt" ]; then
    echo "❌ Error: requirements.txt not found"
    echo "   Please ensure all files are present"
    exit 1
fi

# Install dependencies if needed
echo "📦 Checking dependencies..."
python3 -c "import fastapi, uvicorn, extract_msg" &> /dev/null
if [ $? -ne 0 ]; then
    echo "📦 Installing dependencies..."
    python3 -m pip install -r requirements.txt
    if [ $? -ne 0 ]; then
        echo "❌ Failed to install dependencies"
        exit 1
    fi
    echo "✅ Dependencies installed"
else
    echo "✅ Dependencies are up to date"
fi

# Start the service
echo ""
echo "🚀 Starting Migration Manager Python Services..."
echo "   Host: 127.0.0.1"
echo "   Port: 5000"
echo "   URL: http://127.0.0.1:5000"
echo "   Health: http://127.0.0.1:5000/health"
echo ""
echo "Press Ctrl+C to stop the service"
echo "============================================================"

python3 main.py --host 127.0.0.1 --port 5000

echo ""
echo "⏹️  Service stopped"

