import imaplib
import email
import sys
import json
import socket
import ssl
import traceback
import os
import boto3
from datetime import datetime, timedelta
from email.utils import parsedate_to_datetime
from pathlib import Path
import concurrent.futures
import threading

def _create_robust_ssl_context():
    """Create an SSL context with strong defaults and certifi CA fallback."""
    try:
        import certifi
        cafile = certifi.where()
    except Exception:
        cafile = None

    try:
        context = ssl.create_default_context(cafile=cafile)
    except Exception:
        context = ssl.create_default_context()

    try:
        context.minimum_version = ssl.TLSVersion.TLSv1_2
    except Exception:
        pass

    context.check_hostname = True
    context.verify_mode = ssl.CERT_REQUIRED
    return context

def upload_to_s3(file_path, s3_key, bucket_name, aws_access_key, aws_secret_key, region):
    """Upload file to S3 bucket"""
    try:
        s3_client = boto3.client(
            's3',
            aws_access_key_id=aws_access_key,
            aws_secret_access_key=aws_secret_key,
            region_name=region
        )
        
        s3_client.upload_file(file_path, bucket_name, s3_key)
        print(f"DEBUG: Uploaded {file_path} to s3://{bucket_name}/{s3_key}", file=sys.stderr)
        return True
    except Exception as e:
        print(f"DEBUG: Failed to upload to S3: {e}", file=sys.stderr)
        return False

def resolve_hostname_with_fallback(hostname, port=993):
    """Try multiple methods to resolve hostname with enhanced fallbacks"""
    # Try direct IP first (Zoho IMAP known IPs - updated)
    known_ips = {
        'imap.zoho.com': ['74.201.86.24', '74.201.86.25', '74.201.86.26', '136.143.190.67', '136.143.190.68']
    }
    
    if hostname in known_ips:
        print(f"DEBUG: Using known IP addresses for {hostname}: {known_ips[hostname]}", file=sys.stderr)
        return known_ips[hostname]
    
    methods = [
        # Method 1: Standard getaddrinfo with timeout
        lambda: socket.getaddrinfo(hostname, port, socket.AF_UNSPEC, socket.SOCK_STREAM),
        # Method 2: IPv4 only with timeout
        lambda: socket.getaddrinfo(hostname, port, socket.AF_INET, socket.SOCK_STREAM),
        # Method 3: gethostbyname with timeout
        lambda: [(socket.AF_INET, socket.SOCK_STREAM, 0, '', (socket.gethostbyname(hostname), port))],
        # Method 4: Using Google DNS
        lambda: resolve_with_dns_server(hostname, '8.8.8.8'),
        # Method 5: Using Cloudflare DNS
        lambda: resolve_with_dns_server(hostname, '1.1.1.1'),
    ]
    
    for i, method in enumerate(methods, 1):
        try:
            print(f"DEBUG: Trying DNS resolution method {i} for {hostname}", file=sys.stderr)
            
            # Set socket timeout for DNS operations
            old_timeout = socket.getdefaulttimeout()
            socket.setdefaulttimeout(10.0)  # 10 second timeout
            
            result = method()
            
            # Restore original timeout
            socket.setdefaulttimeout(old_timeout)
            
            if isinstance(result, list) and len(result) > 0:
                if isinstance(result[0], str):
                    # Direct IP list
                    ip_addresses = result
                else:
                    # getaddrinfo result
                    ip_addresses = [ip[4][0] for ip in result]
                print(f"DEBUG: Method {i} succeeded: {ip_addresses}", file=sys.stderr)
                return ip_addresses
        except Exception as e:
            print(f"DEBUG: Method {i} failed: {e}", file=sys.stderr)
            socket.setdefaulttimeout(old_timeout)  # Restore timeout even on error
            continue
    
    raise socket.gaierror(f"All DNS resolution methods failed for {hostname}")

