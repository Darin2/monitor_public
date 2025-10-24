import os
import sqlite3
import warnings
from datetime import datetime
from anthropic import Anthropic
from dotenv import load_dotenv

# Suppress warnings
warnings.filterwarnings('ignore', category=UserWarning, module='pydantic')

# Load environment variables from .env file
load_dotenv()

# Initialize the Anthropic client with API key from environment variable
client = Anthropic(api_key=os.getenv("ANTHROPIC_API_KEY"))

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
    import re
    search_query = None
    cited_urls = []
    
    # Claude's response structure is different - we need to parse the content blocks
    for block in response.content:
        # Check for tool use blocks
        if hasattr(block, 'type') and block.type == 'tool_use':
            if block.name == 'web_search':
                # Extract search query from tool input
                if hasattr(block, 'input') and 'query' in block.input:
                    search_query = block.input['query']
        
        # Check for text blocks that might contain citations or URLs
        if hasattr(block, 'type') and block.type == 'text':
            # Extract URLs from the text content using regex
            text = block.text
            # Find all URLs in the text (http or https)
            urls_with_protocol = re.findall(r'https?://[^\s\)>\]]+', text)
            # Also find domain names without protocol (e.g., "example.com")
            urls_without_protocol = re.findall(r'(?<![/@\w])([a-zA-Z0-9-]+\.(?:com|net|org|edu|gov|io|co)[^\s\)>\]]*)', text)
            
            # Combine both lists
            all_urls = urls_with_protocol + list(urls_without_protocol)
            
            for url in all_urls:
                # Clean up URL (remove trailing punctuation)
                clean_url = url.rstrip('.,;:!?')
                # Remove utm_source parameter for cleaner URLs
                clean_url = clean_url.split('?utm_source')[0]
                if clean_url not in cited_urls:
                    cited_urls.append(clean_url)
    
    return search_query, cited_urls

# Check if paintballevents.net is in the cited URLs or response text
def check_paintballevents_reference(cited_urls, response_text):
    # Check in cited URLs (looking specifically for paintballevents.net)
    for url in cited_urls:
        if 'paintballevents.net' in url.lower():
            return True
    # Also check in the response text directly (for paintballevents.net)
    if 'paintballevents.net' in response_text.lower():
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
model = "claude-3-7-sonnet-20250219"

print(f"Running {len(TEST_QUERIES)} queries with model: {model}\n")
print("=" * 80)

# Run all queries
for idx, query in enumerate(TEST_QUERIES, 1):
    print(f"\n[{idx}/{len(TEST_QUERIES)}] Testing Query: {query}\n")
    
    try:
        # Make API call with web search enabled
        # Note: Claude's API structure is different from OpenAI's
        response = client.messages.create(
            model=model,
            max_tokens=4096,
            tools=[
                {
                    "type": "web_search_20250305",
                    "name": "web_search"
                }
            ],
            messages=[
                {"role": "user", "content": query}
            ]
        )
        
        # Print the response
        response_text = ""
        for block in response.content:
            if hasattr(block, 'type') and block.type == 'text':
                response_text += block.text
        
        print("Response:")
        print(response_text)
        
        # Extract metadata (search query and cited URLs)
        search_query, cited_urls = extract_metadata(response)
        
        # Check if paintballevents.net was referenced in the cited URLs or response text
        paintballevents_ref = check_paintballevents_reference(cited_urls, response_text)
        
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

