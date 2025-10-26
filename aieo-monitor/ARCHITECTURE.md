# AI Citation Monitor - Architecture

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         GITHUB ACTIONS                          │
│                     (Free, Runs Weekly)                         │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │  Workflow: monitor.yml                                     │ │
│  │  Schedule: Every Monday 9 AM UTC                           │ │
│  │  ────────────────────────────────────────────────────────  │ │
│  │                                                             │ │
│  │  1. Checkout repository                                    │ │
│  │  2. Setup Python 3.11                                      │ │
│  │  3. Install dependencies (requirements.txt)                │ │
│  │  4. Run: python run_monitor.py                             │ │
│  │     ├─ Load queries from config/queries.json               │ │
│  │     ├─ Initialize AI models (OpenAI, Claude, etc.)         │ │
│  │     ├─ Execute all queries across all models               │ │
│  │     └─ Store results directly to MySQL                     │ │
│  │                                                             │ │
│  │  Environment: GitHub Secrets                               │ │
│  │  ├─ OPENAI_API_KEY                                         │ │
│  │  ├─ ANTHROPIC_API_KEY                                      │ │
│  │  ├─ MYSQL_HOST                                             │ │
│  │  ├─ MYSQL_DATABASE                                         │ │
│  │  ├─ MYSQL_USER                                             │ │
│  │  └─ MYSQL_PASSWORD                                         │ │
│  └───────────────────────────────────────────────────────────┘ │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          │ Direct MySQL Connection
                          │ (Remote access enabled)
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                      BLUEHOST (darin.tech)                      │
│                                                                 │
│  ┌──────────────────────────────────────┐                      │
│  │        MySQL Database                │                      │
│  │  ─────────────────────────────────  │                      │
│  │                                      │                      │
│  │  Tables:                             │                      │
│  │  ├─ queries      (test queries)      │                      │
│  │  ├─ models       (AI models)         │                      │
│  │  ├─ runs         (execution groups)  │                      │
│  │  └─ responses    (all results)       │                      │
│  │                                      │                      │
│  │  Views:                              │                      │
│  │  ├─ model_performance                │                      │
│  │  ├─ query_performance                │                      │
│  │  └─ recent_citations                 │                      │
│  └──────────────────┬───────────────────┘                      │
│                     │                                           │
│                     │ PHP reads data                            │
│                     ▼                                           │
│  ┌──────────────────────────────────────┐                      │
│  │    monitor.php                        │                      │
│  │  ─────────────────────────────────  │                      │
│  │                                      │                      │
│  │  Features:                           │                      │
│  │  ├─ Summary statistics               │                      │
│  │  ├─ Model comparison chart           │                      │
│  │  ├─ Timeline chart                   │                      │
│  │  ├─ Performance table                │                      │
│  │  └─ Recent citations                 │                      │
│  │                                      │                      │
│  │  URL: darin.tech/monitor.php         │                      │
│  └──────────────────────────────────────┘                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## Component Architecture

### 1. Orchestrator (`run_monitor.py`)

```
┌────────────────────────────────────────┐
│      MonitorOrchestrator               │
│  ────────────────────────────────────  │
│                                        │
│  Initialize:                           │
│  ├─ Load queries from JSON             │
│  ├─ Initialize AI models               │
│  ├─ Connect to MySQL                   │
│  └─ Generate run_id                    │
│                                        │
│  Execute:                              │
│  ├─ For each model:                    │
│  │   └─ For each query:                │
│  │       ├─ Execute query              │
│  │       ├─ Extract metadata           │
│  │       ├─ Check for target site      │
│  │       └─ Store to database          │
│  │                                     │
│  └─ Complete run, print summary        │
└────────────────────────────────────────┘
```

### 2. Model System (Strategy Pattern)

```
        ┌─────────────────────┐
        │    BaseModel        │
        │   (Abstract)        │
        ├─────────────────────┤
        │ + query()           │
        │ + extract_metadata()│
        │ + model_id          │
        │ + model_name        │
        └──────────┬──────────┘
                   │
         ┌─────────┴─────────────────────────┐
         │                                   │
┌────────▼────────┐              ┌──────────▼────────┐
│  OpenAIModel    │              │   ClaudeModel     │
├─────────────────┤              ├───────────────────┤
│ • GPT-4o        │              │ • Claude 3.7      │
│ • Web search    │              │ • Web search      │
│ • Citation ext. │              │ • URL extraction  │
└─────────────────┘              └───────────────────┘
         │                                   │
         │       ┌─────────────┐             │
         └───────┤  + 4 more   ├─────────────┘
                 │   models    │
                 │  (stubs)    │
                 └─────────────┘
                 • DeepSeek
                 • Grok
                 • Perplexity
                 • Llama
```

