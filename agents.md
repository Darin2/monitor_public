# Project Setup

## Environment Variables

This project uses a `.env` file for configuration.

**Location:** `/Users/darinhardin/Documents/GitHub/monitor/.env`

**Contents:**
- `OPENAI_API_KEY` - OpenAI API key for making API calls

**Usage:**
- The `.env` file is loaded using `python-dotenv`
- API key is accessed via `os.getenv("OPENAI_API_KEY")`
- No quotes needed around values in the `.env` file

