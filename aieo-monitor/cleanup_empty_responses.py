#!/usr/bin/env python3
"""
Cleanup script to remove empty responses from the database
These are typically from failed queries (out of credits, API errors, etc.)
"""
import os
import sys
from dotenv import load_dotenv

# Add current directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from database.operations import DatabaseManager

# Load environment variables
load_dotenv()


def cleanup_empty_responses():
    """Remove all responses with empty or whitespace-only response text"""
    
    print("="*80)
    print("CLEANUP: Removing Empty Responses")
    print("="*80)
    print()
    
    db = DatabaseManager()
    
    try:
        with db.connection.cursor() as cursor:
            # First, count how many empty responses we have
            cursor.execute("""
                SELECT COUNT(*) as count
                FROM responses
                WHERE response = '' OR response IS NULL OR TRIM(response) = ''
            """)
            result = cursor.fetchone()
            empty_count = result['count']
            
            print(f"Found {empty_count} empty responses to delete")
            
            if empty_count == 0:
                print("✓ No empty responses found. Database is clean!")
                return
            
            # Show sample of what will be deleted
            print("\nSample records to be deleted:")
            cursor.execute("""
                SELECT id, run_id, model_id, query_id, error, timestamp
                FROM responses
                WHERE response = '' OR response IS NULL OR TRIM(response) = ''
                ORDER BY timestamp DESC
                LIMIT 5
            """)
            samples = cursor.fetchall()
            
            for sample in samples:
                print(f"  ID: {sample['id']}, Model: {sample['model_id']}, "
                      f"Query: {sample['query_id']}, Error: {sample['error'][:50] if sample['error'] else 'None'}")
            
            # Confirm deletion
            print()
            confirm = input(f"Delete {empty_count} empty responses? (yes/no): ")
            
            if confirm.lower() != 'yes':
                print("✗ Cleanup cancelled")
                return
            
            # Delete empty responses
            cursor.execute("""
                DELETE FROM responses
                WHERE response = '' OR response IS NULL OR TRIM(response) = ''
            """)
            
            db.connection.commit()
            
            print(f"✓ Successfully deleted {empty_count} empty responses")
            
            # Show updated statistics
            cursor.execute("SELECT COUNT(*) as count FROM responses")
            result = cursor.fetchone()
            remaining_count = result['count']
            
            print(f"✓ Remaining responses in database: {remaining_count}")
            
    except Exception as e:
        print(f"✗ Error during cleanup: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
    
    finally:
        db.close()
    
    print()
    print("="*80)
    print("Cleanup complete!")
    print("="*80)


if __name__ == "__main__":
    try:
        cleanup_empty_responses()
    except KeyboardInterrupt:
        print("\n\n✗ Interrupted by user")
        sys.exit(1)

