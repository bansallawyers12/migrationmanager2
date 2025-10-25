#!/usr/bin/env python3
"""
Startup script for PDF Processing Microservice
"""

import os
import sys
import signal
import logging
from pathlib import Path

# Add current directory to Python path
sys.path.insert(0, str(Path(__file__).parent))

try:
    from pdf_service_config import get_config
    from pdf_processor import app
except ImportError as e:
    print(f"Failed to import required modules: {e}")
    print("Make sure all files are in the same directory")
    sys.exit(1)

def setup_logging(config):
    """Setup logging configuration"""
    log_level = getattr(logging, config.LOG_LEVEL.upper())
    
    # Configure logging
    handlers = [logging.StreamHandler(sys.stdout)]
    
    # Add file logging if specified
    if config.LOG_FILE:
        try:
            file_handler = logging.FileHandler(config.LOG_FILE)
            file_handler.setFormatter(logging.Formatter(
                '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
            ))
            handlers.append(file_handler)
        except Exception as e:
            print(f"Warning: Could not create log file: {e}")
    
    logging.basicConfig(
        level=log_level,
        format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
        handlers=handlers
    )
    
    return logging.getLogger(__name__)

def signal_handler(signum, frame):
    """Handle shutdown signals gracefully"""
    logger.info(f"Received signal {signum}, shutting down gracefully...")
    sys.exit(0)

def check_dependencies():
    """Check if all required dependencies are available"""
    try:
        import fitz
        from PIL import Image
        from flask import Flask
        logger.info("✓ All required dependencies are available")
        return True
    except ImportError as e:
        logger.error(f"✗ Missing dependency: {e}")
        logger.error("Install dependencies with: pip install -r requirements.txt")
        return False

def main():
    """Main startup function"""
    try:
        # Load configuration
        config = get_config()
        
        # Setup logging
        global logger
        logger = setup_logging(config)
        
        print("=" * 60)
        print(f"  {config.SERVICE_NAME} v{config.SERVICE_VERSION}")
        print("=" * 60)
        logger.info(f"Environment: {os.getenv('FLASK_ENV', 'development')}")
        
        # Check dependencies
        if not check_dependencies():
            sys.exit(1)
        
        # Setup signal handlers for graceful shutdown
        signal.signal(signal.SIGINT, signal_handler)
        signal.signal(signal.SIGTERM, signal_handler)
        
        # Log configuration
        logger.info(f"Service will run on {config.FLASK_HOST}:{config.FLASK_PORT}")
        logger.info(f"Debug mode: {config.FLASK_DEBUG}")
        logger.info(f"Max file size: {config.MAX_FILE_SIZE / (1024*1024):.1f} MB")
        logger.info(f"Default resolution: {config.DEFAULT_RESOLUTION} DPI")
        logger.info(f"Log directory: {config.LOG_DIR}")
        
        print("\n" + "=" * 60)
        print(f"  Service URL: http://{config.FLASK_HOST}:{config.FLASK_PORT}")
        print(f"  Health Check: http://{config.FLASK_HOST}:{config.FLASK_PORT}/health")
        print("=" * 60 + "\n")
        
        # Start the Flask application
        app.run(
            host=config.FLASK_HOST,
            port=config.FLASK_PORT,
            debug=config.FLASK_DEBUG,
            threaded=True
        )
        
    except Exception as e:
        print(f"Failed to start service: {e}")
        sys.exit(1)

if __name__ == '__main__':
    main()

