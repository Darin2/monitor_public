"""
Perplexity model implementation for AI Citation Monitor
STUB - To be implemented when Perplexity API key is available
"""
from typing import Dict, List, Tuple
from .base_model import BaseModel


class PerplexityModel(BaseModel):
    """Perplexity Sonar Pro implementation - STUB"""
    
    def __init__(self, api_key: str):
        super().__init__(api_key)
        # TODO: Initialize Perplexity client when API is available
        # Note: Perplexity uses OpenAI-compatible API
        # from openai import OpenAI
        # self.client = OpenAI(
        #     api_key=api_key,
        #     base_url="https://api.perplexity.ai"
        # )
    
    @property
    def model_id(self) -> str:
        return "sonar-pro"
    
    @property
    def model_name(self) -> str:
        return "Sonar Pro"
    
    def query(self, prompt: str) -> Dict:
        """Execute a query using Perplexity's API"""
        # TODO: Implement Perplexity API call
        # Example structure:
        # def _query():
        #     response = self.client.chat.completions.create(
        #         model="sonar-pro",
        #         messages=[{"role": "user", "content": prompt}]
        #     )
        #     return response
        # 
        # raw_response, elapsed_ms = self._time_query(_query)
        # 
        # return {
        #     'response_text': raw_response.choices[0].message.content,
        #     'response_time_ms': elapsed_ms,
        #     'raw_response': raw_response
        # }
        
        raise NotImplementedError("Perplexity model not yet implemented")
    
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """Extract search query and cited URLs from Perplexity response"""
        # TODO: Implement metadata extraction for Perplexity
        # Perplexity includes citations in response metadata
        raise NotImplementedError("Perplexity model not yet implemented")

