#!/usr/bin/env python3
"""
Network diagnostics script for troubleshooting email connectivity issues.
Tests DNS resolution, socket connections, SSL certificates, and IMAP connectivity.

Usage: python test_network.py <hostname> [port]
"""

import sys
import socket
import ssl
import json
import time
import imaplib
from datetime import datetime
from typing import Dict, Any, Optional


def resolve_hostname_with_fallback(hostname: str, port: int = 993) -> Dict[str, Any]:
    """Resolve hostname with multiple fallback methods"""
    result = {
        "hostname": hostname,
        "port": port,
        "resolved_ips": [],
        "method": None,
        "error": None
    }
    
    try:
        # Method 1: Standard socket.gethostbyname
        try:
            ip = socket.gethostbyname(hostname)
            result["resolved_ips"].append(ip)
            result["method"] = "gethostbyname"
            return result
        except socket.gaierror as e:
            print(f"DEBUG: gethostbyname failed: {e}", file=sys.stderr)
        
        # Method 2: socket.getaddrinfo
        try:
            addrinfo = socket.getaddrinfo(hostname, port, socket.AF_INET)
            ips = [info[4][0] for info in addrinfo]
            result["resolved_ips"] = list(set(ips))  # Remove duplicates
            result["method"] = "getaddrinfo"
            return result
        except socket.gaierror as e:
            print(f"DEBUG: getaddrinfo failed: {e}", file=sys.stderr)
        
        # Method 3: Try with different socket families
        try:
            addrinfo = socket.getaddrinfo(hostname, port, socket.AF_UNSPEC)
            ips = [info[4][0] for info in addrinfo if info[0] == socket.AF_INET]
            if ips:
                result["resolved_ips"] = list(set(ips))
                result["method"] = "getaddrinfo_unspec"
                return result
        except socket.gaierror as e:
            print(f"DEBUG: getaddrinfo_unspec failed: {e}", file=sys.stderr)
        
        result["error"] = "All DNS resolution methods failed"
        return result
        
    except Exception as e:
        result["error"] = f"DNS resolution error: {str(e)}"
        return result


def test_network_connectivity(hostname: str, port: int = 993) -> Dict[str, Any]:
    """Test network connectivity and DNS resolution"""
    debug_info = {
        "hostname": hostname,
        "port": port,
        "dns_resolution": None,
        "socket_connection": None,
        "ssl_connection": None,
        "error_details": []
    }
    
    try:
        # Test DNS resolution with fallback methods
        print(f"DEBUG: Testing DNS resolution for {hostname}", file=sys.stderr)
        debug_info["dns_resolution"] = resolve_hostname_with_fallback(hostname, port)
        print(f"DEBUG: DNS resolved to: {debug_info['dns_resolution']}", file=sys.stderr)
        
        # Test socket connection
        print(f"DEBUG: Testing socket connection to {hostname}:{port}", file=sys.stderr)
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(10)  # 10 second timeout
        result = sock.connect_ex((hostname, port))
        sock.close()
        
        if result == 0:
            debug_info["socket_connection"] = "SUCCESS"
            print(f"DEBUG: Socket connection successful", file=sys.stderr)
        else:
            debug_info["socket_connection"] = f"FAILED (error code: {result})"
            debug_info["error_details"].append(f"Socket connection failed with error code: {result}")
            print(f"DEBUG: Socket connection failed with error code: {result}", file=sys.stderr)
            
        # Test SSL connection
        print(f"DEBUG: Testing SSL connection to {hostname}:{port}", file=sys.stderr)
        context = ssl.create_default_context()
        with socket.create_connection((hostname, port), timeout=10) as sock:
            with context.wrap_socket(sock, server_hostname=hostname) as ssock:
                debug_info["ssl_connection"] = "SUCCESS"
                print(f"DEBUG: SSL connection successful", file=sys.stderr)
                
    except socket.gaierror as e:
        debug_info["dns_resolution"] = {"error": f"DNS resolution failed: {str(e)}"}
        debug_info["error_details"].append(f"DNS resolution failed: {str(e)}")
        print(f"DEBUG: DNS resolution failed: {str(e)}", file=sys.stderr)
    except socket.timeout as e:
        debug_info["socket_connection"] = f"TIMEOUT: {str(e)}"
        debug_info["error_details"].append(f"Socket connection timeout: {str(e)}")
        print(f"DEBUG: Socket connection timeout: {str(e)}", file=sys.stderr)
    except ssl.SSLError as e:
        debug_info["ssl_connection"] = f"SSL_ERROR: {str(e)}"
        debug_info["error_details"].append(f"SSL connection failed: {str(e)}")
        print(f"DEBUG: SSL connection failed: {str(e)}", file=sys.stderr)
    except Exception as e:
        debug_info["error_details"].append(f"Unexpected error: {str(e)}")
        print(f"DEBUG: Unexpected error: {str(e)}", file=sys.stderr)
    
    return debug_info


