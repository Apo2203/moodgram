<?php
/* Profile page: here the user can
    - See information about a registered profile (followers, partner, post)
    - Follow / Unfollow
    - Ask for a relationship if allowed (not already in a relationship)
    - Ban the user IF ADMIN
*/
    $mysqli = require __DIR__ . "/../dataBase/database.php";
    session_start();

    $isMyPage = FALSE; // The page showed is about my profile?
    $currentUserRelationship = FALSE; // The user showed in the page is in a relationship?
    $ImInRelationship = FALSE;  // I am in a relationship?
    $isAdmin = FALSE;  // Am I an admin?

    // Some check to avoid some security issues
    if (! isset($_SESSION["user_id"])) header("Location: index.php");    
    if (substr($_SERVER['REQUEST_URI'], -1) == '/') header ("Location: ".substr($_SERVER['REQUEST_URI'], 0, -1)."");

    if (isset ($_GET['id_user'])){
    $currentIdUserPage = $_GET['id_user'];

    //Check if the ID (this user) exist
    $idExist = "SELECT `id` FROM user WHERE id = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($idExist)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt->bind_param("i", $currentIdUserPage);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows != 1){
        header("Location: ./userNotExist.php");
    }
    if($_GET['id_user'] == $_SESSION["user_id"]){
        $isMyPage = TRUE;
    }

    //Check the number of follower
    $getFollower = "SELECT followers FROM user WHERE id = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($getFollower)) {
        die("SQL error: " . $mysqli->error);
    }    
    
    $stmt->bind_param("i", $currentIdUserPage);
    $stmt->execute();
    $array = [];
    foreach ($stmt->get_result() as $row){
        $array[] = $row['followers'];
    }
    $actualFollower = $array[0];
}

//Check if the user we are looking is already in a relationship
$sql = "SELECT rel.ref_user_1 as id1, rel.ref_user_2 as id2 FROM relationship rel WHERE (ref_user_1 = ? OR ref_user_2 = ?) AND confirmed = 1";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("ii",
$currentIdUserPage,
$currentIdUserPage,
);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
    // The user is already in a relationship
    $currentUserRelationship = TRUE;
    // Check for the ID and then basic information about the partner of this user
    $stmt->bind_result($id1, $id2);
    $stmt->fetch();
    if ($id1 == $currentIdUserPage) $partnerId = $id2;
    else                            $partnerId = $id1;

    $getPartnerInfo = "SELECT name, surname, profilePicture FROM user WHERE id = ?";
    $stmt2 = $mysqli->stmt_init();
    if (! $stmt2->prepare($getPartnerInfo)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt2->bind_param("i", $partnerId);
    $stmt2->execute();
    $stmt2->store_result();
    $stmt2->bind_result($partnerName, $partnerSurname, $partnerProPic);
    $stmt2->fetch();
} 

//Check if the actual user (Me) is already in a relationship
$sql = "SELECT * FROM relationship WHERE (ref_user_1 = ? OR ref_user_2 = ?) AND confirmed = 1";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("ii",
$_SESSION["user_id"],
$_SESSION["user_id"],
);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
    $ImInRelationship = TRUE;
} 

//Ask for a relationship
$checkRelationship = "SELECT * FROM relationship WHERE (ref_user_1 = ? AND ref_user_2 = ?) OR (ref_user_2 = ? AND ref_user_1 = ?)";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($checkRelationship)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("iiii",
$_SESSION["user_id"],
$_GET["id_user"],
$_SESSION["user_id"],
$_GET["id_user"]
);
$stmt->execute();
$stmt->store_result();
$numRelationship = $stmt->num_rows;

if($numRelationship == 0){
    if(isset($_GET['askRelationship']) && $_GET['askRelationship'] == "TRUE"){
        $sql = "INSERT INTO `relationship` (`ref_user_1`, `ref_user_2`, `confirmed`) VALUES (?, ?, '0');";
        $stmt = $mysqli->stmt_init();

        if (! $stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }    
        $stmt->bind_param("ii",
        $_SESSION["user_id"],
        $_GET["id_user"]
        );
        $stmt->execute();
        header("Location: ./Profile.php?id_user=$currentIdUserPage");
    }
}

// Follow someone
$checkFriendship = "SELECT * FROM friendship WHERE ref_user_1 = ? AND ref_user_2 = ?";
$alreadyFollow = FALSE;
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($checkFriendship)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("ii",
$_SESSION["user_id"],
$_GET["id_user"]);
$stmt->execute();
$stmt->store_result();
$numFollow = $stmt->num_rows;

