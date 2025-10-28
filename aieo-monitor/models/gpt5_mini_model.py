"""
GPT-5-mini model implementation for AI Citation Monitor
"""
import warnings
from typing import Dict, List, Tuple
from openai import OpenAI
from .base_model import BaseModel

# Suppress Pydantic serialization warnings
warnings.filterwarnings('ignore', category=UserWarning, module='pydantic')


class GPT5MiniModel(BaseModel):
    """OpenAI GPT-5-mini implementation"""
    
    def __init__(self, api_key: str):
        super().__init__(api_key)
        self.client = OpenAI(api_key=api_key)
        self._model = "gpt-5-mini"
    
    @property
    def model_id(self) -> str:
        return "gpt-5-mini"
    
    @property
    def model_name(self) -> str:
        return "GPT-5-mini"
    
    def query(self, prompt: str) -> Dict:
        """Execute a query using OpenAI's API with web search"""
        def _query():
            response = self.client.responses.create(
                model=self._model,
                tools=[{"type": "web_search"}],
                input=prompt
            )
            return response
        
        raw_response, elapsed_ms = self._time_query(_query)
        
        return {
            'response_text': raw_response.output_text,
            'response_time_ms': elapsed_ms,
            'raw_response': raw_response
        }
    
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """Extract search query and cited URLs from OpenAI response"""
        search_query = None
        cited_urls = []
        
        raw_response = response['raw_response']
        response_dict = raw_response.model_dump()
        output = response_dict.get('output', [])
        
        # Extract search query from web_search_call
        for item in output:
            if item.get('type') == 'web_search_call':
                search_query = item.get('action', {}).get('query')
            
            # Extract URLs from message annotations
            if item.get('type') == 'message':
                content = item.get('content', [])
                for content_item in content:
                    annotations = content_item.get('annotations', [])
                    for annotation in annotations:
                        if annotation.get('type') == 'url_citation':
                            url = annotation.get('url', '')
                            # Remove utm_source parameter for cleaner URLs
                            clean_url = url.split('?utm_source')[0]
                            if clean_url and clean_url not in cited_urls:
                                cited_urls.append(clean_url)
        
        return search_query, cited_urls

