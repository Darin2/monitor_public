# AI Citation Monitor - Setup Guide

## Overview

This monitoring system tracks whether AI models cite paintballevents.net when answering paintball-related queries. The system runs weekly via GitHub Actions and stores results in a MySQL database on Bluehost.

**Architecture:**
- **Runner**: GitHub Actions (runs Python weekly)
- **Database**: MySQL on Bluehost (darin.tech)
- **Dashboard**: PHP on Bluehost

---

## 🚀 Quick Start

### Step 1: Bluehost Database Setup

1. **Create MySQL Database via cPanel**
   - Log into Bluehost cPanel
   - Go to "MySQL Databases"
   - Create a new database: `darintec_monitor` (or similar)
   - Create a new MySQL user with a strong password
   - Add user to database with ALL PRIVILEGES

2. **Enable Remote MySQL Access**
   - In cPanel, go to "Remote MySQL"
   - Add access host: `%` (allows GitHub Actions to connect)
   - **Security Note**: For production, you can whitelist GitHub's IP ranges instead

3. **Run Database Schema**
   - In cPanel, go to "phpMyAdmin"
   - Select your database
   - Go to "Import" tab
   - Upload and run `database/schema.sql`
   - Verify tables were created: `queries`, `models`, `runs`, `responses`

4. **Note Your Database Credentials**
   ```
   Host: yourdomain.com (or IP address)
   Database: darintec_monitor
   Username: darintec_monitor
   Password: your_secure_password
   ```

### Step 2: GitHub Repository Setup

1. **Add Repository Secrets**
   - Go to your GitHub repository
   - Navigate to Settings → Secrets and variables → Actions
   - Click "New repository secret"
   - Add the following secrets:

   **AI Model API Keys:**
   ```
   OPENAI_API_KEY=sk-...
   ANTHROPIC_API_KEY=sk-ant-...
   DEEPSEEK_API_KEY=...       (optional, for future)
   GROK_API_KEY=...            (optional, for future)
   PERPLEXITY_API_KEY=...      (optional, for future)
   LLAMA_API_KEY=...           (optional, for future)
   ```

   **MySQL Connection:**
   ```
   MYSQL_HOST=yourdomain.com
   MYSQL_DATABASE=darintec_monitor
   MYSQL_USER=darintec_monitor
   MYSQL_PASSWORD=your_secure_password
   ```

2. **Commit and Push**
   ```bash
   git add .
   git commit -m "Add AI Citation Monitor infrastructure"
   git push origin main
   ```

### Step 3: Test the Workflow

1. **Manual Trigger**
   - Go to GitHub → Actions tab
   - Select "AI Citation Monitor" workflow
   - Click "Run workflow"
   - Watch the logs to ensure it completes successfully

2. **Verify Results**
   - Check Bluehost phpMyAdmin
   - Query the `responses` table
   - Should see new entries with latest timestamp

### Step 4: Deploy Dashboard to Bluehost

1. **Upload Dashboard File**
   - Via FTP or cPanel File Manager
   - Upload `monitor.php` to your public_html directory
   - Rename to `monitor.php` (or any name you prefer)

2. **Configure Database Connection**
   - Edit the file on the server (lines 10-13)
   - Or set environment variables in `.htaccess`:
   ```apache
   SetEnv MYSQL_HOST "localhost"
   SetEnv MYSQL_DATABASE "darintec_monitor"
   SetEnv MYSQL_USER "darintec_monitor"
   SetEnv MYSQL_PASSWORD "your_password"
   ```

3. **Access Dashboard**
   - Visit: `https://darin.tech/monitor.php`
   - Should see the terminal-style dashboard with data

---

## 📋 Configuration

### Editing Queries

Edit `config/queries.json` to add/modify test queries:

```json
{
  "queries": [
    {
      "id": "q1",
      "text": "Your query here",
      "category": "category_name",
      "priority": 1,
      "active": true
    }
  ]
}
```

**Fields:**
- `id`: Unique identifier (required)
- `text`: The actual query to test (required)
- `category`: Group related queries (optional)
- `priority`: 1=high, 2=medium, 3=low (optional)
- `active`: Set to false to temporarily disable (optional)

### Changing Schedule

Edit `.github/workflows/monitor.yml`:

```yaml
on:
  schedule:
    - cron: '0 9 * * 1'  # Every Monday at 9 AM UTC
```

Common schedules:
- `'0 9 * * 1'` - Every Monday at 9 AM
- `'0 9 * * *'` - Every day at 9 AM
- `'0 9 1 * *'` - First day of every month at 9 AM

### Adding New AI Models

1. **Get API Key** for the model
2. **Add to GitHub Secrets** (e.g., `DEEPSEEK_API_KEY`)
3. **Implement Model Class** in `models/` directory:
   - Inherit from `BaseModel`
   - Implement `query()` and `extract_metadata()` methods
