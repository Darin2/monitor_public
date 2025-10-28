# LLM Citation Monitor - Project Context

## Project Goal

Track whether LLMs cite or recommend **paintballevents.net** when responding to paintball-related queries.

## What We're Tracking

For each LLM query, log:
- **Query text** - The exact question/prompt used
- **Model** - Which LLM model was used (e.g., "gpt-5", "gpt-5-mini", "gpt-5-nano", "claude-3.7-sonnet", "perplexity-sonar-pro")
- **Timestamp** - When the query was made
- **Cited URLs** - URLs the LLM actually searched/cited (extracted from API metadata when available)
- **paintballevents.net cited** - Boolean: Was paintballevents.net in the cited URLs?
- **paintballevents.net recommended** - Boolean: Was paintballevents.net mentioned in the response text?
- **Response text** - Full LLM response for analysis

## Key Distinction: Citations vs Recommendations

There are two ways paintballevents.net can appear:

1. **Citations** - URLs that the LLM's search tool actually found and used
   - For OpenAI API: Extracted from response metadata (annotations with url_citation type)
   - For other APIs: May need different extraction methods
   - More reliable signal of SEO visibility

2. **Recommendations** - Site mentioned in the actual response text shown to users
   - Checked by searching response text for "paintballevents.net"
   - What users actually see

Both matter, but citations show whether AI search is **finding** the site, while recommendations show whether it's being **presented** to users.

## Current Implementation

### Working
- **OpenAI API integration** with web search tool
- **Citation extraction** from OpenAI response metadata
- **SQLite database** to store all query results
- **Multiple test queries** to see which phrasing works best
- **Analysis tools** to view results and patterns

### Database Schema

```sql
CREATE TABLE responses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    timestamp TEXT NOT NULL,
    query TEXT NOT NULL,
    model TEXT NOT NULL,           -- e.g., "gpt-5", "gpt-5-mini", "gpt-5-nano", "claude-3.7-sonnet"
    response TEXT NOT NULL,
    paintballevents_referenced BOOLEAN NOT NULL,
    search_query TEXT,             -- The search query the LLM used
    cited_urls TEXT                -- JSON array of URLs cited
)
```

**Note:** Current schema needs to add `model` column.

## Starting Simple, Then Expanding

### Phase 1 (Current)
- Multiple queries
- Multiple OpenAI models (GPT-5, GPT-5-mini, GPT-5-nano)
- Claude 3.7 Sonnet
- Run weekly via cron job
- Store in MySQL

### Phase 2 (Planned)
- Add more models:
  - Perplexity Sonar Pro
  - DeepSeek
  - Gemini
- Add more queries (different phrasings, locations, event types)
- Dashboard UI to visualize trends over time

### Phase 3 (Future)
- Compare citation rates across models
- Track how rankings change over time
- A/B test query phrasings
- Alert when citation rate drops

## Weekly Cron Job

Goal: Run the monitoring script once per week to track trends over time.

```bash
# Run every Sunday at 2am
0 2 * * 0 cd /Users/darinhardin/Documents/GitHub/monitor/aieo-monitor && /usr/bin/python3 run_monitor.py
```

## Test Queries

Current test queries (will expand):
1. "Find paintball events in Texas in 2025"
2. "Find paintball events in Texas in 2025 and list the websites you referenced"
3. "What paintball tournaments are happening in Texas in 2025?"
4. "Find upcoming paintball scenario games and tournaments in Texas for 2025"
5. "Where can I find paintball events in Texas?"

## Dashboard UI (Planned)

Features:
- Timeline chart showing citation rate over time
- Model comparison (which models cite paintballevents.net most?)
- Query comparison (which phrasings work best?)
- List of all cited URLs (competitor analysis)
- Drill down into individual responses

## Files

- `run_monitor.py` - Main script to run queries and store results (to be created)
- `query_responses.db` - SQLite database with all results
- `view_database.py` - CLI tool to view stored results
- `dashboard/` - Web UI for visualizing data (to be created)
- `.env` - API keys (not in git)

## Environment Setup

Required API keys in `.env`:
- `OPENAI_API_KEY` - For OpenAI API access
- (Future) `ANTHROPIC_API_KEY` - For Claude
- (Future) `PERPLEXITY_API_KEY` - For Perplexity
- (Future) Additional API keys as needed

## Why This Matters

AI search is becoming the primary way users discover information. Traditional SEO metrics (Google rankings) don't capture LLM citation rates. This monitoring helps:
- Understand which AI platforms find paintballevents.net
- Track visibility trends over time
- Identify which query types work best
- Compare against competitors who ARE being cited
- Inform SEO and content strategy for the AI era

## Current Results (OpenAI API Testing)

From existing database:
- 9 queries tested
- paintballevents.net cited: 0% (0/9)
- paintballevents.net recommended: 0% (0/9)
- Competitors being cited: nxlpaintball.com, paintballcombine.com, mlpb.pbleagues.com

**This is why we need to monitor over time and test different queries/models.**

