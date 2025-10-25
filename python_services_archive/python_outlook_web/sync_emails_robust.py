#!/usr/bin/env python3
"""
Robust Email Sync Script - Fresh Connection Per Batch
Ensures IMAP connections never break by creating fresh connections for each batch
"""

import imaplib
import email
import sys
import json
import socket
import ssl
import traceback
import os
import boto3
import time
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

def create_fresh_imap_connection(provider, email_user, token, max_retries=3):
    """
    Create a fresh IMAP connection with retry logic
    Never reuse connections - always create new ones
    """
    
    for attempt in range(max_retries):
        imap = None
        try:
            print(f"DEBUG: Creating fresh IMAP connection (attempt {attempt + 1})", file=sys.stderr)
            
            if provider.lower() == 'zoho':
                # Create SSL context
                ssl_context = _create_robust_ssl_context()
                
                # Create fresh IMAP connection
                imap = imaplib.IMAP4_SSL('imap.zoho.com', 993, ssl_context=ssl_context)
                
                # Set socket timeout
                imap.sock.settimeout(30)
                
                # Login with credentials
                imap.login(email_user, token)
                
                print(f"DEBUG: Fresh IMAP connection created successfully", file=sys.stderr)
                return imap
                
        except Exception as e:
            print(f"DEBUG: Connection attempt {attempt + 1} failed: {str(e)}", file=sys.stderr)
            
            # Clean up failed connection
            if imap:
                try:
                    imap.logout()
                except:
                    pass
            
            if attempt < max_retries - 1:
                # Wait before retry with exponential backoff
                wait_time = (attempt + 1) * 2
                print(f"DEBUG: Waiting {wait_time} seconds before retry", file=sys.stderr)
                time.sleep(wait_time)
            else:
                # Final attempt failed
                raise Exception(f"Failed to create IMAP connection after {max_retries} attempts: {str(e)}")
    
    return None

def safely_close_connection(imap):
    """
    Safely close IMAP connection with proper cleanup
    """
    if not imap:
        return
        
    try:
        print(f"DEBUG: Closing IMAP connection safely", file=sys.stderr)
        imap.close()
        imap.logout()
        print(f"DEBUG: IMAP connection closed successfully", file=sys.stderr)
    except Exception as e:
        print(f"DEBUG: Error closing connection (non-fatal): {str(e)}", file=sys.stderr)

def build_search_criteria(start_date, end_date):
    """Build IMAP search criteria based on date range"""
    criteria = []
    
    if start_date and start_date != 'None':
        try:
            start_dt = datetime.strptime(start_date, '%Y-%m-%d')
            criteria.append(f'SINCE "{start_dt.strftime("%d-%b-%Y")}"')
        except:
            pass
    
    if end_date and end_date != 'None':
        try:
            end_dt = datetime.strptime(end_date, '%Y-%m-%d')
            criteria.append(f'BEFORE "{end_dt.strftime("%d-%b-%Y")}"')
        except:
            pass
    
    return ' '.join(criteria) if criteria else 'ALL'

def fetch_email_by_id(imap, email_id):
    """
    Fetch a single email by ID with error handling
    """
    try:
        # Fetch email headers and body
        typ, msg_data = imap.fetch(email_id, '(RFC822)')
        
        if typ != 'OK' or not msg_data or not msg_data[0]:
            return None
            
        raw_email = msg_data[0][1]
        email_message = email.message_from_bytes(raw_email)
        
        # Extract basic email data
        email_data = {
            'message_id': email_message.get('Message-ID', ''),
            'subject': email_message.get('Subject', ''),
            'from': email_message.get('From', ''),
            'to': email_message.get('To', ''),
            'cc': email_message.get('CC', ''),
            'date': email_message.get('Date', ''),
            'body': '',
            'html_body': '',
            'attachments': []
        }
        
        # Extract body content
        if email_message.is_multipart():
            for part in email_message.walk():
                content_type = part.get_content_type()
                content_disposition = str(part.get("Content-Disposition"))
                
                if content_type == "text/plain" and "attachment" not in content_disposition:
                    try:
                        email_data['body'] = part.get_payload(decode=True).decode('utf-8', errors='ignore')
                    except:
                        pass
                elif content_type == "text/html" and "attachment" not in content_disposition:
                    try:
                        email_data['html_body'] = part.get_payload(decode=True).decode('utf-8', errors='ignore')
                    except:
                        pass
        else:
            try:
                email_data['body'] = email_message.get_payload(decode=True).decode('utf-8', errors='ignore')
            except:
                pass
        
        return email_data
        
    except Exception as e:
        print(f"DEBUG: Error fetching email {email_id}: {str(e)}", file=sys.stderr)
        return None