if($numFollow == 0){
    if(isset($_GET["follow"]) && $_GET["follow"] == 'TRUE'){
        $follow = "INSERT INTO friendship VALUES (?, ?)";
        $stmt = $mysqli->stmt_init();
        if (! $stmt->prepare($follow)) {
            die("SQL error: " . $mysqli->error);
        }    
        $stmt->bind_param("ii",
        $_SESSION["user_id"],
        $_GET["id_user"]);
        $stmt->execute();

        //Update followers on database
        $getFollower = "UPDATE user SET followers = ? WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if (! $stmt->prepare($getFollower)) {
            die("SQL error: " . $mysqli->error);
        }    
        $actualFollower = ($actualFollower + 1);
        $stmt->bind_param("ii",
        ($actualFollower),
        $currentIdUserPage);
        $stmt->execute();
        header("Location: ./Profile.php?id_user=$currentIdUserPage");
    }
}else{
    $alreadyFollow = TRUE;
}

//Unfollow someone
if(isset($_GET["follow"]) && $_GET["follow"] == 'FALSE'){
    $removeFollow = "DELETE FROM friendship WHERE ref_user_1 = ? AND ref_user_2 = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($removeFollow)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt->bind_param("ii",
    $_SESSION["user_id"],
    $_GET["id_user"]);
    $stmt->execute();

    //Update followers on database
    $getFollower = "UPDATE user SET followers = ? WHERE id = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($getFollower)) {
        die("SQL error: " . $mysqli->error);
    }    
    $actualFollower = ($actualFollower - 1);
    $stmt->bind_param("ii",
    ($actualFollower),
    $currentIdUserPage);
    $stmt->execute();
    
    header("Location: ./Profile.php?id_user=$currentIdUserPage");
}


// Check if the current user is an administrator
$sql = "SELECT * FROM user WHERE id = ? AND role = 'admin'";
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 1) $isAdmin = TRUE;

// Ban an account
if(isset($_GET["banUser"])){
    if($isAdmin == TRUE && (! $_GET["banUser"] == $_SESSION["user_id"])){
        $sql = "DELETE FROM `user` WHERE id = ?";
        $stmt = $mysqli->stmt_init();
        if (! $stmt->prepare($sql)) {
            die("SQL error: " . $mysqli->error);
        }    
        $stmt->bind_param("i", $_GET["banUser"]);
        $stmt->execute();
        header("Location: ./home.php");
    }else{
        session_destroy();
        header("Location: ./index.php");
    }
}
$sql = "SELECT u1.profilePicture AS proPic1, u1.name AS userName, u1.surname AS userSurname, u1.followers AS followers
        FROM user u1
        WHERE u1.id = ?";
        
$stmt = $mysqli->stmt_init();
if (! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}    
$stmt->bind_param("i",
$_GET["id_user"]);
$stmt->execute();
/* bind variables to prepared statement */
$stmt->bind_result($proPic1, $userName, $userSurname, $followers);
$stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title> Moodgram </title>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=2">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css?h=025df1ec88740cad5ff14bb3380da6dd">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aboreto&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css?h=5aae26cfc631423a28ee9f3eea8618b0">
    <link rel="stylesheet" href="../assets/css/aos.min.css?h=ea67d3638a91cd05d22f1b4e31c7b746">
    <link rel="stylesheet" href="../assets/css/animate.min.css?h=5512c3e92e3931978c9ddcc7dbeed22b">
    <link rel="stylesheet" href="../assets/css/navbarStyle.css?h=befd8a398792e305b7ffd4a176b5b585">
    <link rel="stylesheet" href="../assets/css/searchBarAnimation.css?h=705ee09c845e6a0566bbf75e428a898f">
    <link rel="stylesheet" href="../assets/css/styles.css?h=398dee27db98ce7d017f9be833b45a5a">
</head>

