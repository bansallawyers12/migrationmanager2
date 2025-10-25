#!/usr/bin/env python3
"""
Test script to verify all email-related modules are working correctly.
This script tests the imports and basic functionality of all required modules.
"""

def test_imports():
    """Test that all required modules can be imported."""
    print("Testing email-related module imports...")
    
    try:
        # Built-in modules
        import smtplib
        import imaplib
        import email
        import ssl
        import socket
        import json
        import os
        import sys
        import traceback
        import pathlib
        from datetime import datetime, timedelta
        from email.utils import parsedate_to_datetime
        from email.mime.text import MIMEText
        from email.mime.multipart import MIMEMultipart
        from email.mime.base import MIMEBase
        from email import encoders
        
        print("✅ Built-in modules imported successfully")
        
        # Third-party modules
        import boto3
        import certifi
        
        print("✅ Third-party modules imported successfully")
        
        # Test basic functionality
        test_basic_functionality()
        
        print("✅ All email modules are working correctly!")
        return True
        
    except ImportError as e:
        print(f"❌ Import error: {e}")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def test_basic_functionality():
    """Test basic functionality of key modules."""
    print("Testing basic functionality...")
    
    # Import modules locally for testing
    from email.mime.multipart import MIMEMultipart
    import ssl
    import json
    import pathlib
    
    # Test email message creation
    msg = MIMEMultipart()
    msg['From'] = 'test@example.com'
    msg['To'] = 'recipient@example.com'
    msg['Subject'] = 'Test Email'
    
    # Test SSL context creation
    context = ssl.create_default_context()
    
    # Test JSON handling
    test_data = {'test': 'data'}
    json_str = json.dumps(test_data)
    parsed_data = json.loads(json_str)
    
    # Test pathlib
    test_path = pathlib.Path('test.txt')
    
    print("✅ Basic functionality tests passed")

if __name__ == "__main__":
    print("=" * 50)
    print("Email Modules Test")
    print("=" * 50)
    
    success = test_imports()
    
    print("=" * 50)
    if success:
        print("🎉 All tests passed! Email functionality is ready.")
        exit(0)
    else:
        print("❌ Some tests failed. Please check the errors above.")
        exit(1)
