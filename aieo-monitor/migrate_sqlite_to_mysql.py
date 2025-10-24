#!/usr/bin/env python3
"""
Migrate data from old SQLite database to new MySQL database
Run this once if you have existing data in query_responses.db
"""
import os
import sys
import sqlite3
import pymysql
import json
from datetime import datetime
from dotenv import load_dotenv

load_dotenv()


def migrate():
    """Migrate data from SQLite to MySQL"""
    
    print("\n" + "="*60)
    print("SQLite → MySQL Migration Tool")
    print("="*60 + "\n")
    
    # Check if SQLite database exists
    if not os.path.exists('query_responses.db'):
        print("❌ No query_responses.db file found.")
        print("Nothing to migrate!")
        return
    
    # Connect to SQLite
    print("Connecting to SQLite database...")
    sqlite_conn = sqlite3.connect('query_responses.db')
    sqlite_conn.row_factory = sqlite3.Row
    sqlite_cursor = sqlite_conn.cursor()
    
    # Connect to MySQL
    print("Connecting to MySQL database...")
    try:
        mysql_conn = pymysql.connect(
            host=os.getenv('MYSQL_HOST'),
            user=os.getenv('MYSQL_USER'),
            password=os.getenv('MYSQL_PASSWORD'),
            database=os.getenv('MYSQL_DATABASE'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
    except Exception as e:
        print(f"❌ MySQL connection failed: {e}")
        sqlite_conn.close()
        return
    
    print("✅ Both databases connected\n")
    
    # Get data from SQLite
    sqlite_cursor.execute("SELECT * FROM responses ORDER BY timestamp ASC")
    rows = sqlite_cursor.fetchall()
    
    if not rows:
        print("No data to migrate!")
        sqlite_conn.close()
        mysql_conn.close()
        return
    
    print(f"Found {len(rows)} records to migrate\n")
    
    # Create a migration run
    run_id = f"migration_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
    
    with mysql_conn.cursor() as cursor:
        # Create run record
        cursor.execute("""
            INSERT INTO runs (run_id, started_at, status, notes)
            VALUES (%s, %s, %s, %s)
        """, (run_id, datetime.now(), 'migrating', 'SQLite migration in progress'))
        mysql_conn.commit()
    
    # Migrate each record
    migrated = 0
    errors = 0
    
    for row in rows:
        try:
            # Extract data from SQLite row
            timestamp = row['timestamp']
            query_text = row['query']
            model_id = row['model'] if row['model'] else 'unknown'
            response = row['response']
            paintballevents_ref = bool(row['paintballevents_referenced'])
            search_query = row['search_query']
            cited_urls = row['cited_urls']
            
            # Generate a query_id from the text (first 50 chars, sanitized)
            query_id = 'migrated_' + ''.join(c for c in query_text[:30] if c.isalnum() or c == ' ').replace(' ', '_').lower()
            
            # Ensure model exists in models table
            with mysql_conn.cursor() as cursor:
                cursor.execute("SELECT id FROM models WHERE id = %s", (model_id,))
                if not cursor.fetchone():
                    # Insert unknown model
                    cursor.execute("""
                        INSERT INTO models (id, name, provider, active)
                        VALUES (%s, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE id=id
                    """, (model_id, model_id, 'unknown', False))
                
                # Ensure query exists in queries table
                cursor.execute("SELECT id FROM queries WHERE id = %s", (query_id,))
                if not cursor.fetchone():
                    cursor.execute("""
                        INSERT INTO queries (id, query_text, category, active)
                        VALUES (%s, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE id=id
                    """, (query_id, query_text, 'migrated', True))
                
                # Insert response
                cursor.execute("""
                    INSERT INTO responses 
                    (run_id, timestamp, query_id, model_id, query_text, response,
                     paintballevents_referenced, search_query, cited_urls)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
                """, (
                    run_id,
                    timestamp,
                    query_id,
                    model_id,
                    query_text,
                    response,
                    paintballevents_ref,
                    search_query,
                    cited_urls  # Already JSON string
                ))
                
                mysql_conn.commit()
                migrated += 1
                
                if migrated % 10 == 0:
                    print(f"Migrated {migrated}/{len(rows)} records...")
        
        except Exception as e:
            print(f"❌ Error migrating record: {e}")
            errors += 1
            continue
    
    # Update run status
    with mysql_conn.cursor() as cursor:
        cursor.execute("""
            UPDATE runs 
            SET completed_at = %s, status = %s, queries_executed = %s, errors_count = %s,
                notes = %s
            WHERE run_id = %s
        """, (
            datetime.now(),
            'completed',
            migrated,
            errors,
            f'Migrated {migrated} records from SQLite',
            run_id
        ))
        mysql_conn.commit()
    
    # Close connections
    sqlite_conn.close()
    mysql_conn.close()
    
    print("\n" + "="*60)
    print(f"✅ Migration complete!")
    print(f"   Migrated: {migrated} records")
    print(f"   Errors: {errors}")
    print(f"   Run ID: {run_id}")
    print("="*60 + "\n")
    
    if errors == 0:
        print("🎉 Perfect! You can now safely delete query_responses.db")
    else:
        print("⚠️  Some errors occurred. Review the output above.")


if __name__ == "__main__":
    migrate()

