<?php

$command = "python3 /var/www/html/moodgram/textToImageAI/test.py "; //command to exec the python code
$inputText = "'zio mandarancio'";//$_POST["inputText"]; // double quotes needed to send a string with more than one word
$command = ($command.$inputText." ");
$uniqID = uniqid();
$command = ($command.$uniqID);


$output = shell_exec($command);
$file = "/var/www/html/moodgram/textToImageAI/tmp/".$uniqID;
$fh = fopen($file,'r');
$line = fgets($fh);
echo($line);
//print($output); 
//$output = "https://oaidalleapiprodscus.blob.core.windows.net/private/org-klFBBL3E2HaU7Q7eQHSSxuik/user-GsPpXf2q2Aq4B0tOcJdV6s0w/img-fRvY7Zl2mtwJ6KxdVrIUwZVE.png?st=2023-01-03T09%3A40%3A37Z&se=2023-01-03T11%3A40%3A37Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-01-03T10%3A04%3A41Z&ske=2023-01-04T10%3A04%3A41Z&sks=b&skv=2021-08-06&sig=Hzmv%2B3UM5wIaI0hJua1v%2BQQNbVLXP5Q62dzP0LhYz5M%3D";
// Remote image URL
//$url = $output;

// Image path
//$new_img_name = uniqid("GEN-IMG-", true);
//$img = '../assets/img/generatedImage/'.$new_img_name.'.jpg';

// Save image 
//file_put_contents($img, file_get_contents($url));

?>