def resolve_with_dns_server(hostname, dns_server):
    """Try to resolve hostname using specific DNS server"""
    try:
        import subprocess
        import platform
        
        if platform.system() == "Windows":
            # Use nslookup on Windows
            result = subprocess.run(['nslookup', hostname, dns_server], 
                                  capture_output=True, text=True, timeout=10)
            if result.returncode == 0:
                lines = result.stdout.split('\n')
                ips = []
                for line in lines:
                    if 'Address:' in line and not dns_server in line:
                        ip = line.split('Address:')[1].strip()
                        if ip and '.' in ip:
                            ips.append(ip)
                return ips
        else:
            # Use dig on Linux
            result = subprocess.run(['dig', f'@{dns_server}', hostname, '+short'], 
                                  capture_output=True, text=True, timeout=10)
            if result.returncode == 0:
                ips = [ip.strip() for ip in result.stdout.split('\n') if ip.strip() and '.' in ip.strip()]
                return ips
    except Exception as e:
        print(f"DEBUG: DNS server {dns_server} resolution failed: {e}", file=sys.stderr)
    
    return []

def create_imap_connection_with_fallback(hostname, port=993):
    """Create IMAP connection with DNS fallback"""
    try:
        # Try hostname first
        print(f"DEBUG: Attempting IMAP connection to {hostname}:{port}", file=sys.stderr)
        # Attempt with default context first, then robust context
        try:
            mail = imaplib.IMAP4_SSL(hostname, port)
        except Exception as e1:
            print(f"DEBUG: Default SSL context failed: {e1}", file=sys.stderr)
            mail = imaplib.IMAP4_SSL(hostname, port, ssl_context=_create_robust_ssl_context())
        print(f"DEBUG: IMAP connection established using hostname", file=sys.stderr)
        return mail
    except socket.gaierror as e:
        print(f"DEBUG: Hostname connection failed: {e}", file=sys.stderr)
        # Try with resolved IP address
        try:
            ip_addresses = resolve_hostname_with_fallback(hostname, port)
            if ip_addresses:
                ip_address = ip_addresses[0]
                print(f"DEBUG: Attempting IMAP connection using IP address: {ip_address}", file=sys.stderr)
                try:
                    mail = imaplib.IMAP4_SSL(ip_address, port)
                except Exception as e2:
                    print(f"DEBUG: IP SSL connection failed: {e2}", file=sys.stderr)
                    mail = imaplib.IMAP4_SSL(ip_address, port, ssl_context=_create_robust_ssl_context())
                print(f"DEBUG: IMAP connection established using IP address", file=sys.stderr)
                return mail
        except Exception as e3:
            print(f"DEBUG: IP connection failed: {e3}", file=sys.stderr)
            raise e
    except Exception as e:
        print(f"DEBUG: All connection methods failed: {e}", file=sys.stderr)
        raise e

def test_network_connectivity(hostname, port=993):
    """Test network connectivity before attempting IMAP connection"""
    try:
        print(f"DEBUG: Testing network connectivity to {hostname}:{port}", file=sys.stderr)
        sock = socket.create_connection((hostname, port), timeout=10)
        sock.close()
        print(f"DEBUG: Network connectivity test passed", file=sys.stderr)
        return True
    except Exception as e:
        print(f"DEBUG: Network connectivity test failed: {e}", file=sys.stderr)
        return False

