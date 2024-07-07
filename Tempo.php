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

    $sql = "SELECT users.bd_nome, tempo.bd_tempo, tempo.bd_data FROM tempo JOIN users ON users.bd_email = tempo.bd_email WHERE 
    users.bd_email = '".$_SESSION['email']."' ORDER BY LPAD(tempo.bd_tempo, 6, '0') ASC";
    $result = $conexao->query($sql);

    $num = 1;
 
    $sql_query = $conexao->query("SELECT users.path FROM users WHERE users.bd_email = '".$_SESSION['email']."'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Tempo-Ranking.css">
    <link rel="shortcut icon" href="Img/Logo-img-white.png" type="image/x-icon">
    <title>Tempos - Entrobots</title>
</head>
<body style="background: url(Img/fundo.jpg);">
    <header class="header-container">
        <div class="header-left">
            <a href="Index.php" class="img-header"><img src="Img/Logo-Main-white.png" alt=""></a>
        </div>

        <div class="menu">
            <a href="Index.php">Home</a>
            <a href="" class="active">Tempos</a>
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

        <div class="pos-table">
            <h2>OS TEUS TEMPOS</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><h3>#</h3></th>
                            <th scope="col"><h3>Nome</h3></th>
                            <th scope="col"><h3>Tempo</h3></th>
                            <th scope="col"><h3>Data</h3></th>
                            <th scope="col"><h3>...</h3></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $num = 1;
                            while ($user_data = mysqli_fetch_assoc($result)) {
                                $minutes = floor($user_data["bd_tempo"] / 60000);
                                $seconds = floor(($user_data["bd_tempo"] % 60000) / 1000);
                                $milliseconds = $user_data["bd_tempo"] % 1000;
                            
                                $bd_tempo = sprintf('%02d:%02d:%03d', $minutes, $seconds, $milliseconds);
                                
                                echo "<tr>";
                                echo "<td>" . $num++ . "</td>";
                                echo "<td>" . $user_data["bd_nome"] . "</td>";
                                echo "<td>" . $bd_tempo . "</td>";
                                echo "<td>" . $user_data["bd_data"] . "</td>";
                                echo "<td> <a class='btn-delete' href='DeletetTempo.php?" . "& bd_tempo=" . $user_data['bd_tempo'] . "& bd_data=" . $user_data['bd_data'] ."'>
                                <i class='fa-solid fa-trash' style='color: white;'></i></a> </td>";
                                echo "</tr>";
                            }
                            
                            if ($num === 1) 
                                echo "<td colspan='5'>Não existe tempos registados</td>";
                            
                        ?>
                    </tbody>
                </table>
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
</body>
</html>