<?php

$command = "python3 /var/www/html/moodgram/textToImageAI/imageGeneration.py "; //command to exec the python code
$inputText = $_POST["inputText"]; 
$inputText = "'$inputText'"; // quotes needed to send more than one word in a string

$command = ($command.$inputText." ");
$uniqID = uniqid();
$command = ($command.$uniqID)." &";

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