def process_email_batch(mail, mail_ids, aws_access_key, aws_secret_key, aws_region, aws_bucket):
    """Process a batch of emails efficiently"""
    messages = []
    
    # Fetch all emails in one batch operation
    if not mail_ids:
        return messages
    
    # Convert to comma-separated string for batch fetch
    mail_ids_str = ','.join([mid.decode() if isinstance(mid, bytes) else str(mid) for mid in mail_ids])
    
    try:
        # Fetch all emails at once with UIDs
        status, data = mail.fetch(mail_ids_str, "(RFC822 UID)")
        if status != "OK":
            print(f"DEBUG: Batch fetch failed: {status}", file=sys.stderr)
            return messages
        
        # Process the batch data
        print(f"DEBUG: Processing {len(data)} items from batch fetch", file=sys.stderr)
        for i, item in enumerate(data):
            if isinstance(item, tuple) and len(item) == 2:
                # Parse the response
                header, msg_data = item
                if not msg_data:
                    print(f"DEBUG: Skipping item {i} - no message data", file=sys.stderr)
                    continue
                
                # Extract UID from header
                imap_uid = None
                if isinstance(header, bytes):
                    header_str = header.decode('utf-8', errors='ignore')
                    if 'UID' in header_str:
                        try:
                            uid_part = header_str.split('UID')[1].strip().split()[0]
                            imap_uid = uid_part.rstrip(')')
                        except:
                            pass
                
                # Parse the email message
                try:
                    if isinstance(msg_data, bytes):
                        msg = email.message_from_bytes(msg_data)
                    else:
                        print(f"DEBUG: Unexpected message data type: {type(msg_data)}", file=sys.stderr)
                        continue
                    
                    # Extract message ID for uniqueness
                    message_id = msg.get("Message-ID")
                    if not message_id:
                        message_id = f"uid_{imap_uid}" if imap_uid else f"msg_{mail_ids[i] if i < len(mail_ids) else 'unknown'}"
                    
                    # Parse date properly
                    date_str = msg.get("Date")
                    parsed_date = None
                    if date_str:
                        try:
                            parsed_date = parsedate_to_datetime(date_str)
                        except:
                            parsed_date = None
                    
                    # Extract body and attachments efficiently
                    text_body = ""
                    html_body = ""
                    attachments = []
                    
                    if msg.is_multipart():
                        for part in msg.walk():
                            content_type = part.get_content_type()
                            content_disposition = (part.get("Content-Disposition") or "").lower()
                            filename = part.get_filename()

                            # Body parts (exclude attachment disposition)
                            if content_type == "text/plain" and not text_body and 'attachment' not in content_disposition:
                                payload = part.get_payload(decode=True)
                                text_body = (payload.decode('utf-8', errors='ignore') if isinstance(payload, (bytes, bytearray)) else str(payload))
                            elif content_type == "text/html" and not html_body and 'attachment' not in content_disposition:
                                payload = part.get_payload(decode=True)
                                html_body = (payload.decode('utf-8', errors='ignore') if isinstance(payload, (bytes, bytearray)) else str(payload))

                            # Process attachments only if they exist and are not too large
                            if (filename or ('attachment' in content_disposition)) and len(attachments) < 10:  # Limit attachments per email
                                try:
                                    payload = part.get_payload(decode=True) or b""
                                    # Skip very large attachments (>10MB)
                                    if len(payload) > 10 * 1024 * 1024:
                                        print(f"DEBUG: Skipping large attachment: {filename} ({len(payload)} bytes)", file=sys.stderr)
                                        continue
                                    
                                    safe_msg_id = (msg.get("Message-ID") or "noid").replace('<','').replace('>','').replace(':','_').replace('/','_').replace('\\','_')
                                    name = filename or f"attachment_{len(attachments)+1}"
                                    ext = ''
                                    if '.' in name:
                                        ext = name.split('.')[-1].lower()
                                    
                                    # Create temporary local file
                                    temp_dir = Path('storage') / 'app' / 'temp' / 'attachments' / safe_msg_id
                                    temp_dir.mkdir(parents=True, exist_ok=True)
                                    temp_file_path = temp_dir / name
                                    
                                    with open(temp_file_path, 'wb') as f:
                                        f.write(payload)
                                    
                                    file_size = temp_file_path.stat().st_size
                                    s3_path = None
                                    
                                    # Upload to S3 if credentials provided (async)
                                    if aws_access_key and aws_secret_key and aws_region and aws_bucket:
                                        s3_key = f"emails/attachments/{safe_msg_id}/{name}"
                                        if upload_to_s3(str(temp_file_path), s3_key, aws_bucket, 
                                                      aws_access_key, aws_secret_key, aws_region):
                                            s3_path = s3_key
                                            # Clean up temp file after successful upload
                                            temp_file_path.unlink()
                                        else:
                                            # Keep local file if S3 upload fails
                                            s3_path = str(temp_file_path)
                                    else:
                                        # Keep local file if no S3 credentials
                                        s3_path = str(temp_file_path)
                                    
                                    attachments.append({
                                        "filename": name,
                                        "display_name": name,
                                        "content_type": content_type,
                                        "file_size": file_size,
                                        "file_path": s3_path,
                                        "content_id": part.get('Content-ID'),
                                        "is_inline": 'inline' in content_disposition,
                                        "headers": dict(part.items()),
                                        "extension": ext,
                                    })
                                except Exception as e:
                                    print(f"DEBUG: Attachment processing error: {e}", file=sys.stderr)
                                    # Skip attachment save errors, continue processing
                                    pass
                    else:
                        payload = msg.get_payload(decode=True)
                        text_body = (payload.decode('utf-8', errors='ignore') if isinstance(payload, (bytes, bytearray)) else str(payload))
                    
                    messages.append({
                        "message_id": message_id,
                        "imap_uid": imap_uid,
                        "from": msg.get("From"),
                        "to": msg.get("To"),
                        "cc": msg.get("Cc"),
                        "reply_to": msg.get("Reply-To"),
                        "subject": msg.get("Subject"),
                        "date": date_str,
                        "parsed_date": parsed_date.isoformat() if parsed_date else None,
                        "body": text_body[:4000] if text_body else "",
                        "text_body": text_body[:4000] if text_body else "",
                        "html_body": html_body if html_body else None,
                        "headers": dict(msg.items()),
                        "folder": "Inbox",  # Will be set by caller
                        "attachments": attachments,
                        "has_attachments": len(attachments) > 0
                    })
                    
                except Exception as e:
                    print(f"DEBUG: Error processing email: {e}", file=sys.stderr)
                    continue
    
    except Exception as e:
        print(f"DEBUG: Batch processing error: {e}", file=sys.stderr)
    
    return messages

