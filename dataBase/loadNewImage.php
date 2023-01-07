<?php
/* Handling new profile pictures */
$mysqli = require __DIR__ . "/database.php";
session_start();

if (isset($_POST["submit"]) && isset($_FILES["newImage"])){
    $img_name = $_FILES["newImage"]["name"];
    $img_size = $_FILES["newImage"]["size"];
    $tmp_name = $_FILES["newImage"]["tmp_name"];
    $error = $_FILES["newImage"]["error"];

    if ($error == 0){
        if($img_size > 925000){
            echo ("Sorry, your file is too big");
        }
        else{
            // Handling image extension
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array ("jpg", "jpeg", "png");
            if(in_array($img_ex_lc, $allowed_exs)){
                // Saving the new image on the server creating a new unique ID
                $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                $img_upload_path = "../assets/img/profilePictureImage/".$new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);

                // Update the Database with the new profile picture
                $sql = "UPDATE user SET profilePicture = ? WHERE id = ?";
                $stmt = $mysqli->stmt_init();
                if (! $stmt->prepare($sql)) {
                die("SQL error: " . $mysqli->error);
                }
                $stmt->bind_param("si",
                    $new_img_name,
                    $_SESSION["user_id"]
                );
                $stmt->execute();
                header("Location: ../WebPages/setting.php");
            }
            else{
                echo "Type of file not allowed";
            }
        }
    }
    else{
        echo "unknown error occurred!";
    }

}
else{
    header("Location: ../WebPages/setting.php");
}
?>