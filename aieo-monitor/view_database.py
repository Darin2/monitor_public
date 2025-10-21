import sqlite3
import json
from datetime import datetime

# Connect to database
conn = sqlite3.connect('query_responses.db')
cursor = conn.cursor()

# Get all responses
cursor.execute('SELECT * FROM responses ORDER BY timestamp DESC')
rows = cursor.fetchall()

print(f"\n{'='*80}")
print(f"STORED RESPONSES: {len(rows)} total")
print(f"{'='*80}\n")

for row in rows:
    # Handle both old and new schema
    if len(row) == 5:
        id, timestamp, query, response, paintballevents_ref = row
        search_query = None
        cited_urls = []
    else:
        id, timestamp, query, response, paintballevents_ref, search_query, cited_urls_json = row
        cited_urls = json.loads(cited_urls_json) if cited_urls_json else []
    
    # Parse and format timestamp
    dt = datetime.fromisoformat(timestamp)
    formatted_time = dt.strftime("%Y-%m-%d %H:%M:%S")
    
    print(f"ID: {id}")
    print(f"Timestamp: {formatted_time}")
    print(f"Query: {query}")
    if search_query:
        print(f"Search Query Used: {search_query}")
    print(f"PaintballEvents.net Cited: {'✓ YES' if paintballevents_ref else '✗ NO'}")
    if cited_urls:
        print(f"URLs Cited ({len(cited_urls)}):")
        for url in cited_urls:
            print(f"  - {url}")
    print(f"Response Preview: {response[:200]}...")
    print(f"{'-'*80}\n")

# Get summary statistics
cursor.execute('SELECT COUNT(*) FROM responses WHERE paintballevents_referenced = 1')
count_with_reference = cursor.fetchone()[0]

cursor.execute('SELECT COUNT(*) FROM responses')
total_count = cursor.fetchone()[0]

print(f"\n{'='*80}")
print(f"SUMMARY:")
print(f"  Total queries: {total_count}")
print(f"  PaintballEvents.net referenced: {count_with_reference}")
print(f"  Reference rate: {(count_with_reference/total_count*100) if total_count > 0 else 0:.1f}%")
print(f"{'='*80}\n")

# Query pattern analysis
print(f"\n{'='*80}")
print(f"QUERY PATTERN ANALYSIS:")
print(f"{'='*80}\n")

cursor.execute('''
    SELECT 
        query,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_referenced,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as reference_rate
    FROM responses
    GROUP BY query
    ORDER BY times_tested DESC
''')

patterns = cursor.fetchall()
for query, times_tested, times_referenced, reference_rate in patterns:
    print(f"Query: {query[:60]}{'...' if len(query) > 60 else ''}")
    print(f"  Tested: {times_tested} times")
    print(f"  Referenced: {times_referenced} times ({reference_rate}%)")
    print()

conn.close()

