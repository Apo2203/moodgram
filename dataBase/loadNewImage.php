<?php

if (isset($_POST["submit"]) && isset($_FILES["newImage"])){
    
    $img_name = $_FILES["newImage"]["name"];
    $img_size = $_FILES["newImage"]["size"];
    $tmp_name = $_FILES["newImage"]["tmp_name"];
    $error = $_FILES["newImage"]["error"];

    if ($error == 0){
        if($img_size > 225000){
            $em = "Sorry, your file is too big";
            header("Location: ../WebPages/setting.php?error=$em");
        }
        else{
            //image extension
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);

            $img_ex_lc = strtolower($img_ex);

            $allowed_exs = array ("jpg", "jpeg", "png");
            if(in_array($img_ex_lc, $allowed_exs)){
                $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                $img_upload_path = "../assets/img/profilePictureImage/".$new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);
            }
            else{
                $em = "Type of file not allowed";
                header("Location: ../WebPages/setting.php?error=$em");
            }
        }
    }
    else{
        $em = "unknown error occurred!";
        header("Location: ../WebPages/setting.php?error=$em");
    }

}

else{
    header("Location: ../WebPages/setting.php");
}
?>