<?php
    $mysqli = require __DIR__ . "/../dataBase/database.php";
    session_start();
    if (! isset($_SESSION["user_id"])) header("Location: index.php");    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>dashboardpost</title>
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
                        <form class="d-sm-flex d-xxl-flex justify-content-sm-center align-items-sm-center justify-content-xxl-center align-items-xxl-center" method="post" action="userList.php">
                            <input type="text" class="search_input" placeholder="Search user...">
                            <button class="btn search-icon" type="submit"><i class="fas fa-search" style="padding: 0px;margin: 0px;color: rgb(255,255,255);"></i></button></form>
                    </div>
                </div>
                <ul class="navbar-nav ms-auto" style="height: 8px;"></ul><a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="Profile.php?id_user=<?php echo($_SESSION["user_id"]) ?>" style="margin: 10px;padding: 8px 14px;">MyProfile</a><a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="setting.php" style="background: #003893;border-color: #003893;margin: 10px;padding: 8px 14px;">Setting</a><a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="logout.php" style="background: var(--bs-gray-700);border-color: var(--bs-gray-700);margin: 10px;padding: 8px 14px;">Logout</a>
            </div>
        </div>
    </nav>
    
    </div>
    <p class="lead font-monospace fs-3 fw-semibold text-center text-info" style="margin-top: 10rem;"><span style="color: rgb(255, 255, 255);"> Error: user does not exist! </span></p>

    </section>
    <script src="../assets/bootstrap/js/bootstrap.min.js?h=981245863c383366a329259d02b8172c"></script>
    <script src="../assets/js/aos.min.js?h=d3718e34eeb0355be8e3179a2e2bccb7"></script>
    <script src="../assets/js/bs-init.js?h=67ee20cf4e5150919853fca3720bbf0d"></script>
    <script src="../assets/js/Material-Text-Input.js?h=713af0c6ce93dbbce2f00bf0a98d0541"></script>
</body>

</html>