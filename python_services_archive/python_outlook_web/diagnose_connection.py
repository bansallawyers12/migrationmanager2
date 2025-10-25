#!/usr/bin/env python3
"""
Comprehensive connection diagnosis for Zoho IMAP
This script will help identify why connections are failing
"""

import socket
import ssl
import sys
import os
import platform
import subprocess
import imaplib

def test_basic_connectivity():
    """Test basic network connectivity"""
    print("=== BASIC CONNECTIVITY TEST ===")
    
    ips_to_test = ['136.143.190.29', '74.201.86.24', '74.201.86.25', '136.143.190.67']
    port = 993
    
    working_ips = []
    
    for ip in ips_to_test:
        print(f"Testing TCP connection to {ip}:{port}...")
        try:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(10)
            result = sock.connect_ex((ip, port))
            sock.close()
            
            if result == 0:
                print(f"✓ TCP connection to {ip}:{port} SUCCESSFUL")
                working_ips.append(ip)
            else:
                print(f"✗ TCP connection to {ip}:{port} FAILED (error code: {result})")
        except Exception as e:
            print(f"✗ TCP connection to {ip}:{port} ERROR: {e}")
    
    return working_ips

def test_ssl_connectivity(working_ips):
    """Test SSL connectivity"""
    print("\n=== SSL CONNECTIVITY TEST ===")
    
    ssl_working = []
    
    for ip in working_ips:
        print(f"Testing SSL connection to {ip}:993...")
        try:
            context = ssl.create_default_context()
            with socket.create_connection((ip, 993), timeout=15) as sock:
                with context.wrap_socket(sock, server_hostname="imap.zoho.com") as ssock:
                    print(f"✓ SSL connection to {ip}:993 SUCCESSFUL")
                    print(f"  SSL Version: {ssock.version()}")
                    print(f"  Cipher: {ssock.cipher()[0] if ssock.cipher() else 'Unknown'}")
                    ssl_working.append(ip)
        except Exception as e:
            print(f"✗ SSL connection to {ip}:993 FAILED: {e}")
    
    return ssl_working

def test_imaplib_connection(ssl_working):
    """Test IMAP library connections"""
    print("\n=== IMAPLIB CONNECTION TEST ===")
    
    for ip in ssl_working:
        print(f"Testing imaplib connection to {ip}...")
        
        # Method 1: Direct IP with default SSL
        try:
            print(f"  Method 1: Direct IP connection...")
            mail = imaplib.IMAP4_SSL(ip, 993)
            print(f"  ✓ imaplib direct connection to {ip} SUCCESSFUL")
            mail.logout()
            return ip  # Return first working IP
        except Exception as e:
            print(f"  ✗ imaplib direct connection to {ip} FAILED: {e}")
        
        # Method 2: Custom SSL context
        try:
            print(f"  Method 2: Custom SSL context...")
            context = ssl.create_default_context()
            mail = imaplib.IMAP4_SSL(ip, 993, ssl_context=context)
            print(f"  ✓ imaplib custom SSL to {ip} SUCCESSFUL")
            mail.logout()
            return ip
        except Exception as e:
            print(f"  ✗ imaplib custom SSL to {ip} FAILED: {e}")
        
        # Method 3: Manual socket + SSL wrap
        try:
            print(f"  Method 3: Manual socket + SSL wrap...")
            sock = socket.create_connection((ip, 993), timeout=15)
            context = ssl.create_default_context()
            ssl_sock = context.wrap_socket(sock, server_hostname="imap.zoho.com")
            mail = imaplib.IMAP4(ssl_sock)
            print(f"  ✓ Manual socket + SSL to {ip} SUCCESSFUL")
            mail.logout()
            return ip
        except Exception as e:
            print(f"  ✗ Manual socket + SSL to {ip} FAILED: {e}")
    
    return None

def test_environment_info():
    """Display environment information"""
    print("\n=== ENVIRONMENT INFORMATION ===")
    print(f"Platform: {platform.platform()}")
    print(f"Python Version: {platform.python_version()}")
    print(f"Python Executable: {sys.executable}")
    print(f"Current Working Directory: {os.getcwd()}")
    print(f"SSL Version: {ssl.OPENSSL_VERSION}")
    
    # Test DNS resolution
    print(f"\n=== DNS RESOLUTION TEST ===")
    try:
        ips = socket.gethostbyname_ex("imap.zoho.com")[2]
        print(f"✓ DNS Resolution for imap.zoho.com: {ips}")
    except Exception as e:
        print(f"✗ DNS Resolution for imap.zoho.com FAILED: {e}")

def test_python_modules():
    """Test required Python modules"""
    print("\n=== PYTHON MODULES TEST ===")
    
    modules = ['socket', 'ssl', 'imaplib', 'email', 'json']
    for module in modules:
        try:
            __import__(module)
            print(f"✓ Module {module}: Available")
        except ImportError as e:
            print(f"✗ Module {module}: MISSING - {e}")

def test_firewall_antivirus():
    """Test for potential firewall/antivirus issues"""
    print("\n=== FIREWALL/ANTIVIRUS CHECK ===")
    
    # Test if we can make HTTPS requests (similar to IMAPS)
    try:
        import urllib.request
        with urllib.request.urlopen('https://www.google.com', timeout=10) as response:
            print("✓ HTTPS connections work (Google test)")
    except Exception as e:
        print(f"✗ HTTPS connections blocked: {e}")
        print("  This suggests firewall/antivirus is blocking SSL connections")

def main():
    """Run comprehensive diagnostics"""
    print("ZOHO IMAP CONNECTION DIAGNOSTICS")
    print("=" * 50)
    
    # Environment info
    test_environment_info()
    
    # Python modules
    test_python_modules()
    
    # Firewall test
    test_firewall_antivirus()
    
    # Basic connectivity
    working_ips = test_basic_connectivity()
    if not working_ips:
        print("\n❌ CRITICAL: No TCP connections work. Check your internet connection.")
        return
    
    # SSL connectivity
    ssl_working = test_ssl_connectivity(working_ips)
    if not ssl_working:
        print("\n❌ CRITICAL: No SSL connections work. Check firewall/antivirus settings.")
        return
    
    # IMAP library test
    imap_working = test_imaplib_connection(ssl_working)
    if not imap_working:
        print("\n❌ CRITICAL: imaplib connections fail. Python SSL configuration issue.")
        return
    
    print(f"\n✅ SUCCESS: IMAP connection works with IP {imap_working}")
    print("\n=== RECOMMENDATIONS ===")
    print(f"1. Use IP address: {imap_working}")
    print("2. Use Method 3 (Manual socket + SSL wrap) for best compatibility")
    print("3. Ensure firewall allows Python.exe outbound connections on port 993")

if __name__ == "__main__":
    main()
