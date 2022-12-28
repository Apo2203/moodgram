#!/usr/bin/env python3
import openai, sys
API_KEY = "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"
openai.api_key = API_KEY
inputText = str(sys.argv[1]) #text I'll use to generate the image

if(len(sys.argv) != 2):
    print("Error getting the input for image generation: check parameter")
    sys.exit()

try:
    response = openai.Image.create(
    prompt=inputText,
    n=1,
    size="512x512"
    )
    image_url = response['data'][0]['url']
except openai.error.OpenAIError as e:
    print(e.http_status)
    print(e.error)

print(image_url)

# to run this code from a php file look the example in "test.php"