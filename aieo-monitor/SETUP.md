# Setup Guide - AI Citation Monitor

This guide will help you set up the AI Citation Monitor for your own website/domain.

## Prerequisites

⚠️ **Security Note:** This project uses API keys and database credentials. **Never commit these to GitHub.** Use `.env` files locally (already gitignored) and GitHub Secrets for production.

Before you begin, make sure you have:

- ✅ A GitHub account (for free GitHub Actions)
- ✅ A MySQL database (shared hosting like Bluehost works fine)
- ✅ A web server with PHP support (for the dashboard)
- ✅ API keys for the AI models you want to monitor:
  - OpenAI (for GPT-5 models)
  - Anthropic (for Claude models)
  - Perplexity (for Sonar Pro)
  - Optional: DeepSeek, Grok, Llama

## Step 1: Fork and Clone the Repository

1. Fork this repository to your GitHub account
2. Clone your fork locally:
```bash
git clone https://github.com/YOUR_USERNAME/monitor_public.git
cd monitor_public/aieo-monitor
```

## Step 2: Customize for Your Domain

### 2.1 Update Target Domain

The code currently checks for `paintballevents.net`. You need to customize this for your domain.

**File: `run_monitor.py`**
- Find the `_check_reference()` method (around line 251)
- Replace `'paintballevents.net'` with your domain name (e.g., `'yourdomain.com'`)