def create_imap_connection_with_dns_fix():
    """Create IMAP connection with DNS resolution fix - proper approach"""
    hostname = "imap.zoho.com"
    port = 993
    
    print(f"DEBUG: Creating IMAP connection to {hostname}:{port} with DNS fix", file=sys.stderr)
    print(f"DEBUG: Python executable: {sys.executable}", file=sys.stderr)
    print(f"DEBUG: Working directory: {os.getcwd()}", file=sys.stderr)
    
    # Method 1: Try standard connection first
    try:
        print(f"DEBUG: Attempting standard IMAP connection", file=sys.stderr)
        # Add socket timeout for better error handling
        import socket
        socket.setdefaulttimeout(30)
        
        mail = imaplib.IMAP4_SSL(hostname, port)
        print(f"DEBUG: Standard IMAP connection successful!", file=sys.stderr)
        return mail
    except Exception as e1:
        print(f"DEBUG: Standard connection failed: {e1}", file=sys.stderr)
        print(f"DEBUG: Exception type: {type(e1).__name__}", file=sys.stderr)
    
    # Method 2: Fix DNS resolution by patching socket.getaddrinfo
    try:
        print(f"DEBUG: Attempting DNS resolution fix", file=sys.stderr)
        import socket
        
        # Store original function
        original_getaddrinfo = socket.getaddrinfo
        
        def fixed_getaddrinfo(host, port, family=0, type=0, proto=0, flags=0):
            """Fixed getaddrinfo that works around Windows DNS issues"""
            if host == hostname:
                # Use our known working IP but return proper socket info
                working_ip = "136.143.190.29"
                print(f"DEBUG: DNS fix - resolving {host} to {working_ip}", file=sys.stderr)
                return [(socket.AF_INET, socket.SOCK_STREAM, 6, '', (working_ip, port))]
            else:
                # For other hosts, use original function
                return original_getaddrinfo(host, port, family, type, proto, flags)
        
        # Apply the fix
        socket.getaddrinfo = fixed_getaddrinfo
        
        try:
            # Now try connection with hostname (but fixed DNS)
            mail = imaplib.IMAP4_SSL(hostname, port)
            print(f"DEBUG: DNS fix connection successful!", file=sys.stderr)
            return mail
        finally:
            # Always restore original function
            socket.getaddrinfo = original_getaddrinfo
            
    except Exception as e2:
        print(f"DEBUG: DNS fix method failed: {e2}", file=sys.stderr)
    
    # Method 3: Manual socket connection with proper SSL
    try:
        print(f"DEBUG: Attempting manual socket + SSL connection", file=sys.stderr)
        import socket
        import ssl
        
        # Create socket and connect to working IP
        working_ip = "136.143.190.29"
        sock = socket.create_connection((working_ip, port), timeout=30)
        
        # Create SSL context and wrap socket with hostname for SNI
        context = ssl.create_default_context()
        ssl_sock = context.wrap_socket(sock, server_hostname=hostname)
        
        # Create IMAP4 instance with the SSL socket
        mail = imaplib.IMAP4(ssl_sock)
        print(f"DEBUG: Manual socket + SSL connection successful!", file=sys.stderr)
        return mail
        
    except Exception as e3:
        print(f"DEBUG: Manual socket method failed: {e3}", file=sys.stderr)
    
    raise Exception(f"All connection methods failed for {hostname}. Try checking your internet connection and firewall settings.")

