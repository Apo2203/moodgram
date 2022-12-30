<?php
$mysqli = require __DIR__ . "/../dataBase/database.php";
session_start();
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    if($_SESSION["user_id"] == $id){
        $deleteQuery = "DELETE FROM `user` WHERE `id`='$id'";
        $stmt = $mysqli->stmt_init();
        if (! $stmt->prepare($deleteQuery)) {
            die("SQL error: " . $mysqli->error);
        }   
        $stmt->execute();
    }
}

session_destroy(); // it's here just to avoid security problem 
header("Location: index.php");
exit;
?>