**File: `monitor.php`** (if you're using the PHP dashboard)
- Search for `paintballevents.net` and replace with your domain
- Update any display text that references the specific domain

### 2.2 Customize Test Queries

Edit `config/queries.json` to include queries relevant to your domain:

```json
{
  "queries": [
    {
      "id": "q1",
      "text": "Find [your topic] events in [your location]",
      "category": "general",
      "priority": 1,
      "active": true
    }
  ]
}
```

## Step 3: Set Up MySQL Database

### 3.1 Create Database

1. Log into your hosting control panel (cPanel, phpMyAdmin, etc.)
2. Create a new MySQL database
3. Create a MySQL user and grant it full privileges to the database
4. Note down:
   - Database host (usually `localhost` or an IP address)
   - Database name
   - Database username
   - Database password

### 3.2 Run Schema

1. Open your database management tool (phpMyAdmin, MySQL Workbench, etc.)
2. Select your database
3. Run the SQL from `database/schema.sql`
4. Verify tables were created: `queries`, `models`, `runs`, `responses`

### 3.3 Enable Remote MySQL Access (for GitHub Actions)

If your database is on shared hosting (like Bluehost):

1. In cPanel, go to "Remote MySQL" or "MySQL Remote Access"
2. Add `%` to allowed hosts (or specific GitHub Actions IP ranges)
3. This allows GitHub Actions to connect to your database

**Security Note:** Use a strong password (20+ characters) and consider restricting to specific IPs if possible.

## Step 4: Configure GitHub Actions

⚠️ **IMPORTANT:** For GitHub Actions, **DO NOT** put API keys in your code or `.env` file. Use GitHub Secrets instead (see below).

### 4.1 Add Repository Secrets

In your GitHub repository, go to:
**Settings → Secrets and variables → Actions → New repository secret**

Add these secrets:

**AI Model API Keys:**
- `OPENAI_API_KEY` - Your OpenAI API key
- `ANTHROPIC_API_KEY` - Your Anthropic API key
- `PERPLEXITY_API_KEY` - Your Perplexity API key
- (Optional) `DEEPSEEK_API_KEY`, `GROK_API_KEY`, `LLAMA_API_KEY`

**Database Credentials:**
- `MYSQL_HOST` - Your MySQL host (e.g., `mysql.yourhost.com` or IP)
- `MYSQL_DATABASE` - Your database name
- `MYSQL_USER` - Your MySQL username
- `MYSQL_PASSWORD` - Your MySQL password

### 4.2 Verify GitHub Actions Workflow

The workflow file should already exist at `.github/workflows/monitor.yml`. It's configured to:
- Run weekly on Mondays at 9 AM UTC
- Allow manual triggers via GitHub Actions UI

To change the schedule, edit the cron expression:
```yaml
schedule:
  - cron: '0 9 * * 1'  # Every Monday at 9 AM UTC
```

## Step 5: Test Locally (Optional but Recommended)

### 5.1 Install Dependencies

```bash
cd aieo-monitor
pip install -r requirements.txt
```

### 5.2 Create .env File

Create a `.env` file in the `aieo-monitor/` directory with your credentials:

```bash
# AI Model API Keys
OPENAI_API_KEY=your_openai_api_key_here
ANTHROPIC_API_KEY=your_anthropic_api_key_here
PERPLEXITY_API_KEY=your_perplexity_api_key_here

# MySQL Database Connection
MYSQL_HOST=your_mysql_host_here
MYSQL_DATABASE=your_database_name_here
MYSQL_USER=your_mysql_username_here
MYSQL_PASSWORD=your_mysql_password_here
```

⚠️ **CRITICAL SECURITY WARNING:** 
- **NEVER commit your `.env` file to GitHub** - it contains your API keys and database passwords
- The `.env` file is already in `.gitignore` to prevent accidental commits
- If you accidentally commit API keys, **immediately revoke them** and generate new ones
- For production (GitHub Actions), use GitHub Secrets instead (see Step 4)

### 5.3 Test Database Connection

You can create a simple test script or run:
```bash
python run_monitor.py
```

This will:
- Connect to your database
- Load queries from `config/queries.json`
- Execute queries across all configured models
- Store results in your database

**Note:** This will make real API calls and may incur costs. Start with a small number of queries or disable some models.

## Step 6: Deploy PHP Dashboard (Optional)

If you want a web dashboard to view results:

1. Upload `monitor.php` to your web server
2. Update database connection details in `monitor.php` (if different from environment variables)
3. Access the dashboard at `https://yourdomain.com/monitor.php`

**Note:** The dashboard reads directly from MySQL, so make sure it can connect to the same database.

## Step 7: Verify Everything Works

### 7.1 Manual GitHub Actions Run

1. Go to your repository on GitHub
2. Click "Actions" tab
3. Select "AI Citation Monitor" workflow
4. Click "Run workflow" → "Run workflow"
5. Watch the logs to ensure it completes successfully

### 7.2 Check Database

Query your database to verify data is being stored:
```sql
SELECT * FROM responses ORDER BY timestamp DESC LIMIT 10;
SELECT * FROM runs ORDER BY started_at DESC LIMIT 5;
```

### 7.3 Check Dashboard

If you deployed the PHP dashboard, visit it and verify:
- Statistics are displaying
- Recent citations are showing
- Charts are rendering

## Troubleshooting

### GitHub Actions Fails to Connect to Database

- Verify MySQL remote access is enabled
- Check that `MYSQL_HOST` includes port if needed (e.g., `host:3306`)
- Ensure firewall allows connections from GitHub Actions IPs
- Test connection manually with MySQL client

### No Models Initialize

- Check that API keys are correctly set in GitHub Secrets
- Verify API keys are valid and have credits/quota
- Check GitHub Actions logs for specific error messages

### Empty Responses

- Some models may return empty responses for certain queries
- Check API rate limits
- Verify your API keys have sufficient quota
- Review model-specific error messages in logs

### Database Schema Errors

- Ensure you ran the complete `schema.sql` file
- Check that your MySQL version supports JSON columns (MySQL 5.7+)
- Verify foreign key constraints are working

## Next Steps

Once everything is working:

1. **Customize queries** - Add queries relevant to your domain
2. **Add more models** - Uncomment additional models in `run_monitor.py` if you have API keys
3. **Adjust schedule** - Change the cron schedule in `.github/workflows/monitor.yml`
4. **Customize dashboard** - Modify `monitor.php` to match your branding
5. **Monitor costs** - Track API usage to manage costs

## Cost Estimates

Approximate monthly costs (varies by usage):
- **GitHub Actions**: FREE (within 2,000 min/month)
- **Database hosting**: Usually included in shared hosting
- **API calls**: $1-10/month depending on:
  - Number of queries
  - Number of models
  - Frequency of runs
  - Response length

Example: 9 queries × 4 models × 4 runs/month = 144 API calls/month ≈ $2-5/month

## Support

If you encounter issues:
1. Check GitHub Actions logs for error messages
2. Review database connection settings
3. Verify API keys are valid
4. Check that all dependencies are installed correctly

For questions about the codebase, refer to:
- `README.md` - Project overview
- `ARCHITECTURE.md` - System architecture details
- Model implementations in `models/` directory