def create_imap_connection_direct_ip():
    """Create IMAP connection using direct IP to bypass DNS issues with Windows compatibility"""
    # Use the working IP first (from diagnostics)
    working_ips = ["136.143.190.29"]  # Only use the IP that works
    hostname = "imap.zoho.com"
    port = 993
    
    print(f"DEBUG: Attempting direct IP connections to Zoho IMAP", file=sys.stderr)
    
    # Import platform to detect Windows
    import platform
    is_windows = platform.system() == "Windows"
    
    # Use the working IP (136.143.190.29)
    working_ip = working_ips[0]
    print(f"DEBUG: Using confirmed working IP: {working_ip}", file=sys.stderr)
    
    # Method 1: Direct imaplib connection (simplest and proven to work)
    try:
        print(f"DEBUG: Attempting direct imaplib connection to {working_ip}", file=sys.stderr)
        mail = imaplib.IMAP4_SSL(working_ip, port)
        print(f"DEBUG: Direct imaplib connection successful!", file=sys.stderr)
        return mail
    except Exception as e1:
        print(f"DEBUG: Direct imaplib failed: {e1}", file=sys.stderr)
    
    # Method 2: Try with hostname but force IP resolution (backup)
    try:
        print(f"DEBUG: Trying hostname with forced IP resolution", file=sys.stderr)
        import socket
        # Monkey patch socket.getaddrinfo to return our working IP
        original_getaddrinfo = socket.getaddrinfo
        def force_ip_getaddrinfo(host, port, family=0, type=0, proto=0, flags=0):
            if host == hostname:
                return [(socket.AF_INET, socket.SOCK_STREAM, 6, '', (working_ip, port))]
            return original_getaddrinfo(host, port, family, type, proto, flags)
        
        socket.getaddrinfo = force_ip_getaddrinfo
        try:
            mail = imaplib.IMAP4_SSL(hostname, port)
            print(f"DEBUG: Forced IP resolution successful", file=sys.stderr)
            return mail
        finally:
            socket.getaddrinfo = original_getaddrinfo
            
    except Exception as e2:
        print(f"DEBUG: Forced IP resolution failed: {e2}", file=sys.stderr)
    
    raise Exception(f"All direct IP connection methods failed for all IPs: {working_ips}")

def _create_windows_compatible_ssl_context():
    """Create SSL context specifically for Windows compatibility"""
    try:
        context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)
        context.check_hostname = True
        context.verify_mode = ssl.CERT_REQUIRED
        
        # Load default CA certificates
        context.load_default_certs()
        
        # Windows-specific: Set ciphers that work well on Windows
        try:
            context.set_ciphers('ECDHE+AESGCM:ECDHE+CHACHA20:DHE+AESGCM:DHE+CHACHA20:!aNULL:!MD5:!DSS')
        except:
            pass  # Ignore cipher setting errors
        
        # Set minimum TLS version
        try:
            context.minimum_version = ssl.TLSVersion.TLSv1_2
        except:
            pass  # Ignore if not supported
            
        return context
    except Exception as e:
        print(f"DEBUG: Failed to create Windows SSL context: {e}", file=sys.stderr)
        return ssl.create_default_context()

