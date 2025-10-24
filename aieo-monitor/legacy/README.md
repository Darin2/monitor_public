# Legacy Files (v1.0)

These files are from the original SQLite-based monitoring system (v1.0). They are kept for reference but are **no longer used** in the current system.

## What Was v1.0?

The original system used:
- **SQLite database** (`query_responses.db`) - Local file-based database
- **Manual execution** - Had to run Python scripts manually
- **Single model at a time** - Separate test scripts for each model
- **No dashboard** - Used `view_database.py` to view results in terminal
- **No automation** - No scheduled runs

## Files in This Folder

### Test Scripts
- `test_openai.py` - Original OpenAI test script (SQLite-based)
- `test_claude.py` - Original Claude test script (SQLite-based)
- `run_all_queries.py` - Original query runner (SQLite-based)

### Visualization
- `dashboard.php` - Original SQLite-based dashboard
- `view_database.py` - Terminal-based database viewer

## Current System (v2.0)

The new system uses:
- **MySQL database** on Bluehost
- **GitHub Actions** for automatic weekly execution
- **Modular model system** - Easy to add new models
- **Beautiful dashboard** (`dashboard_mysql.php`)
- **Full automation** - Runs every Monday automatically

## Migration

If you have old data in `query_responses.db`, you can migrate it using:
```bash
python migrate_sqlite_to_mysql.py
```

This will import all old SQLite data into the new MySQL database.

---

**These files can be deleted once you're confident the new system is working well!**

