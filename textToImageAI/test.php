<?php

$command = "python3 /var/www/html/moodgram/textToImageAI/imageGeneration.py "; //command to exec the python code
$inputText = "'mouse dancing'";//$_POST["inputText"]; // double quotes needed to send a string with more than one word
$command = ($command.$inputText." ");
$uniqID = uniqid();
$command = ($command.$uniqID)." &";

echo($command);

shell_exec($command);
sleep(10);
$file = "/var/www/html/moodgram/textToImageAI/tmp/".$uniqID;
$fh = fopen($file,'r');
$imgurl = fgets($fh);

// Image path
$new_img_name = uniqid("GEN-IMG-");
$img = '/var/www/html/moodgram/assets/img/generatedImage/'.$new_img_name.'.jpg';

// Save image 
file_put_contents($img, file_get_contents($imgurl));

?>