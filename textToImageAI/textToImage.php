<?php

$mysqli = require __DIR__ . "/../dataBase/database.php";
session_start();

$command = "/usr/bin/python3 /var/www/html/moodgram/textToImageAI/imageGeneration.py "; //command to exec the python code

$inputText = $_POST["inputText"]; 
$inputText = "'$inputText'"; // quotes needed to send more than one word in a string

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

// Save the image on the database
$sql = "SELECT * FROM post WHERE ref_user1 = ? OR ref_user2 = ?";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("ii",
$_SESSION["user_id"],
$_SESSION["user_id"],
);

$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if($stmt->num_rows > 0){
    // The partner already posted an image
    $sql = "UPDATE `post` SET `image_ref_user2` = ?, `visibility` = '1' WHERE `post`.`id_post` = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt->bind_param("ii",
    $new_img_name,
    $data["id_post"],
    );
    $stmt->execute();
}
else{
    // I'm the first partner to post the image

    // Find my partner's ID
    $sql = "SELECT * FROM relationship WHERE ref_user_1 = ? OR ref_user_2 = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt->bind_param("ii",
    $_SESSION["user_id"],
    $_SESSION["user_id"],
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if($data["ref_user_1"] == $_SESSION["user_id"]) $partnerId = $data["ref_user_2"];
    else $partnerId = $data["ref_user_1"];
    
    // Creating new post since my partner didn't do it before
    $sql = "INSERT INTO `post` (`ref_user1`, `ref_user2`, `image_ref_user1`, `visibility`, `date`) 
    VALUES (?, ?, ?, '0', ?)";

    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }    
    date_default_timezone_set('Africa/Nairobi');
    $date = date('Y-m-d', time());

    $stmt->bind_param("iiss",
    $_SESSION["user_id"],
    $partnerId,
    $new_img_name,
    $date
    );
    $stmt->execute();
}

?>