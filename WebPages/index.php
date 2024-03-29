<?php
/* Login page: the first page */

$is_invalid = false; // value useful to know if the login information are valid or no
if ($_SERVER["REQUEST_METHOD"] === "POST"){
    $mysqli = require __DIR__ . "/../dataBase/database.php";
    $sql = sprintf("SELECT * FROM user WHERE email = '%s'", $mysqli->real_escape_string($_POST["email"]));
    $result = $mysqli->query($sql);
    $user = $result->fetch_assoc();
    if ($user) {
        if (password_verify($_POST["password"], $user["password_hash"])){
            session_start();
            $_SESSION["user_id"] = $user["id"];
            header("Location: home.php");
            exit;       
        }
    }
    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <title> Welcome to MoodGram </title>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=2">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>dashboardpost</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css?h=025df1ec88740cad5ff14bb3380da6dd">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aboreto&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins&amp;display=swap">
    <link rel="stylesheet" href="../assets/css/animate.min.css?h=5512c3e92e3931978c9ddcc7dbeed22b">
    <link rel="stylesheet" href="../assets/css/navbarStyle.css?h=befd8a398792e305b7ffd4a176b5b585">
    <link rel="stylesheet" href="../assets/css/searchBarAnimation.css?h=705ee09c845e6a0566bbf75e428a898f">
    <link rel="stylesheet" href="../assets/css/styles.css?h=398dee27db98ce7d017f9be833b45a5a">
</head>

<body id="hero">
    <nav class="navbar navbar-dark navbar-expand-md bg-dark py-3">
        <div class="container"><a class="navbar-brand d-flex align-items-center" href="home.php" style="font-family: Aboreto, serif;"><span class="fs-2">MoodGram</span></a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-5"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navcol-5">
                <ul class="navbar-nav ms-auto" style="height: 4px;"></ul><a class="btn btn-primary ms-md-2" role="button" data-bss-hover-animate="pulse" href="register.html" style="padding: 8px 14px;">Sign Up</a>
            </div>
        </div>
    </nav>
    <section class="position-relative py-4 py-xl-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-8 col-xl-6 text-center mx-auto">
                    <h2>Log In</h2>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-5">
                        <div class="card-body d-flex flex-column align-items-center" style="margin-bottom: 26px;">
                            <div class="bs-icon-xl bs-icon-circle bs-icon-primary bs-icon my-4"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-person">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"></path>
                                </svg></div>
                                <form class="text-center" method="post">
                                    <div class="mb-3"><input class="form-control" type="email" name="email" id="email" placeholder="Email" required="" value="<?= htmlspecialchars($_POST["email"] ?? "")?>" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"></div>
                                    <div class="mb-3"><input class="form-control" type="password" name="password" id="password" placeholder="Password" required="" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"></div>
                                    <?php if ($is_invalid):?>
                                        <em> Invalid login information </em>
                                    <?php endif; ?>
                                <button class="btn btn-primary d-block w-100 mb-3" type="submit">Login</button>
                                <a class="btn btn-primary d-block w-100" role="button" href="register.html">Create new account</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="../assets/bootstrap/js/bootstrap.min.js?h=981245863c383366a329259d02b8172c"></script>
    <script src="../assets/js/bs-init.js?h=67ee20cf4e5150919853fca3720bbf0d"></script>
    <script src="../assets/js/Material-Text-Input.js?h=713af0c6ce93dbbce2f00bf0a98d0541"></script>
</body>

</html>