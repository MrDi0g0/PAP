<?php
    session_start();
    include_once('Config.php');
    //print_r($_SESSION);
    if((!isset($_SESSION['email']) == true ) and (!isset($_SESSION['pass']) == true ))
    {
        unset($_SESSION['email']);
        unset($_SESSION['pass']);
        header("location: login.php");
    }
    $logado = $_SESSION['email'];

    $sql_query = $conexao->query("SELECT users.path FROM users WHERE users.bd_email = '".$_SESSION['email']."'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/IndexC.css">
    <link rel="shortcut icon" href="Img/Logo-img-white.png" type="image/x-icon">
    <title>Home - Entrobots</title>
</head>
<body style="background: url(Img/fundo.jpg);">
    <header class="header-container">
        <div class="header-left">
            <a href="" class="img-header"><img src="Img/Logo-Main-white.png" alt=""></a>
        </div>

        <div class="menu">
            <a href="" class="active">Home</a>
            <a href="Tempo.php">Tempos</a>
            <a href="Ranking.php">Ranking</a>
        </div>

        <div class="header-right">
            <div class="user">
                <?php
                    $resultArray = $sql_query->fetch_all(MYSQLI_ASSOC);

                    $imagePath = (!empty($arquivo) && isset($arquivo['path'])) ? $arquivo['path'] : (!empty($resultArray[0]['path']) ? $resultArray[0]['path'] : "Img/user-white.png");
                ?>
                    
                <img src="<?php echo $imagePath; ?>" alt="">
                <ul class="menu-conta">
                    <li><a href="Perfil.php">Conta</a></li>
                    <li><a href="Sair.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main>

        <div class="pos-vid">
            <div class="text-vid">
                <h2>ROBOTICA</h2>
            </div>
            <div class="carousel">
                <div class="carousel-container">
                    <div class="carousel-item"><iframe width="560" height="315" src="https://www.youtube.com/embed/K-92xNpCtko?si=tx9c5wgSYMk23TwR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
                    <div class="carousel-item"><iframe width="560" height="315" src="https://www.youtube.com/embed/mVNnu0h1NU4?si=dR0gP25kGCW2gGCO" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
                    <div class="carousel-item"><iframe width="560" height="315" src="https://www.youtube.com/embed/ZoJXViFkFUQ?si=FTnnhlMHV_XJN5nZ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
                </div>
                <div class="carousel-btn prev-btn" onclick="changeSlide(-1)">&#10094;</div>
                <div class="carousel-btn next-btn" onclick="changeSlide(1)">&#10095;</div>
            </div>
        </div>

    </main>
    
    <footer>
        <div class="left">
            <a href=""><img src="Img/Logo-Main-white.png" alt=""></a>
        </div>
        <div class="gap"></div>
        <div class="links-footer">
            <div class="center">
                <h3>LINKS</h3>
                <ul>
                    <li><a href="Index.php">Home</a></li>
                    <li><a href="Tempo.php">Tempos</a></li>
                    <li><a href="Ranking.php">Ranking</a></li>
                </ul>
            </div>
            <div class="gap"></div>
            <div class="right">
                <h3>REDES SOCIAIS</h3>
                <ul>
                    <li><a href="https://www.youtube.com/channel/UCRDOECLtEchq9kcXP04EsKA"><i class="fa-brands fa-youtube"></i>Youtube</a></li>
                    <li><a href=""><i class="fa-brands fa-facebook"></i>Facebook</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/c97cbab7f2.js" crossorigin="anonymous"></script>
    <script src="JAVA/Index.js"></script>
</body>
</html>