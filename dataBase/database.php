<?php
$host = "localhost";
$dbname = "moodGramDB";
$username = "root";
$password = "Anker";

$mysqli = new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);
if ($mysqli->connect_errno){
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>