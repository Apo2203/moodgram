#!/usr/bin/env python3
import time, sys

if(len(sys.argv) != 3):  
    print("check parameters")
    sys.exit()

print("ho preso in input ", str(sys.argv[1]))
image_url = "sauhdasuihasuihsauishauisahsuhduashsauihsauhsaiah/hduiaadlmsalkmdas.png"

f = open("/var/www/html/moodgram/textToImageAI/tmp/" + str(sys.argv[2]), "w+")
f.write(image_url)
f.close()
