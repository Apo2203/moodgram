#!/usr/bin/env python3
import openai, sys, os, time
API_KEY = "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"
openai.api_key = API_KEY
inputText = str(sys.argv[1]) #text I'll use to generate the image
if(len(sys.argv) != 3):
    print("Error getting the input for image generation: check parameter")
    sys.exit()
f = open("/var/www/html/moodgram/textToImageAI/tmp/" + str(sys.argv[2]), "w+")
