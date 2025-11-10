# AI Citation Monitor

> Track whether AI models cite paintballevents.net when answering paintball-related queries

## 🎯 Purpose

This monitoring system tracks citation rates across multiple AI models (GPT-5, GPT-5-mini, GPT-5-nano, Claude Sonnet 4.5, Perplexity Sonar Pro) to understand:
- Which AI platforms find and cite paintballevents.net
- How citation rates change over time
- Which query phrasings work best
- Competitor visibility in AI search results

## 🏗️ Architecture

The monitoring system consists of three components:

1. **GitHub Actions Pipeline** (runs daily at 9 AM UTC)
   - Executes Python script (`run_monitor.py`) on GitHub's servers
   - Queries AI models (OpenAI GPT-5, Claude Sonnet 4.5, Perplexity Sonar Pro)
   - Writes results directly to MySQL database on Bluehost

2. **MySQL Database** (on Bluehost server)
   - Stores all query responses, citations, and metadata
   - Receives data from GitHub Actions pipeline daily

3. **PHP Dashboard** (`monitor.php` on Bluehost server)
   - Simple PHP script that reads from MySQL database
   - Displays charts, statistics, and recent citations
   - No backend processing - just reads and displays data

**Data Flow:** GitHub Actions → MySQL Database → PHP Dashboard

- ✅ No Python hosting needed on Bluehost
- ✅ Free GitHub Actions (2,000 minutes/month)
- ✅ Scalable to multiple models
- ✅ Clean separation of concerns

## 🚀 Quick Start

See [SETUP.md](SETUP.md) for detailed setup instructions.

**TL;DR:**
1. Create MySQL database on Bluehost
2. Run `database/schema.sql` 
3. Add secrets to GitHub repository
4. Upload `monitor.php` to Bluehost (one-time setup)
5. Done! GitHub Actions runs daily and populates the database, PHP dashboard reads and displays the data.

## 📁 Project Structure

```
aieo-monitor/
├── config/               # Configuration files
│   └── queries.json      # Test queries
├── models/               # AI model implementations
│   ├── base_model.py     # Abstract base class
│   ├── gpt5_model.py     # OpenAI GPT-5 ✓
│   ├── gpt5_mini_model.py # OpenAI GPT-5-mini ✓
│   ├── gpt5_nano_model.py # OpenAI GPT-5-nano ✓
│   ├── claude_model.py   # Anthropic Claude 3.7 Sonnet (paused)
│   ├── claude_sonnet_45_model.py # Anthropic Claude Sonnet 4.5 ✓
│   ├── claude_haiku_45_model.py  # Anthropic Claude Haiku 4.5 (paused)
│   ├── claude_opus_41_model.py   # Anthropic Claude Opus 4.1 (paused)
│   ├── deepseek_model.py # DeepSeek (stub)
│   ├── grok_model.py     # Grok (stub)
│   ├── perplexity_model.py # Perplexity Sonar Pro ✓
│   └── llama_model.py    # Llama (stub)
├── database/             # Database layer
│   ├── schema.sql        # MySQL schema
│   └── operations.py     # CRUD operations
├── run_monitor.py        # Main orchestrator (runs on GitHub Actions)
├── requirements.txt      # Python dependencies
├── monitor.php           # PHP dashboard (runs on Bluehost, reads from MySQL)
└── SETUP.md             # Setup instructions
```

## 🤖 Supported Models

| Model | Status | Provider |
|-------|--------|----------|
| GPT-5 | ✅ Active | OpenAI |
| GPT-5-mini | ✅ Active | OpenAI |
| GPT-5-nano | ✅ Active | OpenAI |
| Claude Sonnet 4.5 | ✅ Active | Anthropic |
| Sonar Pro | ✅ Active | Perplexity |
| Claude 3.7 Sonnet | 🚧 Paused | Anthropic |
| Claude Haiku 4.5 | 🚧 Paused | Anthropic |
| Claude Opus 4.1 | 🚧 Paused | Anthropic |
| DeepSeek Chat | 🚧 Ready (stub) | DeepSeek |
| Grok 2 | 🚧 Ready (stub) | xAI |
| Llama 3 70B | 🚧 Ready (stub) | Meta |

