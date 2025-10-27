#!/usr/bin/env python3
"""
AI Citation Monitor - Main Orchestrator
Runs queries across multiple AI models and tracks citations

Designed to run as a weekly cron job via GitHub Actions
"""
import os
import sys
import json
import uuid
from datetime import datetime
from dotenv import load_dotenv

# Add current directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from database.operations import DatabaseManager
from models.openai_model import OpenAIModel
from models.claude_model import ClaudeModel
from models.deepseek_model import DeepSeekModel
from models.grok_model import GrokModel
from models.perplexity_model import PerplexityModel
from models.llama_model import LlamaModel

# Load environment variables
load_dotenv()


class MonitorOrchestrator:
    """Orchestrates the monitoring process across all models and queries"""
    
    def __init__(self):
        """Initialize the orchestrator"""
        self.db = DatabaseManager()
        self.run_id = f"run_{datetime.now().strftime('%Y%m%d_%H%M%S')}_{str(uuid.uuid4())[:8]}"
        self.models = self._initialize_models()
        self.queries = self._load_queries()
        
        print(f"\n{'='*80}")
        print(f"AI CITATION MONITOR")
        print(f"{'='*80}")
        print(f"Run ID: {self.run_id}")
        print(f"Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"Models: {len(self.models)} active")
        print(f"Queries: {len(self.queries)} active")
        print(f"{'='*80}\n")
    
    def _initialize_models(self):
        """Initialize all active models that have API keys configured"""
        models = []
        
        # OpenAI GPT-4o
        if os.getenv("OPENAI_API_KEY"):
            try:
                models.append(OpenAIModel(os.getenv("OPENAI_API_KEY")))
                print("✓ OpenAI model initialized")
            except Exception as e:
                print(f"✗ OpenAI model failed to initialize: {e}")
        
        # Anthropic Claude
        if os.getenv("ANTHROPIC_API_KEY"):
            try:
                models.append(ClaudeModel(os.getenv("ANTHROPIC_API_KEY")))
                print("✓ Claude model initialized")
            except Exception as e:
                print(f"✗ Claude model failed to initialize: {e}")
        
        # DeepSeek (commented out until implemented)
        # if os.getenv("DEEPSEEK_API_KEY"):
        #     try:
        #         models.append(DeepSeekModel(os.getenv("DEEPSEEK_API_KEY")))
        #         print("✓ DeepSeek model initialized")
        #     except NotImplementedError:
        #         print("⚠️  DeepSeek model not yet implemented")
        #     except Exception as e:
        #         print(f"✗ DeepSeek model failed to initialize: {e}")
        
        # Grok (commented out until implemented)
        # if os.getenv("GROK_API_KEY"):
        #     try:
        #         models.append(GrokModel(os.getenv("GROK_API_KEY")))
        #         print("✓ Grok model initialized")
        #     except NotImplementedError:
        #         print("⚠️  Grok model not yet implemented")
        #     except Exception as e:
        #         print(f"✗ Grok model failed to initialize: {e}")
        
        # Perplexity
        if os.getenv("PERPLEXITY_API_KEY"):
            try:
                models.append(PerplexityModel(os.getenv("PERPLEXITY_API_KEY")))
                print("✓ Perplexity model initialized")
            except Exception as e:
                print(f"✗ Perplexity model failed to initialize: {e}")
        
        # Llama (commented out until implemented)
        # if os.getenv("LLAMA_API_KEY"):
        #     try:
        #         models.append(LlamaModel(os.getenv("LLAMA_API_KEY")))
        #         print("✓ Llama model initialized")
        #     except NotImplementedError:
        #         print("⚠️  Llama model not yet implemented")
        #     except Exception as e:
        #         print(f"✗ Llama model failed to initialize: {e}")
        
        if not models:
            print("✗ ERROR: No models initialized! Check your API keys in .env")
            sys.exit(1)
        
        return models
    
    def _load_queries(self):
        """Load queries from config file"""
        config_path = os.path.join(os.path.dirname(__file__), 'config', 'queries.json')
        
        try:
            with open(config_path, 'r') as f:
                data = json.load(f)
                queries = [q for q in data['queries'] if q.get('active', True)]
                
            # Sync queries to database
            self.db.sync_queries(data['queries'])
            
            return queries
            
        except FileNotFoundError:
            print(f"✗ ERROR: Config file not found: {config_path}")
            sys.exit(1)
        except json.JSONDecodeError as e:
            print(f"✗ ERROR: Invalid JSON in config file: {e}")
            sys.exit(1)
    
    def run(self):
        """Execute all queries across all models"""
        try:
            # Start the run
            self.db.start_run(self.run_id)
            
            total_queries = len(self.models) * len(self.queries)
            completed = 0
            
            # Iterate through each model
            for model in self.models:
                print(f"\n{'='*80}")
                print(f"Testing Model: {model.model_name} ({model.model_id})")
                print(f"{'='*80}\n")
                
                # Iterate through each query
                for query in self.queries:
                    completed += 1
                    progress = f"[{completed}/{total_queries}]"
                    
                    print(f"{progress} Query: {query['text'][:60]}...")
                    
                    try:
                        # Execute query
                        result = model.query(query['text'])
                        
                        # Extract metadata
                        search_query, cited_urls = model.extract_metadata(result)
                        
                        # Check if paintballevents.net is referenced
                        paintballevents_ref = self._check_reference(
                            cited_urls, 
                            result.get('response_text', '')
                        )
                        
                        # Store result in database
                        self.db.store_response(
                            run_id=self.run_id,
                            query_id=query['id'],
                            query_text=query['text'],
                            model_id=model.model_id,
                            response_text=result['response_text'],
                            paintballevents_ref=paintballevents_ref,
                            search_query=search_query,
                            cited_urls=cited_urls,
                            response_time_ms=result.get('response_time_ms')
                        )
                        
                    except Exception as e:
                        print(f"  ✗ Error: {str(e)[:100]}")
                        self.db.store_error(
                            self.run_id,
                            query['id'],
                            model.model_id,
                            query['text'],
                            str(e)
                        )
            
            # Complete the run
            self.db.complete_run(self.run_id)
            
            # Print summary
            self._print_summary()
            
        except Exception as e:
            print(f"\n✗ FATAL ERROR: {e}")
            self.db.fail_run(self.run_id, str(e))
            raise
        
        finally:
            self.db.close()
    
    def _check_reference(self, cited_urls: list, response_text: str) -> bool:
        """Check if paintballevents.net is referenced in URLs or response text"""
        # Check in cited URLs
        for url in cited_urls:
            if 'paintballevents.net' in url.lower():
                return True
        
        # Check in response text
        if 'paintballevents.net' in response_text.lower():
            return True
        
        return False
    
    def _print_summary(self):
        """Print summary of the run"""
        summary = self.db.get_run_summary(self.run_id)
        
        print(f"\n{'='*80}")
        print(f"RUN SUMMARY")
        print(f"{'='*80}")
        print(f"Run ID: {summary['run_id']}")
        print(f"Status: {summary['status']}")
        print(f"Started: {summary['started_at']}")
        print(f"Completed: {summary['completed_at']}")
        print(f"Queries executed: {summary['queries_executed']}")
        print(f"Errors: {summary['errors_count']}")
        print(f"{'='*80}\n")


def main():
    """Main entry point"""
    try:
        orchestrator = MonitorOrchestrator()
        orchestrator.run()
        
    except KeyboardInterrupt:
        print("\n\n✗ Interrupted by user")
        sys.exit(1)
    
    except Exception as e:
        print(f"\n✗ Fatal error: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    main()

