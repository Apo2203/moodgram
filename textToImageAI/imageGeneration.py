#!/usr/bin/env python3
import openai, sys, os, time
API_KEY = "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"
openai.api_key = API_KEY
inputText = str(sys.argv[1]) #text I'll use to generate the image
if(len(sys.argv) != 3):
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

f = open("/var/www/html/moodgram/textToImageAI/tmp/" + str(sys.argv[2]), "w+")
f.write(image_url)
f.close()

#I wait until the php script finish to work on this temporary file and I delete it
time.sleep(10)
os.remove("/var/www/html/moodgram/textToImageAI/tmp/" + str(sys.argv[2]))