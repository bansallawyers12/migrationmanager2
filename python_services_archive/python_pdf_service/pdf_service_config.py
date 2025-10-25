#!/usr/bin/env python3
"""
Configuration file for PDF Processing Microservice
"""

import os
from pathlib import Path

class Config:
    """Base configuration"""
    
    # Service settings
    SERVICE_NAME = "PDF Processing Microservice"
    SERVICE_VERSION = "1.0.0"
    
    # Flask settings
    FLASK_HOST = os.getenv('FLASK_HOST', '127.0.0.1')
    FLASK_PORT = int(os.getenv('FLASK_PORT', 5000))
    FLASK_DEBUG = os.getenv('FLASK_DEBUG', 'false').lower() == 'true'
    
    # PDF processing settings
    MAX_FILE_SIZE = int(os.getenv('MAX_FILE_SIZE', 50 * 1024 * 1024))  # 50MB default
    DEFAULT_RESOLUTION = int(os.getenv('DEFAULT_RESOLUTION', 150))
    SUPPORTED_FORMATS = ['.pdf']
    
    # Storage paths
    TEMP_DIR = os.getenv('TEMP_DIR', 'C:/xampp/htdocs/migrationmanager/storage/app/temp')
    UPLOAD_DIR = os.getenv('UPLOAD_DIR', 'C:/xampp/htdocs/migrationmanager/storage/app/uploads')
    OUTPUT_DIR = os.getenv('OUTPUT_DIR', 'C:/xampp/htdocs/migrationmanager/storage/app/outputs')
    LOG_DIR = os.getenv('LOG_DIR', str(Path(__file__).parent / 'logs'))
    
    # Security settings
    ALLOWED_ORIGINS = os.getenv('ALLOWED_ORIGINS', 'localhost,127.0.0.1').split(',')
    API_KEY = os.getenv('API_KEY', None)
    
    # Logging
    LOG_LEVEL = os.getenv('LOG_LEVEL', 'INFO')
    LOG_FILE = os.getenv('LOG_FILE', str(Path(__file__).parent / 'logs' / 'pdf_service.log'))
    
    # Performance settings
    MAX_CONCURRENT_PROCESSES = int(os.getenv('MAX_CONCURRENT_PROCESSES', 4))
    REQUEST_TIMEOUT = int(os.getenv('REQUEST_TIMEOUT', 300))  # 5 minutes
    
    @classmethod
    def create_directories(cls):
        """Create necessary directories if they don't exist"""
        directories = [cls.TEMP_DIR, cls.UPLOAD_DIR, cls.OUTPUT_DIR, cls.LOG_DIR]
        for directory in directories:
            Path(directory).mkdir(parents=True, exist_ok=True)
    
    @classmethod
    def validate_config(cls):
        """Validate configuration settings"""
        errors = []
        
        # Check if directories are writable
        cls.create_directories()  # Create them first
        
        for directory in [cls.TEMP_DIR, cls.UPLOAD_DIR, cls.OUTPUT_DIR, cls.LOG_DIR]:
            if not os.access(directory, os.W_OK):
                errors.append(f"Directory not writable: {directory}")
        
        # Check file size limits
        if cls.MAX_FILE_SIZE <= 0:
            errors.append("MAX_FILE_SIZE must be positive")
        
        # Check resolution
        if cls.DEFAULT_RESOLUTION < 72 or cls.DEFAULT_RESOLUTION > 600:
            errors.append("DEFAULT_RESOLUTION must be between 72 and 600")
        
        return errors

class DevelopmentConfig(Config):
    """Development configuration"""
    FLASK_DEBUG = True
    LOG_LEVEL = 'DEBUG'
    MAX_FILE_SIZE = 100 * 1024 * 1024  # 100MB for development

class ProductionConfig(Config):
    """Production configuration"""
    FLASK_DEBUG = False
    LOG_LEVEL = 'WARNING'
    
    # Stricter security in production
    ALLOWED_ORIGINS = os.getenv('ALLOWED_ORIGINS', 'localhost,127.0.0.1').split(',')
    
    # Performance optimizations
    MAX_CONCURRENT_PROCESSES = int(os.getenv('MAX_CONCURRENT_PROCESSES', 8))

# Configuration mapping
config_map = {
    'development': DevelopmentConfig,
    'production': ProductionConfig,
}

def get_config():
    """Get configuration based on environment"""
    env = os.getenv('FLASK_ENV', 'development')
    config_class = config_map.get(env, DevelopmentConfig)
    
    # Create directories
    config_class.create_directories()
    
    # Validate configuration
    errors = config_class.validate_config()
    if errors:
        print(f"Configuration warnings: {', '.join(errors)}")
    
    return config_class

