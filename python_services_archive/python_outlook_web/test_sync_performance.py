#!/usr/bin/env python3
"""
Test script to measure email sync performance
"""
import time
import sys
import json
from sync_emails_optimized import fetch_emails

def test_sync_performance(provider, email_user, token, folder, start_date, end_date, limit):
    """Test sync performance and measure timing"""
    print(f"Testing sync performance for {email_user}")
    print(f"Folder: {folder}, Date range: {start_date} to {end_date}, Limit: {limit}")
    print("-" * 50)
    
    start_time = time.time()
    
    try:
        emails = fetch_emails(provider, email_user, token, folder, start_date, end_date, limit,
                             None, None, None, None)  # No AWS credentials for test
        
        end_time = time.time()
        duration = end_time - start_time
        
        if isinstance(emails, dict) and 'error' in emails:
            print(f"❌ Error: {emails['error']}")
            return False
        
        email_count = len(emails) if isinstance(emails, list) else 0
        emails_per_second = email_count / duration if duration > 0 else 0
        
        print(f"✅ Success!")
        print(f"📧 Emails processed: {email_count}")
        print(f"⏱️  Duration: {duration:.2f} seconds")
        print(f"🚀 Speed: {emails_per_second:.2f} emails/second")
        
        if email_count > 0:
            print(f"📊 Average time per email: {duration/email_count:.3f} seconds")
        
        return True
        
    except Exception as e:
        end_time = time.time()
        duration = end_time - start_time
        print(f"❌ Exception: {e}")
        print(f"⏱️  Duration before error: {duration:.2f} seconds")
        return False

if __name__ == "__main__":
    if len(sys.argv) < 6:
        print("Usage: python test_sync_performance.py <provider> <email> <token> <folder> <limit> [start_date] [end_date]")
        sys.exit(1)
    
    provider = sys.argv[1]
    email_user = sys.argv[2]
    token = sys.argv[3]
    folder = sys.argv[4]
    limit = int(sys.argv[5])
    start_date = sys.argv[6] if len(sys.argv) > 6 else None
    end_date = sys.argv[7] if len(sys.argv) > 7 else None
    
    success = test_sync_performance(provider, email_user, token, folder, start_date, end_date, limit)
    sys.exit(0 if success else 1)

