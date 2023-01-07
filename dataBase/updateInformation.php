<?php
/* Handling User's information updating */

session_start(); 

//Variable that make me know if the user would like to update just name and username or also the password
$updateBoth = FALSE; 

if (empty($_POST["name"])) {
    die ("Name is required, please write your name in the correct form");
}
if (empty($_POST["surname"])) {
    die ("Surname is required, please write your surname in the correct form");
}
if (! empty($_POST["password"])){
    $updateBoth = TRUE;
    if (empty($_POST["passwordConfirm"])){
        die("Please provide the password confirmation");
    }
    if (strlen($_POST["password"]) < 8) {
        die("Password must be at least 8 characters");
    }
    if ( ! preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $_POST["password"])){
        die("Your Password should contain at least 8 characters, 1 capital letter and 1 number");
    }
    if ($_POST["password"] !== $_POST["passwordConfirm"]){
        die("The two passwords must be the same");
    }
    $hashedPassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
}

$mysqli = require __DIR__ . "/database.php";

if($updateBoth){
    $sql = " UPDATE user SET name= ?, surname= ?, password_hash = ? WHERE id = ? ";
    $stmt = $mysqli->stmt_init();
    
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }
    
    $stmt->bind_param("sssi",
                    $_POST["name"],
                    $_POST["surname"],
                    $hashedPassword,
                    $_SESSION["user_id"]);
                      
    if ($stmt->execute()) {
        session_destroy();
        header("Location: ../WebPages/userInformationUpdated.html");
        exit;
    
    } else{
        die($mysqli->error . " " . $mysqli->error);
    }
    
}
else{
    $sql = " UPDATE user SET name= ?, surname= ? WHERE id = ? ";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }
    
    $stmt->bind_param("ssi",
                    $_POST["name"],
                    $_POST["surname"],
                    $_SESSION["user_id"]);
                      
    if ($stmt->execute()) {
        session_destroy();
        header("Location: ../WebPages/userInformationUpdated.html");
        exit;
    
    } else{
        die($mysqli->error . " " . $mysqli->error);
    }
}
?> 