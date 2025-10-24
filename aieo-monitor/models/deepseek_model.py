"""
DeepSeek model implementation for AI Citation Monitor
STUB - To be implemented when DeepSeek API key is available
"""
from typing import Dict, List, Tuple
from .base_model import BaseModel


class DeepSeekModel(BaseModel):
    """DeepSeek implementation - STUB"""
    
    def __init__(self, api_key: str):
        super().__init__(api_key)
        # TODO: Initialize DeepSeek client when API is available
        # self.client = DeepSeekClient(api_key=api_key)
    
    @property
    def model_id(self) -> str:
        return "deepseek-chat"
    
    @property
    def model_name(self) -> str:
        return "DeepSeek Chat"
    
    def query(self, prompt: str) -> Dict:
        """Execute a query using DeepSeek's API"""
        # TODO: Implement DeepSeek API call
        # Example structure (adjust based on actual API):
        # def _query():
        #     response = self.client.chat.completions.create(
        #         model="deepseek-chat",
        #         messages=[{"role": "user", "content": prompt}],
        #         web_search=True
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
        
        raise NotImplementedError("DeepSeek model not yet implemented")
    
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """Extract search query and cited URLs from DeepSeek response"""
        # TODO: Implement metadata extraction for DeepSeek
        raise NotImplementedError("DeepSeek model not yet implemented")

