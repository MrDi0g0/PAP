<?php
include_once('Config.php');

if (isset($_POST['email']) && isset($_POST['tempo'])) {
    $email = $_POST['email'];
    $tempo = $_POST['tempo'];
    $data = date('Y-m-d');

    if (empty($tempo)) { // Verifica se o tempo é vazio ou nulo
        echo "Erro: Tempo vazio ou não foi recebido.";
    } else {
        $sql = "INSERT INTO tempo (bd_email, bd_tempo, bd_data) VALUES ('$email', '$tempo', '$data')";
        if ($conexao->query($sql) === TRUE) {
            echo "Tempo registrado com sucesso para o email: " . $email;
        } else {
            echo "Erro ao registrar tempo no banco de dados: " . $conexao->error;
        }
    }    
} else {
    echo "Parâmetros incompletos ou ausentes";
}
?>
