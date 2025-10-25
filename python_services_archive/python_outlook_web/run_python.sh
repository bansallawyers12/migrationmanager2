#!/bin/bash
# Linux shell script to run Python scripts with virtual environment
# Usage: ./run_python.sh script_name.py arg1 arg2 ...

# Get the directory where this script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Change to the script directory
cd "$SCRIPT_DIR"

# Check if virtual environment exists
if [ ! -d "venv" ]; then
    echo "❌ Virtual environment not found. Please run setup_linux_env.sh first."
    exit 1
fi

# Activate virtual environment
source venv/bin/activate

# Check if Python script exists
if [ ! -f "$1" ]; then
    echo "❌ Python script not found: $1"
    echo "Available scripts:"
    ls -la *.py
    exit 1
fi

# Run the Python script with all arguments
python "$@"

# Deactivate virtual environment
deactivate