4. **Uncomment in `run_monitor.py`** (lines 62-90)
5. **Update Database**:
   ```sql
   UPDATE models SET active = 1 WHERE id = 'deepseek-chat';
   ```

---

## 🗂️ File Structure

```
aieo-monitor/
├── config/
│   └── queries.json              # Test queries configuration
├── models/
│   ├── base_model.py             # Abstract base class
│   ├── openai_model.py           # OpenAI GPT-4o
│   ├── claude_model.py           # Anthropic Claude
│   ├── deepseek_model.py         # DeepSeek (stub)
│   ├── grok_model.py             # Grok (stub)
│   ├── perplexity_model.py       # Perplexity (stub)
│   └── llama_model.py            # Llama (stub)
├── database/
│   ├── schema.sql                # MySQL database schema
│   └── operations.py             # Database CRUD operations
├── utils/
│   └── __init__.py
├── run_monitor.py                # Main orchestrator script
├── requirements.txt              # Python dependencies
├── monitor.php                   # Dashboard for Bluehost
└── SETUP.md                      # This file
```

---

## 🔍 Database Schema

### Tables

**queries** - Test queries
```sql
id, query_text, category, priority, active, created_at
```

**models** - AI models being tested
```sql
id, name, provider, active, created_at
```

**runs** - Each execution (weekly cron)
```sql
run_id, started_at, completed_at, status, queries_executed, errors_count, notes
```

**responses** - All query results
```sql
id, run_id, timestamp, query_id, model_id, query_text, response, 
paintballevents_referenced, search_query, cited_urls, response_time_ms, error
```

### Views

**model_performance** - Aggregate model statistics
**query_performance** - Aggregate query statistics
**recent_citations** - Latest successful citations

---

## 🛠️ Troubleshooting

### GitHub Actions Fails

**Error: "Could not connect to MySQL"**
- Check that Remote MySQL is enabled in Bluehost cPanel
- Verify MYSQL_HOST is correct (try IP address instead of domain)
- Check MySQL user has remote access permissions

**Error: "No module named 'pymysql'"**
- Requirements.txt issue - check workflow logs
- Ensure pip cache is working

**Error: "No models initialized"**
- Check that API keys are set in GitHub Secrets
- Verify secret names match exactly (case-sensitive)

### Dashboard Shows No Data

**Blank page or errors**
- Check PHP error logs in cPanel
- Verify database credentials in monitor.php
- Ensure database has data: `SELECT COUNT(*) FROM responses;`

**Connection errors**
- On Bluehost, use 'localhost' as MYSQL_HOST, not domain name
- Check MySQL user has permissions on the database

### Queries Not Running

**No errors but no data**
- Check GitHub Actions logs for actual errors
- Verify queries.json is valid JSON
- Check that models are marked as active in database

---

## 📊 Monitoring & Maintenance

### Weekly Checklist

1. Check GitHub Actions run status
2. Review dashboard for new citations
3. Monitor error rates
4. Check citation trends

### Monthly Tasks

1. Review query performance - adjust as needed
2. Check for API key expiration
3. Update model list if new models available
4. Optimize slow queries

### Yearly Tasks

1. Rotate API keys
2. Review and archive old data
3. Update dependencies (requirements.txt)

---

## 🔐 Security Notes

1. **Never commit API keys** - always use GitHub Secrets
2. **Use strong MySQL passwords** - generate random 20+ char passwords
3. **Restrict Remote MySQL access** - whitelist specific IPs when possible
4. **Keep dependencies updated** - run `pip list --outdated` periodically
5. **Monitor usage/costs** - set up billing alerts for API providers

---

## 📈 Future Enhancements

### Planned Features

- [ ] Email alerts when citation rate drops
- [ ] Competitor tracking (which sites ARE being cited?)
- [ ] Query A/B testing framework
- [ ] Historical trend analysis
- [ ] REST API for programmatic access
- [ ] Mobile-responsive dashboard
- [ ] Export data to CSV/JSON

### Adding New Models

Ready to implement when API access is available:
- **DeepSeek** - Stub ready in `models/deepseek_model.py`
- **Grok** - Stub ready in `models/grok_model.py`
- **Perplexity** - Stub ready in `models/perplexity_model.py`
- **Llama** - Stub ready in `models/llama_model.py`

Just implement the `query()` and `extract_metadata()` methods!

---

## 📞 Support

For issues or questions:
1. Check GitHub Actions logs first
2. Review Bluehost error logs (cPanel → Error Log)
3. Check database for actual data
4. Review this setup guide

---

## 🎯 Success Criteria

Your setup is working correctly when:

✅ GitHub Actions workflow runs weekly without errors
✅ Database receives new records each week
✅ Dashboard displays data and charts correctly
✅ Citation metrics are being tracked
✅ No API errors in logs

---

**Last Updated**: October 2025
**Version**: 1.0.0