## 📊 What We Track

For each query × model combination:
- ✅ Full response text
- ✅ Search query used by model
- ✅ Cited URLs
- ✅ Whether paintballevents.net was cited
- ✅ Response time
- ✅ Timestamp and run metadata

## 🔄 How It Works

1. **GitHub Actions** runs `run_monitor.py` daily at 9 AM UTC
2. **Orchestrator** loads queries from `config/queries.json`
3. **Each model** executes all queries
4. **Results** are stored directly in MySQL on Bluehost
5. **PHP Dashboard** (`monitor.php`) reads from MySQL and displays trends and performance

## 🎨 Dashboard Features

- 📈 Citation rate over time (line chart)
- 📊 Model comparison (bar chart)
- 📋 Detailed performance table
- 🔍 Recent citation events
- 🎯 Run status tracking

## ⚙️ Configuration

### Add/Edit Queries

Edit `config/queries.json`:

```json
{
  "queries": [
    {
      "id": "q1",
      "text": "Find paintball events in Texas",
      "category": "general",
      "priority": 1,
      "active": true
    }
  ]
}
```

### Change Schedule

Edit `.github/workflows/monitor.yml`:

```yaml
on:
  schedule:
    - cron: '0 9 * * 1'  # Every Monday at 9 AM UTC
```

### Add New Model

1. Get API key
2. Add to GitHub Secrets
3. Implement model class (inherit from `BaseModel`)
4. Uncomment in `run_monitor.py`
5. Update database: `UPDATE models SET active = 1 WHERE id = 'model-id';`

## 🔐 Security

- ✅ API keys stored in GitHub Secrets
- ✅ MySQL credentials in environment variables
- ✅ No sensitive data in code
- ✅ Remote MySQL access controlled

## 📈 Results & Dashboard

The dashboard is a simple PHP script (`monitor.php`) hosted on Bluehost that reads from the MySQL database and displays results.

View live dashboard at: **https://darin.tech/monitor.php**

Current baseline (before optimization):
- Citation rate: ~53% across all models queried
- Model with highest citation rate: Sonar Pro (89.4% as of November 10, 2025)
- Best performing query as of November 10, 2025: "Find paintball scenario games in Texas for 2025 (cited in 71 of 85 queries)"

## 🛠️ Development

### Local Testing

```bash
# Install dependencies
pip install -r requirements.txt

# Create .env file
cp .env.example .env
# Edit .env with your credentials

# Run monitor
python run_monitor.py
```

### Adding a New Model

1. Create `models/newmodel_model.py`
2. Inherit from `BaseModel`
3. Implement `query()` and `extract_metadata()`
4. Add to orchestrator initialization
5. Test locally before deploying

## 📚 Documentation

- [SETUP.md](SETUP.md) - Detailed setup instructions
- [PROJECT_CONTEXT.md](PROJECT_CONTEXT.md) - Project background and goals
- `database/schema.sql` - Database structure documentation

## 🎯 Roadmap

- [x] Core infrastructure
- [x] OpenAI integration (GPT-5, GPT-5-mini, GPT-5-nano)
- [x] Claude integration (Sonnet 4.5)
- [x] Perplexity integration (Sonar Pro)
- [x] GitHub Actions automation
- [x] MySQL database
- [x] PHP dashboard
- [ ] Additional Claude models (3.7 Sonnet, Haiku 4.5, Opus 4.1 - code ready, paused)
- [ ] DeepSeek integration
- [ ] Grok integration
- [ ] Llama integration
- [ ] Email alerts
- [ ] Competitor tracking
- [ ] Query A/B testing
- [ ] REST API

## 📄 License

Private project for paintballevents.net monitoring.

---

**Questions?** See [SETUP.md](SETUP.md) for troubleshooting.

