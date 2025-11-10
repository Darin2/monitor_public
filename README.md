# AI Search Citation & Recommendation Monitor

## Project Goal

Monitor the visibility of **paintballevents.net** across major AI search platforms by tracking:
1. **Citations** - URLs that AI platforms search/reference in their web search process
2. **Recommendations** - Whether paintballevents.net is actually mentioned/recommended to users in the AI response text

This helps understand both when the site is being found by AI search AND when it's being presented to end users.

## Architecture

The monitoring system consists of three components:

1. **GitHub Actions Pipeline** (runs daily)
   - Executes Python script (`run_monitor.py`) on GitHub's servers
   - Queries AI models (OpenAI, Claude, Perplexity)
   - Writes results directly to MySQL database on Bluehost

2. **MySQL Database** (on Bluehost server)
   - Stores all query responses, citations, and metadata
   - Receives data from GitHub Actions pipeline daily

3. **PHP Dashboard** (`monitor.php` on Bluehost server)
   - Simple PHP script that reads from MySQL database
   - Displays charts, statistics, and recent citations
   - No backend processing - just reads and displays data

**Data Flow:** GitHub Actions → MySQL Database → PHP Dashboard

- ✅ No Python hosting needed - runs for free on GitHub
- ✅ Works perfectly with Bluehost shared hosting (PHP + MySQL)
- ✅ Scalable to multiple AI models
- ✅ Clean separation of concerns

## Target Platforms

Tracking citations and recommendations across all major AI platforms:

1. **ChatGPT** (OpenAI) - ✅ Implemented (GPT-5, GPT-5-mini, GPT-5-nano)
2. **Claude** (Anthropic) - ✅ Implemented (Sonnet 4.5)
3. **Perplexity** - ✅ Implemented (Sonar Pro)
4. **DeepSeek** - 🚧 Ready (awaiting API key)
5. **Grok** (xAI) - 🚧 Ready (awaiting API key)
6. **Llama** (Meta) - 🚧 Ready (awaiting API key)

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

### ✅ Implemented (v2.0)
- **OpenAI integration** - GPT-5, GPT-5-mini, GPT-5-nano with web search
- **Claude integration** - Sonnet 4.5 with web search
- **Perplexity integration** - Sonar Pro model with real-time search
- **MySQL database** - Hosted on Bluehost with proper schema
- **GitHub Actions automation** - Runs weekly, no server needed
- **Citation extraction** - Captures actual URLs searched
- **PHP Dashboard** - Beautiful terminal-style UI with charts
- **Modular architecture** - Easy to add new models
- **Query configuration** - JSON-based query management
- **Run tracking** - Groups queries and tracks execution

### 📋 Planned
- **Additional models** - DeepSeek, Grok, Llama (stubs ready)
- **Email alerts** - Notify when citation rate changes
- **Competitor tracking** - Track which competitors are cited
- **Query A/B testing** - Optimize query phrasing
- **REST API** - Programmatic access to data

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

## Quick Start

### Setup
See [aieo-monitor/SETUP.md](aieo-monitor/SETUP.md) for detailed setup instructions.

**TL;DR:**
1. Create MySQL database on Bluehost
2. Run `aieo-monitor/database/schema.sql`
3. Add secrets to GitHub repository
4. Upload `aieo-monitor/monitor.php` to Bluehost (one-time setup)
5. Done! GitHub Actions runs daily and populates the database, PHP dashboard reads and displays the data.

### Local Development

```bash
cd aieo-monitor

# Install dependencies
pip install -r requirements.txt

# Setup environment
cp .env.example .env
# Edit .env with your API keys and database credentials

# Test database connection
python test_connection.py

# Run monitor locally
python run_monitor.py
```

## Project Structure

```
monitor/
├── aieo-monitor/              # Main monitoring system
│   ├── config/                # Query configurations
│   ├── models/                # AI model implementations
│   ├── database/              # Database schema and operations
│   ├── run_monitor.py         # Main orchestrator (runs on GitHub Actions)
│   ├── monitor.php            # PHP dashboard (runs on Bluehost)
│   ├── SETUP.md              # Detailed setup guide
│   └── README.md             # Module documentation
├── aieo-optimization/         # SEO optimization tools
└── README.md                 # This file
```

## Environment Setup

API keys required (add to GitHub Secrets):
- `OPENAI_API_KEY` - For OpenAI GPT-5 models
- `ANTHROPIC_API_KEY` - For Claude models
- `PERPLEXITY_API_KEY` - For Perplexity Sonar Pro
- `DEEPSEEK_API_KEY` - For DeepSeek (optional)
- `GROK_API_KEY` - For Grok (optional)
- `LLAMA_API_KEY` - For Llama (optional)

MySQL credentials (add to GitHub Secrets):
- `MYSQL_HOST` - Database host
- `MYSQL_DATABASE` - Database name
- `MYSQL_USER` - Database user
- `MYSQL_PASSWORD` - Database password

## Dashboard

View live results at: **https://darin.tech/monitor.php**

The dashboard is a simple PHP script (`monitor.php`) that runs on the Bluehost server. It reads directly from the MySQL database and displays:

- 📈 Citation rate trends over time
- 📊 Model performance comparison
- 📋 Detailed statistics per model
- 🔍 Recent citation events with URLs
- 🎯 Run status and error tracking

No backend processing required - it's just a read-only view of the database data.

## Results

Check the dashboard for the latest results. Tracking:
- Overall citation rate across all models
- Per-model citation rates
- Best performing queries
- Competitor URLs being cited
- Trends over time

**Current baseline (before optimization):**
- Citation rate: ~53% across all 9 queries

---

## Legacy Files

The following files are from v1.0 (SQLite-based system) and are kept for reference:
- `test_openai.py` - Original OpenAI test script
- `test_claude.py` - Original Claude test script
- `view_database.py` - SQLite database viewer

To migrate old data: `python migrate_sqlite_to_mysql.py`