def fetch_emails(provider, email_user, token, folder, start_date, end_date, limit,
                 aws_access_key, aws_secret_key, aws_region, aws_bucket, offset=0):
    """Fetch emails with optimizations"""
    try:
        # Test network connectivity first
        network_debug = test_network_connectivity("imap.zoho.com", 993)
        
        # Create IMAP connection with proper DNS resolution fix
        mail = create_imap_connection_with_dns_fix()
        
        # Login
        status, data = mail.login(email_user, token)
        if status != "OK":
            return {"error": "Failed to login to email account"}

        # Map folder name
        def map_folder_name(p, fl):
            if not fl:
                return 'INBOX'
            # INBOX is case-insensitive per RFC; map explicitly
            if fl.lower() == 'inbox':
                return 'INBOX'
            # For Zoho, common folders use exact names as below
            if p == 'zoho':
                zoho_known = {
                    'sent': 'Sent',
                    'drafts': 'Drafts',
                    'spam': 'Spam',
                    'trash': 'Trash',
                    'archive': 'Archive',
                }
                return zoho_known.get(fl.lower(), fl)
            return fl

        mapped_folder = map_folder_name(provider, folder)
        print(f"DEBUG: Selecting folder: requested='{folder}', mapped='{mapped_folder}'", file=sys.stderr)
        status, data = mail.select(mapped_folder)
        if status != 'OK':
            error_msg = f"Failed to select folder '{mapped_folder}'"
            print(f"DEBUG: {error_msg}, status={status}, data={data}", file=sys.stderr)
            return {
                "error": error_msg,
                "debug_info": {
                    "provider": provider,
                    "requested_folder": folder,
                    "mapped_folder": mapped_folder,
                    "select_status": status,
                    "select_data": data,
                }
            }
        print(f"DEBUG: Folder selected successfully", file=sys.stderr)

        # Build search criteria
        search_criteria = "ALL"
        if start_date and end_date:
            # Convert dates to IMAP format (DD-MMM-YYYY)
            # IMPORTANT: IMAP BEFORE is exclusive, so add 1 day to make end date inclusive
            start_dt = datetime.strptime(start_date, "%Y-%m-%d")
            end_dt = datetime.strptime(end_date, "%Y-%m-%d")
            end_inclusive = end_dt + timedelta(days=1)
            search_criteria = f'SINCE {start_dt.strftime("%d-%b-%Y")} BEFORE {end_inclusive.strftime("%d-%b-%Y")}'
        elif start_date:
            start_dt = datetime.strptime(start_date, "%Y-%m-%d")
            search_criteria = f'SINCE {start_dt.strftime("%d-%b-%Y")}'

        status, data = mail.search(None, search_criteria)
        if status != "OK":
            return {"error": "Failed to search mailbox"}

        mail_ids = data[0].split()
        if not mail_ids:
            return []

        # Convert bytes to strings
        mail_ids = [mid.decode() if isinstance(mid, bytes) else str(mid) for mid in mail_ids]

        # Apply pagination with offset (simpler approach)
        total_emails = len(mail_ids)
        print(f"DEBUG: Total emails found: {total_emails}, offset: {offset}, limit: {limit}", file=sys.stderr)
        
        # Reverse mail_ids to get most recent first, then apply pagination
        mail_ids = mail_ids[::-1]  # Reverse to get most recent first
        
        # Apply offset and limit
        start_idx = offset
        end_idx = offset + limit
        mail_ids = mail_ids[start_idx:end_idx]
        
        print(f"DEBUG: After pagination - processing {len(mail_ids)} emails (indices {start_idx} to {end_idx})", file=sys.stderr)
        
        # Process emails in batches for better performance
        batch_size = 5  # Smaller batches to prevent timeouts
        all_messages = []
        
        for i in range(0, len(mail_ids), batch_size):
            batch_ids = mail_ids[i:i + batch_size]
            print(f"DEBUG: Processing batch {i//batch_size + 1} with {len(batch_ids)} emails", file=sys.stderr)
            print(f"DEBUG: Batch IDs: {batch_ids[:5]}...", file=sys.stderr)  # Show first 5 IDs
            batch_messages = process_email_batch(mail, batch_ids, aws_access_key, aws_secret_key, aws_region, aws_bucket)
            
            # Set folder for all messages in this batch
            for msg in batch_messages:
                msg['folder'] = folder
            
            all_messages.extend(batch_messages)
            print(f"DEBUG: Processed batch {i//batch_size + 1}, total emails: {len(all_messages)}", file=sys.stderr)

        mail.close()
        mail.logout()
        print(f"DEBUG: Successfully processed {len(all_messages)} emails", file=sys.stderr)
        return all_messages
        
    except imaplib.IMAP4.error as e:
        error_msg = f"IMAP error: {e}"
        print(f"DEBUG: {error_msg}", file=sys.stderr)
        return {
            "error": error_msg,
            "debug_info": {
                "network_test": network_debug,
                "error_type": "IMAP_ERROR",
                "traceback": traceback.format_exc()
            }
        }
    except socket.gaierror as e:
        error_msg = f"DNS resolution failed: {e}"
        print(f"DEBUG: {error_msg}", file=sys.stderr)
        return {
            "error": error_msg,
            "debug_info": {
                "network_test": network_debug,
                "error_type": "DNS_ERROR",
                "traceback": traceback.format_exc()
            }
        }
    except Exception as e:
        error_msg = f"Unexpected error: {e}"
        print(f"DEBUG: {error_msg}", file=sys.stderr)
        return {
            "error": error_msg,
            "debug_info": {
                "network_test": network_debug,
                "error_type": "UNEXPECTED_ERROR",
                "traceback": traceback.format_exc()
            }
        }

