# Migration Guide: GPT-4o → GPT-5 Models

This document explains the migration from GPT-4o to the new GPT-5 model family (GPT-5, GPT-5-mini, GPT-5-nano).

## 📋 What Changed

### Models
- **Removed**: Single `OpenAIModel` using GPT-4o
- **Added**: Three new model implementations
  - `GPT5Model` - Full GPT-5 model for complex reasoning
  - `GPT5MiniModel` - Lightweight, cost-optimized GPT-5-mini
  - `GPT5NanoModel` - Ultra-fast, low-latency GPT-5-nano

### Database Schema
- **Added**: Three new model entries in the `models` table
  - `gpt-5` (GPT-5, OpenAI, Active)
  - `gpt-5-mini` (GPT-5-mini, OpenAI, Active)
  - `gpt-5-nano` (GPT-5-nano, OpenAI, Active)
- **Deprecated**: `gpt-4o` entry (can be set to inactive)

### Code Structure
- **New Files**:
  - `models/gpt5_model.py`
  - `models/gpt5_mini_model.py`
  - `models/gpt5_nano_model.py`
  - `database/migrate_gpt4o_to_gpt5.sql`
- **Modified Files**:
  - `run_monitor.py` - Updated imports and model initialization
  - `database/schema.sql` - Updated default models
  - `models/base_model.py` - Updated documentation
  - `README.md` - Updated model list
  - `ARCHITECTURE.md` - Updated architecture diagrams
  - `PROJECT_CONTEXT.md` - Updated project status
  - `SETUP.md` - Updated file structure

## 🚀 Migration Steps

### Step 1: Update Code

```bash
# Pull the latest changes
git pull origin main
```

The code changes are already in place with the new GPT-5 model implementations.

### Step 2: Update Database

Run the migration script to add the new models to your database:

```bash
# Via phpMyAdmin on Bluehost:
# 1. Select your database
# 2. Go to "Import" tab
# 3. Upload database/migrate_gpt4o_to_gpt5.sql
# 4. Click "Go"
```

Or via MySQL command line:

```bash
mysql -h yourdomain.com -u username -p database_name < database/migrate_gpt4o_to_gpt5.sql
```

### Step 3: Verify Migration

Check that the new models are active:

```sql
SELECT id, name, provider, active FROM models WHERE provider = 'OpenAI';
```

Expected output:
```
+-------------+-------------+----------+--------+
| id          | name        | provider | active |
+-------------+-------------+----------+--------+
| gpt-5       | GPT-5       | OpenAI   |      1 |
| gpt-5-mini  | GPT-5-mini  | OpenAI   |      1 |
| gpt-5-nano  | GPT-5-nano  | OpenAI   |      1 |
| gpt-4o      | GPT-4o      | OpenAI   |      0 |
+-------------+-------------+----------+--------+
```

### Step 4: Test the System

Run a manual test to ensure everything works:

```bash
# Locally (for testing):
cd aieo-monitor
python run_monitor.py
```

Or trigger via GitHub Actions:
1. Go to GitHub → Actions tab
2. Select "AI Citation Monitor" workflow
3. Click "Run workflow"
4. Monitor the logs

You should see:
```
✓ GPT-5 model initialized
✓ GPT-5-mini model initialized
✓ GPT-5-nano model initialized
✓ Claude model initialized
```

## 📊 What to Expect

### Execution Changes

**Before (GPT-4o only)**:
- Models: 2 (GPT-4o, Claude)
- Total queries: 18 (9 queries × 2 models)
- Execution time: ~2-5 minutes

**After (GPT-5 family)**:
- Models: 4 (GPT-5, GPT-5-mini, GPT-5-nano, Claude)
- Total queries: 36 (9 queries × 4 models)
- Execution time: ~4-10 minutes

### Cost Changes

**GPT-4o Pricing** (discontinued):
- No longer available

**GPT-5 Pricing**:
- GPT-5: $1.25/$10 per 1M input/output tokens
- GPT-5-mini: $0.25/$2 per 1M input/output tokens  
- GPT-5-nano: $0.05/$0.40 per 1M input/output tokens

