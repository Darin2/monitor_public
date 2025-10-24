"""
Base model interface for AI Citation Monitor
All AI model implementations should inherit from this class
"""
from abc import ABC, abstractmethod
from typing import Dict, List, Tuple
import time


class BaseModel(ABC):
    """Abstract base class for AI models"""
    
    def __init__(self, api_key: str):
        """
        Initialize the model with an API key
        
        Args:
            api_key: API key for the model provider
        """
        self.api_key = api_key
    
    @property
    @abstractmethod
    def model_id(self) -> str:
        """
        Return the model identifier (matches models table in database)
        
        Returns:
            Model ID string (e.g., 'gpt-4o', 'claude-3-7-sonnet')
        """
        pass
    
    @property
    @abstractmethod
    def model_name(self) -> str:
        """
        Return the human-readable model name
        
        Returns:
            Model name string (e.g., 'GPT-4o', 'Claude 3.7 Sonnet')
        """
        pass
    
    @abstractmethod
    def query(self, prompt: str) -> Dict:
        """
        Execute a query and return the response with metadata
        
        Args:
            prompt: The query text to send to the model
            
        Returns:
            Dictionary with keys:
                - response_text: str - The model's response
                - response_time_ms: int - Time taken in milliseconds
                - raw_response: any - Raw API response object
        """
        pass
    
    @abstractmethod
    def extract_metadata(self, response: Dict) -> Tuple[str, List[str]]:
        """
        Extract search query and cited URLs from the response
        
        Args:
            response: Response dictionary from query() method
            
        Returns:
            Tuple of (search_query, cited_urls)
                - search_query: str or None - The search query used by the model
                - cited_urls: List[str] - List of URLs cited in the response
        """
        pass
    
    def _time_query(self, query_func):
        """
        Helper method to time a query execution
        
        Args:
            query_func: Function to execute and time
            
        Returns:
            Tuple of (result, elapsed_ms)
        """
        start_time = time.time()
        result = query_func()
        elapsed_ms = int((time.time() - start_time) * 1000)
        return result, elapsed_ms

