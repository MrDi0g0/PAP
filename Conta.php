<?php
    session_start();

    if(isset($_POST['submit']) && !empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['pass']))
    {
        include_once('Config.php');
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];

        // Verifica se o nome de usuário já está em uso
        $sql_check_nome = "SELECT * FROM users WHERE bd_nome = '$nome'";
        $result_check_nome = $conexao->query($sql_check_nome);

        if(mysqli_num_rows($result_check_nome) > 0)
        {
            // Nome de usuário já em uso
            echo "<script>alert('Este nome de usuário já está em uso. Por favor, escolha outro.'); window.location.href = 'Conta.html';</script>";
        }
        else
        {
            // Nome de usuário não em uso, verifica se o email já está registrado
            $sql_check_email = "SELECT * FROM users WHERE bd_email = '$email'";
            $result_check_email = $conexao->query($sql_check_email);

            if(mysqli_num_rows($result_check_email) > 0)
            {
                // Email já registrado
                echo "<script>alert('Este email já está em uso. Por favor, escolha outro.'); window.location.href = 'Conta.html';</script>";
            }
            else
            {
                // Email e nome de usuário não registrados, insere no banco de dados
                $sql_insert = "INSERT INTO users (bd_nome, bd_email, bd_password) VALUES ('$nome', '$email', '$pass')";
                if($conexao->query($sql_insert) === TRUE)
                {
                    // Registro bem-sucedido, exibe alerta e redireciona para a página inicial
                    echo "<script>alert('Conta criada com sucesso!'); window.location.href = 'Index.php';</script>";
                    $_SESSION['email'] = $email;
                    $_SESSION['pass'] = $pass;
                    //header('Location: Index.php');
                    exit; // Certifique-se de sair após redirecionar para evitar execução adicional de código
                }
                else
                {
                    // Erro ao registrar
                    echo "<script>alert('Erro ao registrar conta. Por favor, tente novamente.'); window.location.href = 'Conta.html';</script>";
                }
            }
        }
    }
    else
    {
        // Os campos não foram preenchidos, redirecione para a página de registro
        header('Location: Register.html');
        exit; // Sair após redirecionamento
    }
?>
