#!/usr/bin/env python3
"""
Test MySQL connection and verify setup
Run this before deploying to GitHub Actions
"""
import os
import sys
from dotenv import load_dotenv
import pymysql

load_dotenv()

def test_connection():
    """Test MySQL connection with current environment variables"""
    
    print("\n" + "="*60)
    print("MySQL Connection Test")
    print("="*60 + "\n")
    
    # Get credentials from environment
    host = os.getenv('MYSQL_HOST')
    database = os.getenv('MYSQL_DATABASE')
    username = os.getenv('MYSQL_USER')
    password = os.getenv('MYSQL_PASSWORD')
    
    # Check if all credentials are present
    missing = []
    if not host: missing.append('MYSQL_HOST')
    if not database: missing.append('MYSQL_DATABASE')
    if not username: missing.append('MYSQL_USER')
    if not password: missing.append('MYSQL_PASSWORD')
    
    if missing:
        print("❌ Missing environment variables:")
        for var in missing:
            print(f"   - {var}")
        print("\nCreate a .env file or set these environment variables.")
        return False
    
    print(f"Host: {host}")
    print(f"Database: {database}")
    print(f"Username: {username}")
    print(f"Password: {'*' * len(password)}\n")
    
    # Try to connect
    try:
        print("Attempting connection...")
        connection = pymysql.connect(
            host=host,
            user=username,
            password=password,
            database=database,
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        
        print("✅ Connection successful!\n")
        
        # Check tables
        with connection.cursor() as cursor:
            cursor.execute("SHOW TABLES")
            tables = cursor.fetchall()
            
            if not tables:
                print("⚠️  No tables found. Run database/schema.sql first!")
                return False
            
            print("Tables found:")
            expected_tables = ['queries', 'models', 'runs', 'responses']
            found_tables = [list(t.values())[0] for t in tables]
            
            for table in expected_tables:
                if table in found_tables:
                    print(f"   ✅ {table}")
                else:
                    print(f"   ❌ {table} (missing)")
            
            # Check if we have models
            cursor.execute("SELECT COUNT(*) as count FROM models")
            model_count = cursor.fetchone()['count']
            print(f"\nModels in database: {model_count}")
            
            # Check if we have queries
            cursor.execute("SELECT COUNT(*) as count FROM queries")
            query_count = cursor.fetchone()['count']
            print(f"Queries in database: {query_count}")
            
            # Check for any existing responses
            cursor.execute("SELECT COUNT(*) as count FROM responses")
            response_count = cursor.fetchone()['count']
            print(f"Responses in database: {response_count}")
            
        connection.close()
        
        print("\n" + "="*60)
        print("✅ Database setup looks good!")
        print("="*60 + "\n")
        return True
        
    except pymysql.Error as e:
        print(f"❌ Connection failed: {e}\n")
        print("Common issues:")
        print("  - Check that Remote MySQL is enabled in cPanel")
        print("  - Verify host, username, and password are correct")
        print("  - Ensure database exists")
        print("  - Check firewall settings")
        return False
    
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        return False


if __name__ == "__main__":
    success = test_connection()
    sys.exit(0 if success else 1)

