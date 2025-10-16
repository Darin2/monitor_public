import os
from openai import OpenAI
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

# Initialize the OpenAI client with API key from environment variable
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

# Test query
query = "Find paintball events in Texas in 2025. List any websites you referenced when searching. Format your response as JSON"

print(f"Sending query: {query}\n")

# Make API call with web search enabled
response = client.responses.create(
    model="gpt-4o",
    tools=[
        {"type": "web_search"}
    ],
    input=query
)

# Print the response
print("Response:")
print(response.output_text)

