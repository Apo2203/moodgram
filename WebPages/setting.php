<?php
/* Setting page: here the user is allowed to
    - Change profile picture image
    - Change Name / Surname 
    - Change Password
    - Delete the account on MoodGram
*/
    $mysqli = require __DIR__ . "/../dataBase/database.php";
    session_start();

    // Some check to avoid some security issues
    if (! isset($_SESSION["user_id"])) header("Location: index.php");    
    if (substr($_SERVER['REQUEST_URI'], -1) == '/') header ("Location: ".substr($_SERVER['REQUEST_URI'], 0, -1)."");
        
    /* Loading user information */
    $sql = "SELECT u1.profilePicture AS proPic1, u1.name AS userName, u1.surname AS userSurname, u1.email as email
            FROM user u1
            WHERE u1.id = ?";
    $stmt = $mysqli->stmt_init();
    if (! $stmt->prepare($sql)) {
        die("SQL error: " . $mysqli->error);
    }    
    $stmt->bind_param("i",
    $_SESSION["user_id"]);
    $stmt->execute();
    
    /* bind variables to prepared statement */
    $stmt->bind_result($proPic1, $userName, $userSurname, $email);
    $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title> MoodGram </title>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=2">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css?h=025df1ec88740cad5ff14bb3380da6dd">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aboreto&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css?h=5aae26cfc631423a28ee9f3eea8618b0">
    <link rel="stylesheet" href="../assets/css/animate.min.css?h=5512c3e92e3931978c9ddcc7dbeed22b">
    <link rel="stylesheet" href="../assets/css/navbarStyle.css?h=befd8a398792e305b7ffd4a176b5b585">
    <link rel="stylesheet" href="../assets/css/searchBarAnimation.css?h=705ee09c845e6a0566bbf75e428a898f">
    <link rel="stylesheet" href="../assets/css/styles.css?h=398dee27db98ce7d017f9be833b45a5a">
</head>

<body style="background: #313596;">
    <nav class="navbar navbar-dark navbar-expand-md bg-dark py-3" style="--bs-success: #ffffff;--bs-success-rgb: 255,255,255;">
        <div class="container"><a class="navbar-brand d-flex align-items-center" href="home.php" style="font-family: Aboreto, serif;"><span class="fs-2">MoodGram</span></a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-5"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-5" style="padding-left: 0;width: 300px;">
                <div class="d-inline-flex justify-content-center">
                    <div class="searchbar">
                        <form class="d-sm-flex d-xxl-flex justify-content-sm-center align-items-sm-center justify-content-xxl-center align-items-xxl-center" method="get" action="userList.php"><input type="text" class="search_input" placeholder="Search user..." name="searchUser">
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
            <?php echo(' <p class="lead font-monospace fs-3 fw-semibold text-center text-info" style="margin-bottom: 3rem;margin-top: 3rem;color: rgb(255,255,255);"><span style="color: rgb(255, 255, 255);">'.$userName.'\'s Setting</span></p> ') ?>
            <div class="row d-lg-flex justify-content-lg-center align-items-lg-center" style="margin-top: 3rem;margin-bottom: 3rem;margin-right: 0;margin-left: 0;">
                <div class="col-3 col-style-sx" data-bss-hover-animate="pulse"><a href="" data-bs-target="#modal-2" data-bs-toggle="modal">
                    <div class="d-flex d-lg-flex justify-content-center align-items-center justify-content-lg-center align-items-lg-center justify-content-xxl-center align-items-xxl-center overpicture-trigger"><i class="fas fa-images" style="font-size: 4rem;color: var(--bs-primary);"></i></div>
                    <?php echo(' </a><img class="image-style" alt="profile picture" src="../assets/img/profilePictureImage/'.$proPic1.'"></div> ') ?>
            </div>
            <div class="col-lg-12 d-lg-flex justify-content-lg-center">
                <div class="card shadow mb-3">
                    <div class="card-header py-3">
                        <p class="text-primary m-0 fw-bold">User Settings</p>
                    </div>
                    <div class="card-body">
                        <form action="../dataBase/updateInformation.php" method="post"> 
                            <div class="row">
                                <div class="col">
                                <?php echo(' <div class="form-group mb-3"><label class="form-label" for="email"><strong>Email Address</strong></label><input class="form-control" type="email" placeholder="'.$email.'" name="email" readonly=""></div>') ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                <?php echo(' <div class="form-group mb-3"><label class="form-label" for="first_name"><strong>First Name</strong></label><input class="form-control" type="text" placeholder="'.$userName.'" name="name"></div> ') ?>
                                </div>
                                <div class="col">
                                <?php echo(' <div class="form-group mb-3"><label class="form-label" for="last_name"><strong>Last Name</strong></label><input class="form-control" type="text" placeholder="'.$userSurname.'" name="surname"></div> ') ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group mb-3"><label class="form-label" for="first_name"><strong>New Password</strong><br></label><input class="form-control" type="password"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" name="password"></div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-3"><label class="form-label" for="last_name"><strong>Confirm New Password</strong><br></label><input class="form-control" type="password"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" name="passwordConfirm"></div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <div class="row">
                                    <div class="col"><button class="btn btn-primary btn-sm" type="submit">Save Settings</button></div>
                                    <div class="col"><button class="btn btn-danger btn-sm" type="button" data-bs-target="#modal-1" data-bs-toggle="modal">DELETE ACCOUNT</button></div>
                                </div>
                            </div>
                        </form>
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
    <div class="modal fade" role="dialog" tabindex="-1" id="modal-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">DO YOU WANT TO DELETE YOUR ACCOUNT?</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Be careful! You can't go back after your confirmation!</p>
                </div>
                <?php
                    echo('
                        <div class="modal-footer"><button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                        <a href="accountDelete.php?id='.$_SESSION["user_id"].'"> <button class="btn btn-danger" type="button">Yes I would like to delete my account</button></a></div>
                    ')
                ?>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" tabindex="-1" id="modal-2">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose your new profile picture and confirm.</h4><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../dataBase/loadNewImage.php" method="post" enctype="multipart/form-data">
                        <input class="form-control" type="file" name="newImage" accept="image/*" required="">
                        <button class="btn btn-primary float-end" type="submit" name="submit" value="upload" style="margin-top: 12px;">Save</button>
                    </form>
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" style="margin-top: 12px;">Close</button>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js?h=981245863c383366a329259d02b8172c"></script>
    <script src="../assets/js/bs-init.js?h=67ee20cf4e5150919853fca3720bbf0d"></script>
    <script src="../assets/js/Material-Text-Input.js?h=713af0c6ce93dbbce2f00bf0a98d0541"></script>
</body>

</html>