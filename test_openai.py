import os
from openai import OpenAI
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

# Initialize the OpenAI client with API key from environment variable
client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

# Test query
query = "Find paintball events in Texas in 2025"

print(f"Sending query: {query}\n")

# Make API call
response = client.chat.completions.create(
    model="gpt-4",  # Change to "gpt-3.5-turbo" if you don't have GPT-4 access
    messages=[
        {"role": "user", "content": query}
    ]
)

# Print the response
print("Response:")
print(response.choices[0].message.content)

