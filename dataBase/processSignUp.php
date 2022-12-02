<?php
if (empty($_POST["name"])) {
    die ("Name is required");
}
if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die ("Email is not valid");
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

$mysqli = require __DIR__ . "/database.php";

$sql = "INSERT INTO user (name, surname, email, password_hash, age)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssssi",
                  $_POST["name"],
                  $_POST["surname"],
                  $_POST["email"],
                  $hashedPassword,
                  $_POST["age"]);
                  
if ($stmt->execute()) {
    header("Location: ../WebPages/signupSuccesfully.html");
    exit;

} else{
    die($mysqli->error . " " . $mysqli->error);
}
?> 