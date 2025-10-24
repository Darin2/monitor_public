"""
Perplexity model implementation for AI Citation Monitor
Uses Perplexity's Sonar models which are optimized for real-time search
"""
import re
from typing import Dict, List, Tuple
from openai import OpenAI
from .base_model import BaseModel


class PerplexityModel(BaseModel):
    """Perplexity Sonar Pro implementation"""
    
    def __init__(self, api_key: str):
        super().__init__(api_key)
        # Perplexity uses OpenAI-compatible API
        self.client = OpenAI(
            api_key=api_key,
            base_url="https://api.perplexity.ai"
        )
        self._model = "sonar-pro"
    
    @property
    def model_id(self) -> str:
        return "sonar-pro"
    
    @property
    def model_name(self) -> str:
        return "Sonar Pro"
    
    def query(self, prompt: str) -> Dict:
        """Execute a query using Perplexity's API"""
        def _query():
            response = self.client.chat.completions.create(
                model=self._model,
                messages=[{"role": "user", "content": prompt}]
            )
            return response
        
        raw_response, elapsed_ms = self._time_query(_query)
        
        return {
            'response_text': raw_response.choices[0].message.content,
            'response_time_ms': elapsed_ms,
            'raw_response': raw_response
        }
    
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """Extract search query and cited URLs from Perplexity response"""
        search_query = None
        cited_urls = []
        
        raw_response = response['raw_response']
        response_text = response['response_text']
        
        # Perplexity includes citations in the response
        # Look for citations in the response object
        if hasattr(raw_response, 'citations') and raw_response.citations:
            cited_urls = list(raw_response.citations)
        
        # Also extract URLs from the response text using regex
        # Perplexity often includes URLs in [n] citation format
        urls_in_text = re.findall(r'https?://[^\s\)>\]]+', response_text)
        for url in urls_in_text:
            clean_url = url.rstrip('.,;:!?')
            if clean_url and clean_url not in cited_urls:
                cited_urls.append(clean_url)
        
        # Perplexity doesn't expose the search query used, so we'll note that
        # The model does real-time search internally but doesn't return the query
        search_query = None  # Not available from Perplexity API
        
        return search_query, cited_urls