def fetch_single_batch_robust(provider, email_user, token, folder, offset, batch_size, start_date, end_date):
    """
    Fetch EXACTLY ONE BATCH of emails with fresh connection
    This function is called once per batch by Laravel
    """
    
    emails = []
    imap = None
    
    try:
        print(f"DEBUG: Starting batch fetch - offset: {offset}, batch_size: {batch_size}", file=sys.stderr)
        
        # Step 1: Create fresh IMAP connection
        imap = create_fresh_imap_connection(provider, email_user, token)
        
        # Step 2: Select folder
        print(f"DEBUG: Selecting folder: {folder}", file=sys.stderr)
        imap.select(folder)
        
        # Step 3: Build search criteria
        search_criteria = build_search_criteria(start_date, end_date)
        print(f"DEBUG: Search criteria: {search_criteria}", file=sys.stderr)
        
        # Step 4: Search for emails
        typ, message_numbers = imap.search(None, search_criteria)
        
        if typ != 'OK':
            raise Exception("IMAP search failed")
        
        email_ids = message_numbers[0].split() if message_numbers[0] else []
        total_emails = len(email_ids)
        
        print(f"DEBUG: Found {total_emails} total emails matching criteria", file=sys.stderr)
        
        # Step 5: Calculate batch range
        start_idx = offset
        end_idx = min(offset + batch_size, total_emails)
        
        if start_idx >= total_emails:
            print(f"DEBUG: Offset {offset} exceeds total emails {total_emails}", file=sys.stderr)
            return []
        
        batch_email_ids = email_ids[start_idx:end_idx]
        print(f"DEBUG: Processing batch: emails {start_idx} to {end_idx-1} ({len(batch_email_ids)} emails)", file=sys.stderr)
        
        # Step 6: Fetch emails in this batch
        for i, email_id in enumerate(batch_email_ids):
            try:
                print(f"DEBUG: Fetching email {start_idx + i + 1}/{total_emails} (ID: {email_id.decode()})", file=sys.stderr)
                
                email_data = fetch_email_by_id(imap, email_id)
                
                if email_data:
                    emails.append(email_data)
                    print(f"DEBUG: Successfully fetched email: {email_data.get('subject', 'No Subject')[:50]}", file=sys.stderr)
                else:
                    print(f"DEBUG: Failed to fetch email ID: {email_id.decode()}", file=sys.stderr)
                
                # Small delay to be gentle with server
                time.sleep(0.1)
                
            except Exception as e:
                print(f"DEBUG: Error processing email {email_id}: {str(e)}", file=sys.stderr)
                continue
        
        print(f"DEBUG: Batch completed successfully - fetched {len(emails)} emails", file=sys.stderr)
        return emails
        
    except Exception as e:
        print(f"DEBUG: Batch fetch failed: {str(e)}", file=sys.stderr)
        print(f"DEBUG: Traceback: {traceback.format_exc()}", file=sys.stderr)
        raise e
        
    finally:
        # ALWAYS cleanup connection
        safely_close_connection(imap)

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
        print(f"DEBUG: S3 upload failed: {str(e)}", file=sys.stderr)
        return False

def main():
    """
    Main function - processes EXACTLY ONE BATCH per execution
    Laravel calls this script multiple times for multiple batches
    """
    
    try:
        # Validate arguments
        if len(sys.argv) < 13:
            print(json.dumps({"error": "Insufficient arguments provided"}))
            return
        
        # Parse arguments
        provider = sys.argv[1]
        email_user = sys.argv[2]
        token = sys.argv[3]
        folder = sys.argv[4]
        batch_size = int(sys.argv[5])
        start_date = sys.argv[6]
        end_date = sys.argv[7]
        aws_access_key = sys.argv[8]
        aws_secret_key = sys.argv[9]
        aws_region = sys.argv[10]
        aws_bucket = sys.argv[11]
        offset = int(sys.argv[12])
        
        print(f"DEBUG: Starting robust email sync", file=sys.stderr)
        print(f"DEBUG: Provider: {provider}, Email: {email_user}, Folder: {folder}", file=sys.stderr)
        print(f"DEBUG: Batch size: {batch_size}, Offset: {offset}", file=sys.stderr)
        print(f"DEBUG: Date range: {start_date} to {end_date}", file=sys.stderr)
        
        # Fetch single batch with fresh connection
        emails = fetch_single_batch_robust(
            provider, email_user, token, folder, 
            offset, batch_size, start_date, end_date
        )
        
        # Return results as JSON
        result = {
            "success": True,
            "emails": emails,
            "count": len(emails),
            "offset": offset,
            "batch_size": batch_size
        }
        
        print(json.dumps(result))
        
    except Exception as e:
        error_result = {
            "success": False,
            "error": str(e),
            "traceback": traceback.format_exc()
        }
        print(json.dumps(error_result))

if __name__ == "__main__":
    main()
