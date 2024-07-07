<?php
include_once('Config.php');

if(isset($_GET['bd_tempo']) && isset($_GET['bd_data'])) {
    $tempo = $_GET['bd_tempo'];
    $data = $_GET['bd_data'];

    $sqlDelete = "DELETE FROM tempo WHERE bd_tempo = '$tempo' AND bd_data = '$data'";
    $resultDelete = $conexao->query($sqlDelete);
    
    if($resultDelete) {
        // Redirecionar de volta para a página depois de excluir o registro
        echo "<script>alert('Tempo removido com sucesso'); window.history.back();</script>";
        //header('Location: perfil.php');
        exit();
    } else {
        // Em caso de erro na exclusão
        echo "Erro ao excluir o registro.";
    }
} else {
    echo "ID e email não especificados.";
}
?>