"""
Anthropic Claude Sonnet 4.5 model implementation for AI Citation Monitor
"""
import re
import warnings
from typing import Dict, List, Tuple
from anthropic import Anthropic
from .base_model import BaseModel

# Suppress warnings
warnings.filterwarnings('ignore', category=UserWarning, module='pydantic')


class ClaudeSonnet45Model(BaseModel):
    """Anthropic Claude Sonnet 4.5 implementation"""
    
    def __init__(self, api_key: str):
        super().__init__(api_key)
        self.client = Anthropic(api_key=api_key)
        self._model = "claude-sonnet-4-5-20250929"
    
    @property
    def model_id(self) -> str:
        return "claude-sonnet-4-5"
    
    @property
    def model_name(self) -> str:
        return "Claude Sonnet 4.5"
    
    def query(self, prompt: str) -> Dict:
        """Execute a query using Claude's API with web search"""
        def _query():
            response = self.client.messages.create(
                model=self._model,
                max_tokens=4096,
                tools=[
                    {
                        "type": "web_search_20250305",
                        "name": "web_search"
                    }
                ],
                messages=[
                    {"role": "user", "content": prompt}
                ]
            )
            return response
        
        raw_response, elapsed_ms = self._time_query(_query)
        
        # Extract text from response blocks
        response_text = ""
        for block in raw_response.content:
            if hasattr(block, 'type') and block.type == 'text':
                response_text += block.text
        
        return {
            'response_text': response_text,
            'response_time_ms': elapsed_ms,
            'raw_response': raw_response
        }
    
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """Extract search query and cited URLs from Claude response"""
        search_query = None
        cited_urls = []
        
        raw_response = response['raw_response']
        
        # Claude's response structure is different - parse the content blocks
        for block in raw_response.content:
            # Check for tool use blocks
            if hasattr(block, 'type') and block.type == 'tool_use':
                if block.name == 'web_search':
                    # Extract search query from tool input
                    if hasattr(block, 'input') and 'query' in block.input:
                        search_query = block.input['query']
            
            # Check for text blocks that might contain citations or URLs
            if hasattr(block, 'type') and block.type == 'text':
                # Extract URLs from the text content using regex
                text = block.text
                # Find all URLs in the text (http or https)
                urls_with_protocol = re.findall(r'https?://[^\s\)>\]]+', text)
                # Also find domain names without protocol (e.g., "example.com")
                urls_without_protocol = re.findall(
                    r'(?<![/@\w])([a-zA-Z0-9-]+\.(?:com|net|org|edu|gov|io|co)[^\s\)>\]]*)', 
                    text
                )
                
                # Combine both lists
                all_urls = urls_with_protocol + list(urls_without_protocol)
                
                for url in all_urls:
                    # Clean up URL (remove trailing punctuation)
                    clean_url = url.rstrip('.,;:!?')
                    # Remove utm_source parameter for cleaner URLs
                    clean_url = clean_url.split('?utm_source')[0]
                    if clean_url and clean_url not in cited_urls:
                        cited_urls.append(clean_url)
        
        return search_query, cited_urls

