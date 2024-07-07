<?php
    session_start();
    include_once('Config.php');

    if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['pass']))
    {
        $email = $_POST['email'];
        $pass = $_POST['pass'];

        $sql = "SELECT * FROM users WHERE bd_email = '$email'";
        $result = $conexao->query($sql);

        if(mysqli_num_rows($result) < 1)
        {
            // Email não registrado
            echo "<script>alert('Dados incorretos !!'); window.location.href = 'Login.html';</script>";
        }
        else
        {
            // Email registrado, verifique a senha
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            if($row['bd_password'] != $pass)
            {
                // Senha incorreta
                echo "<script>alert('Dados incorretos !!'); window.location.href = 'Login.html';</script>";
            }
            else
            {
                // Email e senha corretos, inicie a sessão e redirecione para a página inicial
                $_SESSION['email'] = $email;
                $_SESSION['pass'] = $pass;
                header('Location: Index.php');
                exit; // Certifique-se de sair após redirecionar para evitar execução adicional de código
            }
        }
    }
    else
    {
        // Os campos não foram preenchidos, redirecione para a página de login
        header('Location: Login.html');
        exit; // Sair após redirecionamento
    }
?>