<body style="background: #025891;">
    <nav class="navbar navbar-dark navbar-expand-md bg-dark py-3" style="--bs-success: #ffffff;--bs-success-rgb: 255,255,255;">
        <div class="container"><a class="navbar-brand d-flex align-items-center" href="home.php" style="font-family: Aboreto, serif;"><span class="fs-2">MoodGram</span></a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-5"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-5" style="padding-left: 0;width: 300px;">
                <div class="d-inline-flex justify-content-center">
                    <div class="searchbar">
                        <form class="d-sm-flex d-xxl-flex justify-content-sm-center align-items-sm-center justify-content-xxl-center align-items-xxl-center" method="get" action="userList.php">
                            <input type="text" class="search_input" placeholder="Search user..." name="searchUser">
                            <button class="btn search-icon" type="submit"><i class="fas fa-search" style="padding: 0px;margin: 0px;color: rgb(255,255,255);"></i></button></form>
                    </div>
                </div>
                <ul class="navbar-nav ms-auto" style="height: 8px;"></ul>
                <a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="Profile.php?id_user=<?php echo($_SESSION["user_id"]) ?>" style="margin: 10px;padding: 8px 14px;">MyProfile</a>
                <a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="setting.php" style="background: #003893;border-color: #003893;margin: 10px;padding: 8px 14px;">Setting</a>
                <a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="logout.php" style="background: var(--bs-gray-700);border-color: var(--bs-gray-700);margin: 10px;padding: 8px 14px;">Logout</a>
            </div>
        </div>
    </nav>
    <section>

        
        <div style="width: 98%;padding-bottom: 6rem;margin-bottom: 6rem;background: rgba(49,53,150,0);">
            <div class="row g-0 text-center" style="margin-top: 3rem;margin-right: 0;margin-left: 0;">
            <?php echo(' <div class="col-12 col-style-sx"><img class="image-style profile-picture" alt="profile picture" src="../assets/img/profilePictureImage/'.$proPic1.'" style="width: 30%;"></div> ')?>
            </div>
            <div class="row d-flex d-sm-flex d-md-flex d-lg-flex d-xl-flex d-xxl-flex justify-content-center align-items-center justify-content-sm-center align-items-sm-center justify-content-md-center align-items-md-center justify-content-lg-center align-items-lg-center justify-content-xl-center align-items-xl-center justify-content-xxl-center align-items-xxl-center" style="margin-left: 15%;margin-right: 15%;">
                <div class="col-12 col-xxl-12 text-center myProfileInformationXS" style="background: #4e95ce;border-radius: 3rem;box-shadow: 0px 0px 9px 0px;margin: 1rem;padding-top: 7px;">
                    <?php echo(' <p class="fs-2 fw-normal" style="position: relative;display: inline;font-family: Poppins, sans-serif;"><span style="color: rgb(255, 255, 255);">'.$userName.' '.$userSurname.'&nbsp;</span><br></p> ') ?>
                    <?php
                        if ($currentUserRelationship == TRUE){
                            echo('
                                <p style="font-family: Poppins, sans-serif;font-size: 20px;">
                                <span style="color: rgb(255, 255, 255);">In a relationship with </span>
                                <a href="Profile.php?id_user='.$partnerId.'"><img src="../assets/img/profilePictureImage/'.$partnerProPic.'"  alt="profile picture" style="width: 3rem;border-radius: 3rem;"></a>
                                <strong>
                                    <span style="color: rgb(255, 255, 255);">&nbsp;</span>
                                </strong>
                                <span style="color: rgb(255, 255, 255);">'.$partnerName.' '.$partnerSurname.'</span></p>
                            ');
                        }
                    ?>
                    <?php echo(' <p style="font-size: 20px;"><span style="color: rgb(255, 255, 255);">'.$followers.' follower</span></p>') ?>
                </div>
        </div>

        <?php
            if(!$isMyPage){
                    echo(' <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-4 text-center" style="width: 70%;margin-left: 15%;margin-right: 15%;"> ');
                    if($alreadyFollow == TRUE){
                        echo(' <div class="col d-xl-flex align-items-center justify-content-xl-center"><button class="btn btn-primary btn-sm" data-bss-hover-animate="pulse" type="button" onclick="window.location.href=\'Profile.php?id_user='.$currentIdUserPage.'&follow=FALSE\';" style="background: rgb(176,59,181);border-radius: 1rem;margin: 7px;font-size: 25px;border-color: var(--bs-gray-900);">Unfollow</button></div> ');
                    }else{
                        echo(' <div class="col d-xl-flex align-items-center justify-content-xl-center"><button class="btn btn-primary btn-sm" data-bss-hover-animate="pulse" type="button" onclick="window.location.href=\'Profile.php?id_user='.$currentIdUserPage.'&follow=TRUE\';" style="background: rgb(176,59,181);border-radius: 1rem;margin: 7px;font-size: 25px;border-color: var(--bs-gray-900);">Follow</button></div> ');
                    }
                    if($currentUserRelationship == FALSE && $ImInRelationship == FALSE){
                        echo(' <div class="col d-xl-flex align-items-center justify-content-xl-center"><button class="btn btn-success btn-sm" data-bss-hover-animate="pulse" type="button" onclick="window.location.href=\'Profile.php?id_user='.$currentIdUserPage.'&askRelationship=TRUE\';" style="border-radius: 1rem;margin: 7px;font-size: 25px;border-color: var(--bs-gray-900);">Ask Relation</button></div>');
                    }
                    echo(' <div class="col d-xl-flex align-items-center justify-content-xl-center"><button class="btn btn-warning btn-sm" data-bss-hover-animate="pulse" type="button" style="border-radius: 1rem;margin: 7px;font-size: 25px;border-color: var(--bs-gray-900);">Report</button></div>');
                    if($isAdmin == "TRUE"){
                        echo(' <div class="col d-xl-flex align-items-center justify-content-xl-center"><button class="btn btn-danger btn-sm" data-bss-hover-animate="pulse" type="button" onclick="window.location.href=\'Profile.php?banUser='.$currentIdUserPage.'\'" style="border-radius: 1rem;margin: 7px;font-size: 25px;border-color: var(--bs-gray-900);">Ban</button>');
                    }
                    echo (' </div></div> ');
                    
            }
        ?>
        </div>
        
        <?php
            //Select and show all the post of the user
            $sql = "SELECT p.date, p.id_post, p.ref_user1, p.ref_user2, p.image_ref_user1, p.image_ref_user2, u1.id as id1, u2.id as id2, u1.name AS name1, u1.surname AS surname1, u2.name AS name2, u2.surname AS surname2, u1.profilePicture AS proPic1, u2.profilePicture AS proPic2, 
            (SELECT COUNT(*) FROM vote v1 WHERE (v1.ref_post) = (p.id_post) AND v1.voted_image = 0) AS voteImg1, 
            (SELECT COUNT(*) FROM vote v2 WHERE (v2.ref_post) = (p.id_post) AND v2.voted_image = 1) AS voteImg2
            FROM post p, user u1, user u2
            WHERE p.ref_user1 = u1.id AND p.visibility = 1 AND p.ref_user2 = u2.id AND p.id_post = ANY (SELECT p1.id_post FROM post p1 WHERE ref_user1 = ? OR ref_user2 = ?)
            GROUP BY p.id_post
            ORDER BY date DESC";
            $stmt = $mysqli->stmt_init();
            if (! $stmt->prepare($sql)) {
                die("SQL error: " . $mysqli->error);
            }    
            $stmt->bind_param("ii", $_GET["id_user"], $_GET["id_user"]);
            $stmt->execute();
            $result = $stmt->get_result();

            // If the user have at least one post
            if ($result->num_rows > 0)
                echo(' <p class="lead font-monospace fs-3 fw-semibold text-center text-info" style="margin-bottom: 5rem;"><span style="color: rgb(255, 255, 255);">Here is '.$userName.'\'s post</span></p>');

            while($data = $result->fetch_assoc()){
                echo('
                <div class="container Cardsize" style="margin-bottom: 7rem;">
                    <div class="row" style="margin: 0;box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;margin-bottom: 2rem;padding: 1rem;border-radius: 3rem;background: #E5A904;">
                        <div class="col">
                            <p class="lead fs-2 text-start" style="font-family: Poppins, sans-serif;color: #250001;text-shadow: 0px 0px 0px var(--bs-black);margin-bottom: 0.5rem;">
                                <span style="font-weight: normal !important;">'.$data['name1'].' '.$data['surname1'].' &amp; '.$data['name2'].' '.$data['surname2'].'</span>
                            </p>
                            <a href="Profile.php?id_user='.$data['id1'].'"><img src="../assets/img/profilePictureImage/'.$data['proPic1'].'"  alt="profile picture" style="width: 3rem;border-radius: 3rem;"></a>
                            <p class="fs-6 fw-normal" style="position: relative;display: inline;padding: 0.5em;color: #250001;">
                                <strong>'.$data['voteImg1'].'</strong>
                            </p>
                            <a href="Profile.php?id_user='.$data['id2'].'"><img src="../assets/img/profilePictureImage/'.$data['proPic2'].'"  alt="profile picture" style="width: 3rem;border-radius: 3rem;"></a>
                            <p class="fs-6 fw-normal" style="position: relative;display: inline;padding: 0.5em;color: #250001;">
                                <strong>'.$data['voteImg2'].'</strong>
                            </p>
                            <div>
                                <p class="lead fs-4 fw-light text-start float-start" style="padding-top: 0.3rem;font-family: Abel, sans-serif;color: #250001;">
                                    <em>'.$data['date'].'</em>
                                </p>
                                <div class="dropend float-end">
                                    <button class="btn btn-primary" aria-expanded="false" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bss-hover-animate="pulse" type="button" style="border-radius: 3rem;background: #4f94cf;">
                                        <i class="far fa-sun"></i>
                                    </button>
                                    ');
                                    if ($isAdmin == "TRUE"){
                                        echo('
                                            <div class="dropdown-menu dropdown-menu-dark" style="border-radius: 1rem;">
                                                <a class="dropdown-item" href="#">Report Post</a>
                                                <a class="dropdown-item" href="./home.php?deletePost='.$data["id_post"].'" style="color: rgb(255,0,0);">Delete post</a>
                                            </div>
                                        ');
                                    }
                                    else{
                                        echo('
                                            <div class="dropdown-menu dropdown-menu-dark" style="border-radius: 1rem;">
                                                <a class="dropdown-item" href="#">Report Post</a>
                                            </div>
                                        ');
                                    }
                                    echo('
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin: 0;">
                        <div class="col col-style-sx" data-bss-hover-animate="pulse">
                            <a href="#"> 
                                <div class="d-flex d-lg-flex justify-content-center align-items-center justify-content-lg-center align-items-lg-center justify-content-xxl-center align-items-xxl-center overpicture-trigger">
                                    <i class="fas fa-heart" style="font-size: 4rem;color: var(--bs-red);"></i>
                                </div>
                            </a>
                            <img class="img-fluid image-style"  alt="profile picture" src="../assets/img/generatedImage/'.$data['image_ref_user1'].'">
                        </div>
                        <div class="col col-style-dx" data-bss-hover-animate="pulse">
                            <a href="#">
                                <div class="d-flex d-xxl-flex justify-content-center align-items-center justify-content-xxl-center align-items-xxl-center overpicture-trigger">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </a>
                            <img class="img-fluid image-style"  alt="profile picture" src="../assets/img/generatedImage/'.$data['image_ref_user2'].'">
                        </div>
                    </div>
                </div>
            ');
            }
        ?>
        <div class="container NoMorePostSizeForBigScreen" style="margin-bottom: 7rem;">
            <div class="row" data-aos="zoom-in" data-aos-duration="1000" data-aos-once="true" style="margin: 0;box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;margin-bottom: 2rem;padding: 1rem;border-radius: 3rem;background: #549ad4;">
                <div class="col text-center">
                    <p style="color: rgb(0,0,0);text-align: center;font-size: 2rem;font-family: Aboreto, serif;">It's all, no post to show you</p><img src="../assets/img/allPostSeen.png?h=e404b28546662f09501ec78b4461766c" style="width: 30%;">
                </div>
            </div>
        </div>
        <footer class="text-center py-4">
            <div class="container">
                <div class="row row-cols-1 row-cols-lg-3">
                    <div class="col">
                        <p class="text-muted my-2">Copyright&nbsp;© 2022 Moodgram</p>
                    </div>
                    <div class="col">
                        <ul class="list-inline my-2">
                            <li class="list-inline-item me-4">
                                <div class="bs-icon-circle bs-icon-primary bs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-facebook">
                                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"></path>
                                    </svg></div>
                            </li>
                            <li class="list-inline-item me-4">
                                <div class="bs-icon-circle bs-icon-primary bs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-twitter">
                                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"></path>
                                    </svg></div>
                            </li>
                            <li class="list-inline-item">
                                <div class="bs-icon-circle bs-icon-primary bs-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-instagram">
                                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"></path>
                                    </svg></div>
                            </li>
                        </ul>
                    </div>
                    <div class="col">
                        <ul class="list-inline my-2">
                            <li class="list-inline-item"><a class="link-secondary" href="#">Privacy Policy</a></li>
                            <li class="list-inline-item"><a class="link-secondary" href="#">Terms of Use</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </section>
    <script src="../assets/bootstrap/js/bootstrap.min.js?h=981245863c383366a329259d02b8172c"></script>
    <script src="../assets/js/aos.min.js?h=d3718e34eeb0355be8e3179a2e2bccb7"></script>
    <script src="../assets/js/bs-init.js?h=67ee20cf4e5150919853fca3720bbf0d"></script>
    <script src="../assets/js/Material-Text-Input.js?h=713af0c6ce93dbbce2f00bf0a98d0541"></script>
</body>

</html>