if __name__ == "__main__":
    try:
        if len(sys.argv) < 6:
            print(json.dumps({"error": "Insufficient arguments"}))
            sys.exit(1)
        
        provider = sys.argv[1]
        email_user = sys.argv[2]
        token = sys.argv[3]
        folder = sys.argv[4]
        limit = int(sys.argv[5]) if len(sys.argv) > 5 else 10  # Default to 10 for pagination
        start_date = sys.argv[6] if len(sys.argv) > 6 else None
        end_date = sys.argv[7] if len(sys.argv) > 7 else None
        
        # AWS credentials (optional)
        aws_access_key = sys.argv[8] if len(sys.argv) > 8 else None
        aws_secret_key = sys.argv[9] if len(sys.argv) > 9 else None
        aws_region = sys.argv[10] if len(sys.argv) > 10 else None
        aws_bucket = sys.argv[11] if len(sys.argv) > 11 else None
        
        # Pagination offset (new parameter)
        offset = int(sys.argv[12]) if len(sys.argv) > 12 else 0
        
        print(f"DEBUG: Starting sync with pagination - offset: {offset}, limit: {limit}", file=sys.stderr)
        
        emails = fetch_emails(provider, email_user, token, folder, start_date, end_date, limit,
                             aws_access_key, aws_secret_key, aws_region, aws_bucket, offset)
        
        # Ensure clean JSON output - only print JSON to stdout
        if isinstance(emails, dict) and 'error' in emails:
            print(json.dumps(emails))
        elif isinstance(emails, list):
            print(json.dumps(emails))
        else:
            print(json.dumps({"error": "Unexpected response format", "data": emails}))
            
    except Exception as e:
        # Catch any unhandled exceptions and return proper JSON error
        print(json.dumps({
            "error": f"Script execution error: {str(e)}",
            "debug_info": {
                "error_type": "SCRIPT_ERROR", 
                "traceback": traceback.format_exc()
            }
        }))
