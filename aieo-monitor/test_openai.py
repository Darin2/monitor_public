import os
import sqlite3
import warnings
from datetime import datetime
from openai import OpenAI
from dotenv import load_dotenv

# Suppress Pydantic serialization warnings
warnings.filterwarnings('ignore', category=UserWarning, module='pydantic')

# Load environment variables from .env file
load_dotenv()

# Initialize the OpenAI client with API key from environment variable
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

# Initialize SQLite database
def init_database():
    conn = sqlite3.connect('query_responses.db')
    cursor = conn.cursor()
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS responses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp TEXT NOT NULL,
            query TEXT NOT NULL,
            response TEXT NOT NULL,
            paintballevents_referenced BOOLEAN NOT NULL,
            search_query TEXT,
            cited_urls TEXT,
            model TEXT
        )
    ''')
    
    # Check if model column exists, add it if it doesn't (for existing databases)
    cursor.execute("PRAGMA table_info(responses)")
    columns = [column[1] for column in cursor.fetchall()]
    if 'model' not in columns:
        cursor.execute('ALTER TABLE responses ADD COLUMN model TEXT')
        print("✓ Added 'model' column to existing database")
    
    conn.commit()
    return conn

# Extract search query and cited URLs from response metadata
def extract_metadata(response):
    search_query = None
    cited_urls = []
    
    response_dict = response.model_dump()
    output = response_dict.get('output', [])
    
    # Extract search query from web_search_call
    for item in output:
        if item.get('type') == 'web_search_call':
            search_query = item.get('action', {}).get('query')
        
        # Extract URLs from message annotations
        if item.get('type') == 'message':
            content = item.get('content', [])
            for content_item in content:
                annotations = content_item.get('annotations', [])
                for annotation in annotations:
                    if annotation.get('type') == 'url_citation':
                        url = annotation.get('url', '')
                        # Remove utm_source parameter for cleaner URLs
                        clean_url = url.split('?utm_source')[0]
                        if clean_url not in cited_urls:
                            cited_urls.append(clean_url)
    
    return search_query, cited_urls

# Check if paintballevents.net is in the cited URLs
def check_paintballevents_reference(cited_urls):
    for url in cited_urls:
        if 'paintballevents.net' in url.lower():
            return True
    return False

# Store response in database
def store_response(conn, query, response_text, paintballevents_ref, search_query, cited_urls, model):
    cursor = conn.cursor()
    timestamp = datetime.now().isoformat()
    
    # Convert cited_urls list to JSON string for storage
    import json
    cited_urls_json = json.dumps(cited_urls)
    
    cursor.execute('''
        INSERT INTO responses (timestamp, query, response, paintballevents_referenced, search_query, cited_urls, model)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ''', (timestamp, query, response_text, paintballevents_ref, search_query, cited_urls_json, model))
    conn.commit()
    
    print(f"\n✓ Response stored in database (ID: {cursor.lastrowid})")
    print(f"  Timestamp: {timestamp}")
    print(f"  Model: {model}")
    print(f"  Search query used: {search_query}")
    print(f"  URLs cited: {len(cited_urls)}")
    for url in cited_urls:
        print(f"    - {url}")
    print(f"  PaintballEvents.net cited: {'✓ YES' if paintballevents_ref else '✗ NO'}")

# Initialize database
conn = init_database()

# Test queries - all queries from the database
TEST_QUERIES = [
    "Find paintball events in Texas in 2025 and list the websites you searched",
    "Find paintball events in Texas in 2025",
    "Find paintball events in Texas in 2025 and list the websites you referenced",
    "What paintball tournaments are happening in Texas in 2025?",
    "Find upcoming paintball scenario games and tournaments in Texas for 2025",
    "Where can I find paintball events in Texas?"
]

# Define model to use
model = "gpt-4o"

print(f"Running {len(TEST_QUERIES)} queries with model: {model}\n")
print("=" * 80)

# Run all queries
for idx, query in enumerate(TEST_QUERIES, 1):
    print(f"\n[{idx}/{len(TEST_QUERIES)}] Testing Query: {query}\n")
    
    try:
        # Make API call with web search enabled
        response = client.responses.create(
            model=model,
            tools=[
                {"type": "web_search"}
            ],
            input=query
        )
        
        # Print the response
        response_text = response.output_text
        print("Response:")
        print(response_text)
        
        # Extract metadata (search query and cited URLs)
        search_query, cited_urls = extract_metadata(response)
        
        # Check if paintballevents.net was referenced in the cited URLs
        paintballevents_ref = check_paintballevents_reference(cited_urls)
        
        # Store in database
        store_response(conn, query, response_text, paintballevents_ref, search_query, cited_urls, model)
        
        print("\n" + "=" * 80)
        
    except Exception as e:
        print(f"✗ Error processing query: {e}")
        print("=" * 80)
        continue

print(f"\n✓ Completed all {len(TEST_QUERIES)} queries")

# Close database connection
conn.close()
