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

    //--------- Imagem de Perfil ---------//

    if(isset($_FILES['arquivo']))
    {
        $arquivo = $_FILES['arquivo'];

        if($arquivo['error'])
        {
            die("<script>alert('Erro ao enviar a imagem'); window.history.back();</script> A imagem é muito grande !! Max: 5MB");
        }

        if ($arquivo['size'] > 5300000) 
        {
            die("<script>alert('A imagem escolhida não pode ultrapassar os 5MB!!'); window.history.back();</script> A imagem é muito grande !! Max: 5MB");
        }
        
        //var_dump($_FILES['arquivo']);
        $pasta = "img-perfil/";
        $nomeDoArquivo = $arquivo['name'];
        $novoNomeDoArquivo = uniqid();
        $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

        if($extensao != "jpg" && $extensao != "png" && $extensao != "gif")
        {
            die("<script>alert('Esse tipo de arquivo não é permitido'); window.history.back();</script> A imagem é muito grande !! Max: 5MB");
        }

        $query = $conexao->query("SELECT path FROM users WHERE bd_email = '".$_SESSION['email']."'");
        $usuario = $query->fetch_assoc();
    
        if($usuario && !empty($usuario['path']))
        {
            // Excluir o arquivo antigo
            unlink($usuario['path']);
        }

        $path = $pasta . $novoNomeDoArquivo . "." . $extensao;

        $deu_certo = move_uploaded_file($arquivo["tmp_name"], $path);
        if($deu_certo)
        {
            $result = $conexao->query("UPDATE users SET nome_path = '$nomeDoArquivo', path = '$path' WHERE bd_email = '".$_SESSION['email']."'");
            //echo "<p>Deu certo</p>";
        }
        else
        {
            //echo "<p>nao deu</p>";
        }
    }
    
    $sql_query = $conexao->query("SELECT users.path FROM users WHERE users.bd_email = '".$_SESSION['email']."'");

    
    if(isset($_POST['img-delete'])) 
    {
        // Consultar o caminho da imagem do banco de dados
        $query = $conexao->query("SELECT path FROM users WHERE bd_email = '".$_SESSION['email']."'");
        $usuario = $query->fetch_assoc();

        // Verificar se há uma imagem associada ao usuário
        if($usuario && !empty($usuario['path']))
        {
            // Excluir o arquivo de imagem do servidor
            if (unlink($usuario['path'])) 
            {
                // Remover o caminho da imagem do banco de dados
                $conexao->query("UPDATE users SET nome_path = NULL, path = NULL WHERE bd_email = '".$_SESSION['email']."'");
                echo "<script>alert('Imagem de perfil apagada com sucesso.');</script>";
            } 
            else 
            {
                echo "<script>alert('Erro ao apagar a imagem de perfil.');</script>";
            }
        }
        else
            echo "<script>alert('Não há imagem de perfil associada a este usuário.');</script>";
    }


    //--------- Alterações de dados ---------//

    if (isset($_POST['btn-dados-user'])) 
    {
        $updates = array();
        
        if (!empty($_POST['nome'])) 
        {
            $nome = mysqli_real_escape_string($conexao, $_POST['nome']);
            $updates[] = "bd_nome = '$nome'";
        }
        
        if (!empty($_POST['pass'])) 
        {
            $pass = mysqli_real_escape_string($conexao, $_POST['pass']);
            $updates[] = "bd_password = '$pass'";
        }
    
        if (!empty($updates) && isset($_SESSION['email'])) 
        {
            $email = mysqli_real_escape_string($conexao, $_SESSION['email']);
            $updateString = implode(', ', $updates);
            $sql = "UPDATE users SET $updateString WHERE bd_email = '$email'";
            
            if ($conexao->query($sql) === TRUE) 
                echo "<script>alert('Dados atualizados com sucesso.'); window.history.back();</script>";
            else 
                //echo "Erro ao atualizar os dados: " . $conexao->error;
                echo "<script>alert('Erro ao atualizar os dados'); window.history.back();</script>";
        } 
        else 
            echo "<script>alert('Nenhum dado fornecido para atualização.'); window.history.back();</script>";
    }
    
    //--------- TAG RFID ---------//
    $Write="<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
    file_put_contents('UIDContainer.php',$Write);

    //--------- GUARDAR TAG RFID ---------//
    if(isset($_POST['btn-guardar-TAG'])) 
    {
        $uidResult = $_POST['UIDresult'];
        $nome = $_POST['nome'];
        
        // Verificar se o UIDresult já existe na base de dados
        $sqlCheck = "SELECT COUNT(*) AS count FROM tags WHERE bd_id = '$uidResult'";
        $resultCheck = $conexao->query($sqlCheck);
        $row = $resultCheck->fetch_assoc();
        $count = $row['count'];
        
        if($count > 0) 
        {
            // UIDresult já existe na base de dados, exibir mensagem de erro
            echo "<script>alert('Essa Tag já existe. Por favor, escolha outra.'); window.history.back();</script>";
            exit();
        } 
        else 
        {
            // UIDresult não existe na base de dados, inserir novo registro
            $sqlInsert = "INSERT INTO tags (bd_id, bd_nome, bd_email) VALUES ('$uidResult', '$nome', '{$_SESSION['email']}')";
            $insertion = $conexao->query($sqlInsert);
            
            if($insertion) 
            {
                echo "<script>alert('Tag registada com sucesso.'); window.history.back();</script>";
                exit();
            } else 
            {
                echo "<script>alert('Erro ao registar, tente novamente.'); window.history.back();</script>";
                exit();
            }
        }
    }
    

    //--------- MOSTRAR TABELA TAG RFID ---------//

    $sql = "SELECT * FROM tags WHERE bd_email = '" . $_SESSION['email'] . "' ";
    $result = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/PerfilC.css">
    <link rel="shortcut icon" href="Img/Logo-img-white.png" type="image/x-icon">
    <script src="JAVA/jquery.min.js"></script>
    <script src="JAVA/bootstrap.min.js"></script>
		<script>
			$(document).ready(function(){
				 $("#getUID").load("UIDContainer.php");
				setInterval(function() {
					$("#getUID").load("UIDContainer.php");
				}, 500);
			});
		</script>
    <title>Perfil - Entrobots</title>
    