### 3. Database Layer

```
┌─────────────────────────────────────────┐
│      DatabaseManager                    │
│  ─────────────────────────────────────  │
│                                         │
│  Connection:                            │
│  └─ PyMySQL → Bluehost MySQL            │
│                                         │
│  Operations:                            │
│  ├─ start_run()                         │
│  ├─ complete_run()                      │
│  ├─ fail_run()                          │
│  ├─ sync_queries()                      │
│  ├─ store_response()                    │
│  ├─ store_error()                       │
│  └─ get_run_summary()                   │
└─────────────────────────────────────────┘
```

---

## Data Flow

### Execution Flow

```
1. GitHub Actions Trigger (Weekly Cron)
   │
   ├─ Checkout code from repository
   ├─ Install Python dependencies
   └─ Execute run_monitor.py
      │
2. Load Configuration
   │
   ├─ Read config/queries.json
   ├─ Load environment variables (secrets)
   └─ Initialize database connection
      │
3. Initialize Models
   │
   ├─ Check for OPENAI_API_KEY → Create OpenAIModel
   ├─ Check for ANTHROPIC_API_KEY → Create ClaudeModel
   └─ Check for other keys → Create other models
      │
4. Execute Queries
   │
   ├─ Create run record in database
   │
   ├─ For each model:
   │   │
   │   └─ For each query:
   │       │
   │       ├─ Call model.query(query_text)
   │       │   ├─ Make API call to AI service
   │       │   ├─ Measure response time
   │       │   └─ Return response + metadata
   │       │
   │       ├─ Call model.extract_metadata(response)
   │       │   ├─ Extract search query used
   │       │   └─ Extract all cited URLs
   │       │
   │       ├─ Check if paintballevents.net in URLs
   │       │
   │       └─ Store to database
   │           ├─ INSERT INTO responses
   │           └─ UPDATE run statistics
   │
   └─ Mark run as completed
      │
5. Results Available
   │
   └─ Dashboard queries MySQL and displays data
```

### Query Execution Details

```
Single Query Execution:
═══════════════════════

1. Orchestrator calls model.query(prompt)
   │
2. Model Implementation:
   │
   ├─ OpenAI: client.responses.create(model="gpt-4o", tools=[web_search])
   │           └─ Returns response with citations in metadata
   │
   └─ Claude: client.messages.create(model="claude-3-7-sonnet", tools=[web_search])
               └─ Returns response with tool use blocks
   │
3. Model returns:
   {
     'response_text': "...",
     'response_time_ms': 1234,
     'raw_response': <API response object>
   }
   │
4. Orchestrator calls model.extract_metadata(response)
   │
5. Model Implementation:
   │
   ├─ Parse raw_response for search queries
   ├─ Parse raw_response for cited URLs
   └─ Return (search_query, cited_urls)
   │
6. Orchestrator:
   │
   ├─ Check if 'paintballevents.net' in cited_urls
   ├─ Check if 'paintballevents.net' in response_text
   │
   └─ Store to database:
       INSERT INTO responses (
         run_id, query_id, model_id, query_text,
         response, paintballevents_referenced,
         search_query, cited_urls, response_time_ms
       )
```

---

## Scalability Design

### Adding a New Model

```
Step 1: Implement Model Class
   │
   ├─ Create models/newmodel_model.py
   ├─ Inherit from BaseModel
   └─ Implement:
       ├─ query() method
       ├─ extract_metadata() method
       ├─ model_id property
       └─ model_name property

Step 2: Add to Orchestrator
   │
   └─ Edit run_monitor.py:
       if os.getenv("NEWMODEL_API_KEY"):
           models.append(NewModel(os.getenv("NEWMODEL_API_KEY")))

Step 3: Configure
   │
   ├─ Add NEWMODEL_API_KEY to GitHub Secrets
   └─ Add model to database:
       INSERT INTO models (id, name, provider, active)
       VALUES ('newmodel-id', 'Model Name', 'Provider', TRUE)

Done! Next run will test the new model.
```

### Adding New Queries

