"""
Database operations for AI Citation Monitor
Handles MySQL connection and CRUD operations
"""
import os
import json
import pymysql
from datetime import datetime
from typing import List, Dict, Optional


class DatabaseManager:
    """Manages MySQL database operations for the monitor"""
    
    def __init__(self):
        """Initialize database connection"""
        self._connect()
        self._ensure_schema()
    
    def _connect(self):
        """Establish database connection"""
        self.connection = pymysql.connect(
            host=os.getenv('MYSQL_HOST'),
            user=os.getenv('MYSQL_USER'),
            password=os.getenv('MYSQL_PASSWORD'),
            database=os.getenv('MYSQL_DATABASE'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor,
            autocommit=False,
            connect_timeout=60,
            read_timeout=60,
            write_timeout=60
        )
    
    def _reconnect_if_needed(self):
        """Reconnect to database if connection was lost"""
        try:
            self.connection.ping(reconnect=True)
        except Exception:
            print("⚠️  Reconnecting to MySQL...")
            self._connect()
    
    def _ensure_schema(self):
        """Ensure all tables exist (basic check)"""
        with self.connection.cursor() as cursor:
            # Check if main tables exist
            cursor.execute("SHOW TABLES LIKE 'responses'")
            if not cursor.fetchone():
                print("⚠️  Warning: Database schema not found. Run schema.sql first!")
    
    def start_run(self, run_id: str):
        """Start a new monitoring run"""
        with self.connection.cursor() as cursor:
            sql = """
                INSERT INTO runs (run_id, started_at, status, queries_executed, errors_count)
                VALUES (%s, %s, %s, %s, %s)
            """
            cursor.execute(sql, (
                run_id,
                datetime.now(),
                'running',
                0,
                0
            ))
        self.connection.commit()
        print(f"✓ Started run: {run_id}")
    
    def complete_run(self, run_id: str, notes: Optional[str] = None):
        """Mark a run as completed"""
        with self.connection.cursor() as cursor:
            sql = """
                UPDATE runs 
                SET completed_at = %s, status = %s, notes = %s
                WHERE run_id = %s
            """
            cursor.execute(sql, (datetime.now(), 'completed', notes, run_id))
        self.connection.commit()
        print(f"✓ Completed run: {run_id}")
    
    def fail_run(self, run_id: str, error: str):
        """Mark a run as failed"""
        self._reconnect_if_needed()
        with self.connection.cursor() as cursor:
            sql = """
                UPDATE runs 
                SET completed_at = %s, status = %s, notes = %s
                WHERE run_id = %s
            """
            cursor.execute(sql, (datetime.now(), 'failed', error, run_id))
        self.connection.commit()
        print(f"✗ Failed run: {run_id}")
    
    def sync_queries(self, queries: List[Dict]):
        """Sync queries from config to database"""
        with self.connection.cursor() as cursor:
            for query in queries:
                sql = """
                    INSERT INTO queries (id, query_text, category, priority, active)
                    VALUES (%s, %s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE
                        query_text = VALUES(query_text),
                        category = VALUES(category),
                        priority = VALUES(priority),
                        active = VALUES(active)
                """
                cursor.execute(sql, (
                    query['id'],
                    query['text'],
                    query.get('category'),
                    query.get('priority', 1),
                    query.get('active', True)
                ))
        self.connection.commit()
        print(f"✓ Synced {len(queries)} queries to database")
    
    def store_response(
        self,
        run_id: str,
        query_id: str,
        query_text: str,
        model_id: str,
        response_text: str,
        paintballevents_ref: bool,
        search_query: Optional[str],
        cited_urls: List[str],
        response_time_ms: Optional[int] = None,
        error: Optional[str] = None
    ):
        """Store a query response in the database"""
        self._reconnect_if_needed()
        with self.connection.cursor() as cursor:
            # Convert cited_urls list to JSON
            cited_urls_json = json.dumps(cited_urls)
            
            sql = """
                INSERT INTO responses 
                (run_id, timestamp, query_id, model_id, query_text, response, 
                 paintballevents_referenced, search_query, cited_urls, 
                 response_time_ms, error)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(sql, (
                run_id,
                datetime.now(),
                query_id,
                model_id,
                query_text,
                response_text,
                paintballevents_ref,
                search_query,
                cited_urls_json,
                response_time_ms,
                error
            ))
            
            # Update run statistics
            cursor.execute("""
                UPDATE runs 
                SET queries_executed = queries_executed + 1
                WHERE run_id = %s
            """, (run_id,))
            
        self.connection.commit()
        
        # Print result
        citation_status = '✓ CITED' if paintballevents_ref else '✗ Not cited'
        print(f"  {citation_status} | {model_id} | {query_id[:20]}")
        if cited_urls:
            print(f"    URLs: {len(cited_urls)} found")
    
    def store_error(self, run_id: str, query_id: str, model_id: str, query_text: str, error: str):
        """Store an error that occurred during a query"""
        self._reconnect_if_needed()
        with self.connection.cursor() as cursor:
            sql = """
                INSERT INTO responses 
                (run_id, timestamp, query_id, model_id, query_text, response, 
                 paintballevents_referenced, error)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            cursor.execute(sql, (
                run_id,
                datetime.now(),
                query_id,
                model_id,
                query_text,
                "",
                False,
                error
            ))
            
            # Update error count
            cursor.execute("""
                UPDATE runs 
                SET errors_count = errors_count + 1
                WHERE run_id = %s
            """, (run_id,))
            
        self.connection.commit()
        print(f"  ✗ Error | {model_id} | {query_id}: {error}")
    
    def get_run_summary(self, run_id: str) -> Dict:
        """Get summary statistics for a run"""
        with self.connection.cursor() as cursor:
            cursor.execute("""
                SELECT 
                    run_id,
                    started_at,
                    completed_at,
                    status,
                    queries_executed,
                    errors_count,
                    notes
                FROM runs
                WHERE run_id = %s
            """, (run_id,))
            
            return cursor.fetchone()
    
    def close(self):
        """Close database connection"""
        if self.connection:
            self.connection.close()
    
    def __enter__(self):
        """Context manager entry"""
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        """Context manager exit"""
        self.close()

