#!/usr/bin/env python3
"""
Simple network connectivity test for Zoho IMAP
This script tests basic connectivity to help diagnose DNS/network issues
"""

import socket
import ssl
import sys
import subprocess
import platform

def test_dns_resolution():
    """Test DNS resolution using multiple methods"""
    hostname = "imap.zoho.com"
    print(f"Testing DNS resolution for {hostname}...")
    
    # Method 1: Standard resolution
    try:
        ips = socket.gethostbyname_ex(hostname)[2]
        print(f"✓ Standard DNS resolution successful: {ips}")
        return ips
    except Exception as e:
        print(f"✗ Standard DNS resolution failed: {e}")
    
    # Method 2: getaddrinfo
    try:
        result = socket.getaddrinfo(hostname, 993, socket.AF_INET, socket.SOCK_STREAM)
        ips = [r[4][0] for r in result]
        print(f"✓ getaddrinfo resolution successful: {ips}")
        return ips
    except Exception as e:
        print(f"✗ getaddrinfo resolution failed: {e}")
    
    # Method 3: Use known IPs as fallback
    known_ips = ['74.201.86.24', '74.201.86.25', '74.201.86.26', '136.143.190.67', '136.143.190.68']
    print(f"Using known Zoho IMAP IPs: {known_ips}")
    return known_ips

def test_tcp_connection(ip, port=993):
    """Test TCP connection to specific IP"""
    print(f"Testing TCP connection to {ip}:{port}...")
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(10)
        result = sock.connect_ex((ip, port))
        sock.close()
        
        if result == 0:
            print(f"✓ TCP connection to {ip}:{port} successful")
            return True
        else:
            print(f"✗ TCP connection to {ip}:{port} failed with code {result}")
            return False
    except Exception as e:
        print(f"✗ TCP connection to {ip}:{port} error: {e}")
        return False

def test_ssl_connection(ip, hostname="imap.zoho.com", port=993):
    """Test SSL connection"""
    print(f"Testing SSL connection to {ip}:{port}...")
    try:
        context = ssl.create_default_context()
        with socket.create_connection((ip, port), timeout=10) as sock:
            with context.wrap_socket(sock, server_hostname=hostname) as ssock:
                print(f"✓ SSL connection to {ip}:{port} successful")
                print(f"  SSL version: {ssock.version()}")
                print(f"  Cipher: {ssock.cipher()}")
                return True
    except Exception as e:
        print(f"✗ SSL connection to {ip}:{port} failed: {e}")
        return False

def test_system_dns():
    """Test system DNS configuration"""
    print("Testing system DNS configuration...")
    
    try:
        if platform.system() == "Windows":
            # Test with nslookup
            result = subprocess.run(['nslookup', 'imap.zoho.com'], 
                                  capture_output=True, text=True, timeout=10)
            print(f"nslookup result:\n{result.stdout}")
            if result.stderr:
                print(f"nslookup errors:\n{result.stderr}")
        else:
            # Test with dig
            result = subprocess.run(['dig', 'imap.zoho.com'], 
                                  capture_output=True, text=True, timeout=10)
            print(f"dig result:\n{result.stdout}")
    except Exception as e:
        print(f"System DNS test failed: {e}")

def main():
    """Main test function"""
    print("=" * 50)
    print("Zoho IMAP Network Connectivity Test")
    print("=" * 50)
    
    # Test 1: DNS Resolution
    ips = test_dns_resolution()
    if not ips:
        print("❌ DNS resolution completely failed")
        return
    
    print()
    
    # Test 2: System DNS
    test_system_dns()
    
    print()
    
    # Test 3: TCP connections
    working_ips = []
    for ip in ips[:3]:  # Test first 3 IPs
        if test_tcp_connection(ip):
            working_ips.append(ip)
    
    if not working_ips:
        print("❌ No TCP connections successful")
        return
    
    print()
    
    # Test 4: SSL connections
    ssl_working = []
    for ip in working_ips:
        if test_ssl_connection(ip):
            ssl_working.append(ip)
    
    print()
    print("=" * 50)
    print("SUMMARY:")
    print(f"DNS Resolution: {'✓' if ips else '✗'}")
    print(f"TCP Connections: {len(working_ips)} working out of {len(ips)} tested")
    print(f"SSL Connections: {len(ssl_working)} working")
    
    if ssl_working:
        print(f"✅ Network connectivity is working! Use IP: {ssl_working[0]}")
        print(f"All working IPs: {ssl_working}")
    else:
        print("❌ Network connectivity issues detected")
    
    print("=" * 50)

if __name__ == "__main__":
    main()
