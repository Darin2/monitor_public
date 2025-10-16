# AI Search Citation & Recommendation Monitor

## Project Goal

Monitor the visibility of **paintballevents.net** across major AI search platforms by tracking:
1. **Citations** - URLs that AI platforms search/reference in their web search process
2. **Recommendations** - Whether paintballevents.net is actually mentioned/recommended to users in the AI response text

This helps understand both when the site is being found by AI search AND when it's being presented to end users.

## Target Platforms

The goal is to track citations and recommendations across all major AI platforms with search capabilities:

1. **ChatGPT** (OpenAI) - Web search integration
2. **Claude** (Anthropic) - Web search capabilities
3. **Perplexity** - AI search engine with citations
4. **Grok** (X.AI) - Real-time search integration

## What We're Tracking

For each query across each platform:
- **Query text** - The exact question asked
- **Timestamp** - When the query was made
- **Search query used** - The actual search query the AI used
- **Cited URLs** - All websites referenced/searched during the AI's web search
- **PaintballEvents.net cited** - Boolean: Was the site found in the search citations?
- **PaintballEvents.net recommended** - Boolean: Was the site mentioned/recommended in the response to the user?
- **Response text** - Full AI response for analysis

## Current Status

### ✅ Implemented
- **OpenAI API integration** - Working with web search
- **SQLite database** - Stores all query responses and citations
- **Citation extraction** - Captures actual URLs searched (not just mentioned)
- **Test query variations** - Multiple query styles to test
- **Analysis tooling** - View database and query patterns

### 📋 Planned
- **Perplexity API** - Add Perplexity search monitoring
- **Claude API** - Add Anthropic Claude search monitoring
- **Grok API** - Add X.AI Grok monitoring (if API available)
- **Multi-platform comparison** - Compare citation rates across platforms
- **Manual entry tool** - For platforms without API access (ChatGPT web)
- **Automated scheduling** - Run queries periodically
- **Reporting dashboard** - Visualize trends over time

## Why This Matters

AI search is rapidly becoming how users discover events and information. Understanding:
- **Which platforms cite paintballevents.net** (finding it via search)
- **Which platforms recommend paintballevents.net** (presenting it to users)
- **What query types work best** for both citation and recommendation
- **How patterns change over time** across different AI platforms

...is critical for SEO and discoverability strategy in the AI era. There's a key difference between being **found** by AI search (citation) vs being **recommended** to users (appearing in the response).

## Current Test Queries

1. "Find paintball events in Texas in 2025"
2. "Find paintball events in Texas in 2025 and list the websites you referenced"
3. "What paintball tournaments are happening in Texas in 2025?"
4. "Find upcoming paintball scenario games and tournaments in Texas for 2025"
5. "Where can I find paintball events in Texas?"

## Usage

### Run a single test query:
```bash
python3 test_openai.py
```

### Run all test queries:
```bash
python3 run_all_queries.py
```

### View results:
```bash
python3 view_database.py
```

## Database Schema

```sql
CREATE TABLE responses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    timestamp TEXT NOT NULL,
    query TEXT NOT NULL,
    response TEXT NOT NULL,
    paintballevents_referenced BOOLEAN NOT NULL,
    search_query TEXT,
    cited_urls TEXT  -- JSON array of URLs
)
```

## Environment Setup

See `AGENTS.md` for environment configuration details.

API keys required (store in `.env`):
- `OPENAI_API_KEY` - For OpenAI API access
- (Future) `ANTHROPIC_API_KEY` - For Claude API
- (Future) `PERPLEXITY_API_KEY` - For Perplexity API
- (Future) `XAI_API_KEY` - For Grok API

## Results So Far

**OpenAI API Testing (9 queries):**
- PaintballEvents.net **cited** in search: **0.0%** (0 out of 9 queries)
- PaintballEvents.net **recommended** to users: **0.0%** (0 out of 9 queries)
- Most frequently cited: nxlpaintball.com, paintballcombine.com, mlpb.pbleagues.com, reddit.com

**Note:** OpenAI API results may differ from ChatGPT web/mobile experience, which is why multi-platform monitoring is the goal.

