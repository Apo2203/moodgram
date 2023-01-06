<?php

$mysqli = require __DIR__ . "/../dataBase/database.php";
session_start();
date_default_timezone_set('Africa/Nairobi');
$date = date('Y-m-d', time());

$inputText = $_POST["inputText"]; 
$curlquery = " https://api.openai.com/v1/images/generations \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer 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' \
  -d '{
    \"prompt\": \"$inputText\",
    \"n\": 1,
    \"size\": \"512x512\"
  }' ";


$result = "https://oaidalleapiprodscus.blob.core.windows.net/private/org-klFBBL3E2HaU7Q7eQHSSxuik/user-GsPpXf2q2Aq4B0tOcJdV6s0w/img-cfdxnLJfu7jJ6OMjIOloDKBL.png?st=2023-01-06T09%3A56%3A57Z&se=2023-01-06T11%3A56%3A57Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-01-06T10%3A34%3A05Z&ske=2023-01-07T10%3A34%3A05Z&sks=b&skv=2021-08-06&sig=nc7hbYQoTB1NrMQldqBkbHz6K/lXc0TqEGfqALtxn0Q%3D";//(shell_exec("curl".$curlquery));
// Regex to get just the image url from the curl answer
preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $result, $match);
$imgurl = ($match[0][0]); 


// Image path
$new_img_name = uniqid("GEN-IMG-");
$img = '/var/www/html/moodgram/assets/img/generatedImage/'.$new_img_name.'.jpg';

// Save image 
file_put_contents($img, file_get_contents($imgurl));


// Save the image on the database
$sql = "SELECT * FROM post WHERE ref_user1 = ? OR ref_user2 = ? AND date = ?";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("iis",
$_SESSION["user_id"],
$_SESSION["user_id"],
$date
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

    $stmt->bind_param("iiss",
    $_SESSION["user_id"],
    $partnerId,
    $new_img_name,
    $date
    );
    $stmt->execute();
}
header("Location: ../WebPages/home.php");
?>