</head>
<body style="background: url(Img/fundo.jpg);">
    <header class="header-container">

        <div class="header-left">
            <a href="Index.php" class="img-header"><img src="Img/Logo-Main-white.png" alt=""></a>
        </div>
        
        <div class="menu">
            <a href="Index.php">Home</a>
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

                <div class="menu-conta">
                    <ul>
                        <li><a href="" class="active">Conta</a></li>
                        <li><a href="Sair.php">Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>

    </header>

    <main>
        <div class="pos-img-up">
            <div class="txt-tit">
                <h2>Perfil</h2>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="form-img">
                <div class="img-perfil">
                        <h3>Mudar Imagem de Perfil</h3>
                    <div class="img-path">
                    <?php
                        mysqli_data_seek($sql_query, 0);
                        
                        $arquivo = $sql_query->fetch_assoc();
                        $imagePath = ($arquivo && isset($arquivo['path'])) ? $arquivo['path'] : "Img/user.png";
                        do {
                            ?>
                            
                            <img src="<?php echo $imagePath; ?>" alt="">
                            <?php
                        } while ($arquivo = $sql_query->fetch_assoc());
                        ?>
                    </div>
                    <div class="file-input-container">
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="fileInput" class="file-input-label">
                                <span>Escolha um arquivo</span>
                                <input type="file" id="fileInput" class="file-input" name="arquivo">
                            </label>
                        </form>
                    </div>
                    <div class="pad"></div>
                    <div class="btn-submit">
                        <button type="submit" class="btn" name="upload">Enviar</button>
                        <form action="" method="post">
                            <button type="submit" class="btn" name="img-delete">Apagar</button>
                        </form>
                    </div>
                </div>
            </form>

            <div class="txt-tit">
                <h2>Alterações de Dados</h2>
            </div>
            <form action="" method="post" class="form-user">
                <div class="utili-container">
                    <div class="input-user">
                        <label for="">Username</label>
                        <div class="pad"></div>
                        <input type="text" name="nome">
                    </div>
                    <div class="input-user">
                        <label for="">Password</label>
                        <div class="pad"></div>
                        <input type="password" name="pass">
                    </div>
                    <div class="pad"></div>
                    <button type="submit" name="btn-dados-user">Guardar alterações</button>
                </div>
            </form>

            <div class="txt-tit">
                <h2>Registar TAG</h2>
            </div>
            <form action="" method="post" class="form-tag">
                <div class="utili-container">
                <div class="input-user">
                        <label for="">ID</label>
                        <div class="pad"></div>
                        <textarea name="UIDresult" id="getUID" placeholder="Passe a sua Tag no sensor" rows="1" cols="3" required></textarea>
                    </div>
                    <div class="input-user">
                        <label for="">Nome</label>
                        <div class="pad"></div>
                        <input type="text" name="nome">
                    </div>
                    <div class="pad"></div>
                    <button type="submit" name="btn-guardar-TAG">Novo</button>
                </div>
            </form>

            <div class="pos-table">
            <h2>TAGs Registadas</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><h3>ID</h3></th>
                            <th scope="col"><h3>Nome</h3></th>
                            <th scope="col"><h3>Email</h3></th>
                            <th scope="col"><h3>...</h3></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        // Loop para exibir os dados
                        if(mysqli_num_rows($result) > 0) 
                        {
                            while ($user_data = mysqli_fetch_assoc($result)) 
                            {
                                echo "<tr>";
                                echo "<td>" . $user_data["bd_id"] . "</td>";
                                echo "<td>" . $user_data["bd_nome"] . "</td>";
                                echo "<td>" . $user_data["bd_email"] . "</td>";
                                echo "<td> <a class='btn-delete' href='DeleteTag.php?bd_id=" . $user_data['bd_id'] . "& bd_email=" . $user_data['bd_email'] . "'><i class='fa-solid fa-trash' style='color: white;'></i></a> </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Não existem tags registadas</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
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
</body>
</html>