**Estimated Weekly Cost**:
- GPT-5: ~$0.50-1.00
- GPT-5-mini: ~$0.10-0.20
- GPT-5-nano: ~$0.02-0.05
- Total (all three): ~$0.62-1.25 per week

### Performance Comparison

| Model | Speed | Quality | Cost | Use Case |
|-------|-------|---------|------|----------|
| GPT-5 | Moderate | Excellent | High | Complex reasoning, detailed analysis |
| GPT-5-mini | Fast | Good | Medium | Balanced performance and cost |
| GPT-5-nano | Very Fast | Good | Low | High-volume, speed-critical tasks |

## 🔄 Rollback Plan

If you need to rollback to GPT-4o:

### Option 1: Keep using existing code
The old `openai_model.py` file still exists and can be used if needed.

### Option 2: Revert database changes
```sql
-- Reactivate GPT-4o
UPDATE models SET active = TRUE WHERE id = 'gpt-4o';

-- Deactivate GPT-5 models
UPDATE models SET active = FALSE WHERE id IN ('gpt-5', 'gpt-5-mini', 'gpt-5-nano');
```

However, note that GPT-4o has been retired by OpenAI, so this may not work with the API.

## 🎯 Testing Checklist

After migration, verify:

- [ ] Database has new GPT-5 models
- [ ] GitHub Actions workflow runs successfully
- [ ] All three GPT-5 models execute queries
- [ ] Results are stored in database
- [ ] Dashboard displays new model data
- [ ] No errors in GitHub Actions logs
- [ ] Citation tracking works for all models
- [ ] Response times are reasonable
- [ ] API costs are within budget

## 📝 Key Differences

### API Changes
The GPT-5 models use the same `responses.create()` API as GPT-4o, so the implementation is nearly identical. The main differences are:

1. **Model Names**: `"gpt-5"`, `"gpt-5-mini"`, `"gpt-5-nano"` instead of `"gpt-4o"`
2. **Performance**: GPT-5 models offer improved reasoning and faster responses
3. **Features**: Support for new features like minimal reasoning, verbosity settings, and enhanced tool use

### No Code Changes Required for Existing Queries
Your existing queries in `config/queries.json` will work exactly the same way with the new models. No changes needed!

## 🆘 Troubleshooting

### Error: "Unknown model gpt-5"
- Make sure you have the latest OpenAI Python package
- Run: `pip install --upgrade openai`

### Error: "Model gpt-5 not found"
- Check that your OpenAI API key has access to GPT-5 models
- GPT-5 models require API access (may need to request access)

### No results from GPT-5 models
- Check GitHub Actions logs for API errors
- Verify OPENAI_API_KEY is still valid
- Ensure database migration completed successfully

### Dashboard shows old data
- Clear browser cache
- Verify new responses are in database:
  ```sql
  SELECT model_id, COUNT(*) FROM responses 
  GROUP BY model_id ORDER BY model_id;
  ```

## 📚 Additional Resources

- [OpenAI GPT-5 Documentation](https://platform.openai.com/docs/models/gpt-5)
- [GPT-5-mini Documentation](https://platform.openai.com/docs/models/gpt-5-mini)
- [GPT-5-nano Documentation](https://platform.openai.com/docs/models/gpt-5-nano)
- [Migration Script](database/migrate_gpt4o_to_gpt5.sql)
- [Architecture Guide](ARCHITECTURE.md)

## ✅ Migration Complete

Once you've completed all the steps and verified everything works, you're done! The system will now:

✅ Test 3 GPT-5 models instead of 1 GPT-4o model
✅ Provide better performance and reliability  
✅ Use cost-optimized models (especially GPT-5-nano)
✅ Track results for all models in the database
✅ Display comparative results in the dashboard

**Questions or issues?** Check the troubleshooting section above or review the main [SETUP.md](SETUP.md) guide.

---

**Migration Date**: October 28, 2025
**Version**: 2.0.0 (GPT-5 Update)

