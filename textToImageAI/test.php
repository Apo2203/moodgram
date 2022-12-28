<?php

$command = "python3 /var/www/html/moodgram/textToImageAI/test.py "; //command to exec the python code
$inputText = " 'a girl angry like a tiger' "; // double quotes needed to send a string with more than one word
$command = ($command.$inputText);

$output = shell_exec($command);
print($output); 

?>