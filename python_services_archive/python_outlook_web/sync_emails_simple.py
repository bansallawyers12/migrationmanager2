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
    """Try multiple methods to resolve hostname"""
    methods = [
        # Method 1: Standard getaddrinfo
        lambda: socket.getaddrinfo(hostname, port, socket.AF_UNSPEC, socket.SOCK_STREAM),
        # Method 2: IPv4 only
        lambda: socket.getaddrinfo(hostname, port, socket.AF_INET, socket.SOCK_STREAM),
        # Method 3: gethostbyname
        lambda: [(socket.AF_INET, socket.SOCK_STREAM, 0, '', (socket.gethostbyname(hostname), port))],
    ]
    
    for i, method in enumerate(methods, 1):
        try:
            print(f"DEBUG: Trying DNS resolution method {i} for {hostname}", file=sys.stderr)
            result = method()
            ip_addresses = [ip[4][0] for ip in result]
            print(f"DEBUG: Method {i} succeeded: {ip_addresses}", file=sys.stderr)
            return ip_addresses
        except Exception as e:
            print(f"DEBUG: Method {i} failed: {e}", file=sys.stderr)
            continue
    
    raise socket.gaierror(f"All DNS resolution methods failed for {hostname}")

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

def fetch_emails(provider, email_user, token, folder, start_date, end_date, limit,
                 aws_access_key, aws_secret_key, aws_region, aws_bucket):
    """Fetch emails with simple, reliable approach"""
    try:
        # Test network connectivity first
        network_debug = test_network_connectivity("imap.zoho.com", 993)
        
        # Create IMAP connection
        mail = create_imap_connection_with_fallback("imap.zoho.com", 993)
        
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

        # Get the most recent emails up to the limit
        mail_ids = mail_ids[-limit:] if len(mail_ids) > limit else mail_ids
        
        print(f"DEBUG: Found {len(mail_ids)} emails to process", file=sys.stderr)
        
        messages = []
        
        # Process emails one by one (simpler, more reliable)
        for i, mail_id in enumerate(mail_ids):
            try:
                print(f"DEBUG: Processing email {i+1}/{len(mail_ids)} (ID: {mail_id})", file=sys.stderr)
                
                # Fetch individual email
                status, msg_data = mail.fetch(mail_id, "(RFC822 UID)")
                if status != "OK" or not msg_data:
                    print(f"DEBUG: Failed to fetch email {mail_id}", file=sys.stderr)
                    continue
                
                # Get the message data
                if isinstance(msg_data[0], tuple) and len(msg_data[0]) == 2:
                    header, msg_bytes = msg_data[0]
                else:
                    print(f"DEBUG: Unexpected message data format for {mail_id}", file=sys.stderr)
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
                if isinstance(msg_bytes, bytes):
                    msg = email.message_from_bytes(msg_bytes)
                else:
                    print(f"DEBUG: Unexpected message data type for {mail_id}: {type(msg_bytes)}", file=sys.stderr)
                    continue
                
                # Extract message ID for uniqueness
                message_id = msg.get("Message-ID")
                if not message_id:
                    message_id = f"uid_{imap_uid}" if imap_uid else f"msg_{mail_id}"
                
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
                        if (filename or ('attachment' in content_disposition)) and len(attachments) < 5:  # Limit attachments per email
                            try:
                                payload = part.get_payload(decode=True) or b""
                                # Skip very large attachments (>5MB)
                                if len(payload) > 5 * 1024 * 1024:
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
                    "folder": folder,
                    "attachments": attachments,
                    "has_attachments": len(attachments) > 0
                })
                
                print(f"DEBUG: Successfully processed email {i+1}/{len(mail_ids)}", file=sys.stderr)
                
            except Exception as e:
                print(f"DEBUG: Error processing email {mail_id}: {e}", file=sys.stderr)
                continue

        mail.close()
        mail.logout()
        print(f"DEBUG: Successfully processed {len(messages)} emails", file=sys.stderr)
        return messages
        
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
    if len(sys.argv) < 6:
        print(json.dumps({"error": "Insufficient arguments"}))
        sys.exit(1)
    
    provider = sys.argv[1]
    email_user = sys.argv[2]
    token = sys.argv[3]
    folder = sys.argv[4]
    limit = int(sys.argv[5]) if len(sys.argv) > 5 else 50
    start_date = sys.argv[6] if len(sys.argv) > 6 else None
    end_date = sys.argv[7] if len(sys.argv) > 7 else None
    
    # AWS credentials (optional)
    aws_access_key = sys.argv[8] if len(sys.argv) > 8 else None
    aws_secret_key = sys.argv[9] if len(sys.argv) > 9 else None
    aws_region = sys.argv[10] if len(sys.argv) > 10 else None
    aws_bucket = sys.argv[11] if len(sys.argv) > 11 else None
    
    emails = fetch_emails(provider, email_user, token, folder, start_date, end_date, limit,
                         aws_access_key, aws_secret_key, aws_region, aws_bucket)
    print(json.dumps(emails))

