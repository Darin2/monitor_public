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
    # Handle different schema versions
    if len(row) == 5:
        # Original schema (no search_query, cited_urls, or model)
        id, timestamp, query, response, paintballevents_ref = row
        search_query = None
        cited_urls = []
        model = None
    elif len(row) == 7:
        # Schema with search_query and cited_urls (no model)
        id, timestamp, query, response, paintballevents_ref, search_query, cited_urls_json = row
        cited_urls = json.loads(cited_urls_json) if cited_urls_json else []
        model = None
    else:
        # Current schema with model column
        id, timestamp, query, response, paintballevents_ref, search_query, cited_urls_json, model = row
        cited_urls = json.loads(cited_urls_json) if cited_urls_json else []
    
    # Parse and format timestamp
    dt = datetime.fromisoformat(timestamp)
    formatted_time = dt.strftime("%Y-%m-%d %H:%M:%S")
    
    print(f"ID: {id}")
    print(f"Timestamp: {formatted_time}")
    print(f"Query: {query}")
    if model:
        print(f"Model: {model}")
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
print(f"  PaintballEvents.net cited: {count_with_reference}")
print(f"  Citation rate: {(count_with_reference/total_count*100) if total_count > 0 else 0:.1f}%")
print(f"{'='*80}\n")

# Model performance analysis
print(f"\n{'='*80}")
print(f"MODEL PERFORMANCE ANALYSIS:")
print(f"{'='*80}\n")

cursor.execute('''
    SELECT 
        model,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY model
    ORDER BY times_tested DESC
''')

models = cursor.fetchall()
if models:
    for model, times_tested, times_cited, citation_rate in models:
        print(f"Model: {model}")
        print(f"  Tested: {times_tested} times")
        print(f"  Cited: {times_cited} times ({citation_rate}%)")
        print()
else:
    print("No model data available.\n")

# Query pattern analysis
print(f"{'='*80}")
print(f"QUERY PATTERN ANALYSIS:")
print(f"{'='*80}\n")

cursor.execute('''
    SELECT 
        query,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    GROUP BY query
    ORDER BY times_tested DESC
''')

patterns = cursor.fetchall()
for query, times_tested, times_cited, citation_rate in patterns:
    print(f"Query: {query[:60]}{'...' if len(query) > 60 else ''}")
    print(f"  Tested: {times_tested} times")
    print(f"  Cited: {times_cited} times ({citation_rate}%)")
    print()

# Query + Model combination analysis
print(f"{'='*80}")
print(f"QUERY + MODEL COMBINATION ANALYSIS:")
print(f"{'='*80}\n")

cursor.execute('''
    SELECT 
        query,
        model,
        COUNT(*) as times_tested,
        SUM(paintballevents_referenced) as times_cited,
        ROUND(CAST(SUM(paintballevents_referenced) AS FLOAT) / COUNT(*) * 100, 1) as citation_rate
    FROM responses
    WHERE model IS NOT NULL
    GROUP BY query, model
    ORDER BY query, model
''')

combinations = cursor.fetchall()
if combinations:
    current_query = None
    for query, model, times_tested, times_cited, citation_rate in combinations:
        if query != current_query:
            if current_query is not None:
                print()
            print(f"Query: {query[:60]}{'...' if len(query) > 60 else ''}")
            current_query = query
        print(f"  {model}: {times_tested} tests, {times_cited} citations ({citation_rate}%)")
    print()
else:
    print("No combination data available.\n")

conn.close()