def test_imap_connection(hostname: str, port: int = 993) -> Dict[str, Any]:
    """Test IMAP connection"""
    debug_info = {
        "hostname": hostname,
        "port": port,
        "imap_connection": None,
        "error_details": []
    }
    
    try:
        print(f"DEBUG: Testing IMAP connection to {hostname}:{port}", file=sys.stderr)
        
        # Create IMAP connection
        if port == 993:
            # SSL connection
            imap = imaplib.IMAP4_SSL(hostname, port, timeout=10)
        else:
            # Non-SSL connection
            imap = imaplib.IMAP4(hostname, port)
            imap.starttls()
        
        # Test basic IMAP commands
        imap.noop()  # Test if connection is alive
        imap.logout()
        
        debug_info["imap_connection"] = "SUCCESS"
        print(f"DEBUG: IMAP connection successful", file=sys.stderr)
        
    except imaplib.IMAP4.error as e:
        debug_info["imap_connection"] = f"IMAP_ERROR: {str(e)}"
        debug_info["error_details"].append(f"IMAP error: {str(e)}")
        print(f"DEBUG: IMAP error: {str(e)}", file=sys.stderr)
    except socket.timeout as e:
        debug_info["imap_connection"] = f"TIMEOUT: {str(e)}"
        debug_info["error_details"].append(f"IMAP connection timeout: {str(e)}")
        print(f"DEBUG: IMAP connection timeout: {str(e)}", file=sys.stderr)
    except Exception as e:
        debug_info["imap_connection"] = f"ERROR: {str(e)}"
        debug_info["error_details"].append(f"IMAP connection failed: {str(e)}")
        print(f"DEBUG: IMAP connection failed: {str(e)}", file=sys.stderr)
    
    return debug_info


def run_comprehensive_test(hostname: str, port: int = 993) -> Dict[str, Any]:
    """Run comprehensive network and IMAP tests"""
    print(f"Starting comprehensive network test for {hostname}:{port}", file=sys.stderr)
    
    # Test 1: Network connectivity
    print("Running network connectivity tests...", file=sys.stderr)
    network_results = test_network_connectivity(hostname, port)
    
    # Test 2: IMAP connection
    print("Running IMAP connection tests...", file=sys.stderr)
    imap_results = test_imap_connection(hostname, port)
    
    # Determine individual test results (for PHP compatibility)
    dns_ok = bool(network_results.get("dns_resolution", {}).get("resolved_ips"))
    socket_ok = network_results.get("socket_connection") == "SUCCESS"
    ssl_ok = network_results.get("ssl_connection") == "SUCCESS"
    imap_ok = imap_results.get("imap_connection") == "SUCCESS"
    
    # Create results in the format expected by PHP
    results = {
        "dns": dns_ok,
        "socket": socket_ok,
        "ssl": ssl_ok,
        "imap": imap_ok,
        "overall": dns_ok and socket_ok and ssl_ok and imap_ok,
        "timestamp": datetime.now().isoformat(),
        "hostname": hostname,
        "port": port,
        "detailed_results": {
            "network": network_results,
            "imap": imap_results
        }
    }
    
    # Add summary message
    if results["overall"]:
        results["message"] = "All tests passed successfully"
    elif dns_ok and socket_ok and ssl_ok:
        results["message"] = "Network tests passed, but IMAP connection failed"
    else:
        results["message"] = "Network connectivity tests failed"
    
    # Collect all error details
    all_errors = []
    if network_results.get("error_details"):
        all_errors.extend(network_results["error_details"])
    if imap_results.get("error_details"):
        all_errors.extend(imap_results["error_details"])
    
    if all_errors:
        results["errors"] = all_errors
    
    return results


def main():
    """Main function"""
    if len(sys.argv) < 2:
        print("Usage: python test_network.py <hostname> [port]", file=sys.stderr)
        print("Example: python test_network.py imap.zoho.com 993", file=sys.stderr)
        sys.exit(1)
    
    hostname = sys.argv[1]
    port = int(sys.argv[2]) if len(sys.argv) > 2 else 993
    
    try:
        # Run comprehensive tests
        results = run_comprehensive_test(hostname, port)
        
        # Output results as JSON
        print(json.dumps(results, indent=2))
        
        # Save results to file
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        filename = f"network_test_{hostname.replace('.', '_')}_{timestamp}.json"
        
        with open(filename, 'w') as f:
            json.dump(results, f, indent=2)
        
        print(f"Detailed results saved to: {filename}", file=sys.stderr)
        
        # Exit with appropriate code
        if results["overall"]:
            sys.exit(0)
        elif results["dns"] and results["socket"] and results["ssl"]:
            sys.exit(1)  # Network OK but IMAP failed
        else:
            sys.exit(2)  # Network failed
            
    except KeyboardInterrupt:
        print("Test interrupted by user", file=sys.stderr)
        sys.exit(130)
    except Exception as e:
        print(f"Unexpected error: {str(e)}", file=sys.stderr)
        sys.exit(3)


if __name__ == "__main__":
    main()
