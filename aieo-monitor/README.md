# AI Citation Monitor

> Track whether AI models cite paintballevents.net when answering paintball-related queries

## 🎯 Purpose

This monitoring system tracks citation rates across multiple AI models (OpenAI, Claude, DeepSeek, Grok, Perplexity, Llama) to understand:
- Which AI platforms find and cite paintballevents.net
- How citation rates change over time
- Which query phrasings work best
- Competitor visibility in AI search results

## 🏗️ Architecture

**GitHub Actions** (Weekly Cron) → **MySQL on Bluehost** → **PHP Dashboard**

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
4. Upload `monitor.php` to Bluehost
5. Done! Runs automatically every Monday.

## 📁 Project Structure

```
aieo-monitor/
├── config/               # Configuration files
│   └── queries.json      # Test queries
├── models/               # AI model implementations
│   ├── base_model.py     # Abstract base class
│   ├── openai_model.py   # OpenAI GPT-4o ✓
│   ├── claude_model.py   # Anthropic Claude ✓
│   ├── deepseek_model.py # DeepSeek (stub)
│   ├── grok_model.py     # Grok (stub)
│   ├── perplexity_model.py # Perplexity (stub)
│   └── llama_model.py    # Llama (stub)
├── database/             # Database layer
│   ├── schema.sql        # MySQL schema
│   └── operations.py     # CRUD operations
├── run_monitor.py        # Main orchestrator
├── requirements.txt      # Python dependencies
├── monitor.php           # Web dashboard
└── SETUP.md             # Setup instructions
```

## 🤖 Supported Models

| Model | Status | Provider |
|-------|--------|----------|
| GPT-4o | ✅ Active | OpenAI |
| Claude 3.7 Sonnet | ✅ Active | Anthropic |
| DeepSeek Chat | 🚧 Ready (stub) | DeepSeek |
| Grok 2 | 🚧 Ready (stub) | xAI |
| Sonar Pro | 🚧 Ready (stub) | Perplexity |
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

1. **GitHub Actions** runs `run_monitor.py` every Monday
2. **Orchestrator** loads queries from `config/queries.json`
3. **Each model** executes all queries
4. **Results** are stored in MySQL on Bluehost
5. **Dashboard** displays trends and performance

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

## 📈 Results So Far

Check the live dashboard at: **https://darin.tech/monitor.php**

Current baseline (before optimization):
- Citation rate: TBD
- Best performing model: TBD
- Best performing query: TBD

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
- [x] OpenAI integration
- [x] Claude integration
- [x] GitHub Actions automation
- [x] MySQL database
- [x] PHP dashboard
- [ ] DeepSeek integration
- [ ] Grok integration
- [ ] Perplexity integration
- [ ] Llama integration
- [ ] Email alerts
- [ ] Competitor tracking
- [ ] Query A/B testing
- [ ] REST API

## 📄 License

Private project for paintballevents.net monitoring.

---

**Questions?** See [SETUP.md](SETUP.md) for troubleshooting.

