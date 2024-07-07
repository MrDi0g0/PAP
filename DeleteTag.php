<?php
include_once('Config.php');

if(isset($_GET['bd_id']) && isset($_GET['bd_email'])) {
    $id = $_GET['bd_id'];
    $email = $_GET['bd_email'];

    $sqlDelete = "DELETE FROM tags WHERE bd_id = '$id' AND bd_email = '$email'";
    $resultDelete = $conexao->query($sqlDelete);
    
    if($resultDelete) {
        // Redirecionar de volta para a página depois de excluir o registro
        echo "<script>alert('TAG removida com sucesso'); window.history.back();</script>";
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