```
Step 1: Edit Configuration
   │
   └─ Edit config/queries.json:
       {
         "id": "q_new",
         "text": "Your query",
         "category": "category",
         "priority": 1,
         "active": true
       }

Step 2: Commit and Push
   │
   └─ git add config/queries.json
       git commit -m "Add new query"
       git push

Done! Next run will use the new queries.
```

---

## Security Architecture

### Secrets Management

```
Development (Local):
  .env file → python-dotenv → Environment variables
  (Never committed to git)

Production (GitHub Actions):
  GitHub Secrets → Workflow env → Environment variables
  (Encrypted at rest, only accessible during workflow)

Database Credentials:
  Stored in GitHub Secrets
  Passed as environment variables
  Never in code or logs
```

### Network Security

```
GitHub Actions → Bluehost MySQL
  │
  ├─ Remote MySQL enabled in cPanel
  ├─ Access host: % (or specific IP whitelist)
  ├─ Strong password (20+ characters)
  └─ SSL/TLS encrypted connection

Dashboard → MySQL
  │
  ├─ Local connection (localhost)
  ├─ Credentials in environment or config
  └─ No external access needed
```

---

## Monitoring & Observability

### What Gets Logged

```
GitHub Actions Logs:
  ├─ Model initialization success/failure
  ├─ Query execution progress
  ├─ Citation detection (✓ or ✗)
  ├─ Database storage confirmation
  ├─ Error details (if any)
  └─ Run summary (queries executed, errors)

Database Tracking:
  runs table:
    ├─ run_id, started_at, completed_at
    ├─ status (running, completed, failed)
    ├─ queries_executed, errors_count
    └─ notes

  responses table:
    └─ Every query result with full metadata
```

### Error Handling

```
Model Level:
  try:
    response = model.query(query)
  except Exception as e:
    log error
    store_error(run_id, query_id, model_id, error)
    continue to next query

Orchestrator Level:
  try:
    run all queries
  except Exception as e:
    fail_run(run_id, error)
    raise

Database Level:
  ├─ Transaction per response
  ├─ Rollback on error
  └─ Connection error handling
```

---

## Performance Considerations

### Execution Time

```
Single Query:
  ├─ API call: 1-5 seconds
  ├─ Metadata extraction: <100ms
  └─ Database storage: <100ms
  Total: ~1-5 seconds per query

Full Run:
  ├─ Models: 2 active (OpenAI, Claude)
  ├─ Queries: 9 configured
  ├─ Total: 18 queries
  ├─ Sequential execution
  └─ Expected time: 2-5 minutes

GitHub Actions:
  ├─ Free tier: 2,000 minutes/month
  ├─ Weekly run: ~5 minutes
  ├─ Monthly usage: ~20 minutes
  └─ Well within free tier!
```

### Cost Analysis

```
GitHub Actions:
  ├─ Cost: FREE (within 2,000 min/month)
  └─ Usage: ~20 min/month

AI Model APIs:
  ├─ OpenAI: ~$0.01-0.05 per query
  ├─ Claude: ~$0.01-0.05 per query
  └─ Weekly run: ~$0.20-1.00

Database (Bluehost):
  ├─ Cost: Included in hosting
  ├─ Storage: Minimal (<100MB/year)
  └─ Bandwidth: Negligible

Dashboard (Bluehost):
  ├─ Cost: Included in hosting
  └─ PHP execution: Native, no cost

Total Monthly Cost: ~$1-4 (mostly API calls)
```

---

## Deployment Architecture

### CI/CD Pipeline

```
Local Development:
  ├─ Edit code
  ├─ Test locally (python run_monitor.py)
  └─ Commit and push

GitHub:
  ├─ Receives push
  ├─ Runs on schedule (cron)
  └─ Or manual trigger (workflow_dispatch)

Production:
  ├─ Executes in GitHub-hosted runner
  ├─ Stores results in Bluehost MySQL
  └─ Dashboard automatically shows new data

No deployment step for dashboard!
  └─ Upload once, reads live data from MySQL
```

---

## Summary

This architecture provides:

✅ **Scalability** - Easy to add models and queries
✅ **Reliability** - GitHub Actions, MySQL database
✅ **Maintainability** - Modular, well-documented code
✅ **Cost-effective** - Free GitHub Actions, minimal API costs
✅ **Bluehost-friendly** - PHP dashboard, MySQL database
✅ **Secure** - Secrets management, encrypted connections
✅ **Observable** - Comprehensive logging and tracking
✅ **Future-proof** - Ready for 4 more AI models

The system is production-ready and designed for long-term use! 🚀

