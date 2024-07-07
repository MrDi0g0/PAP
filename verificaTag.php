<?php
    include_once('Config.php');
    if(isset($_POST['UIDresult'])) {
        $UIDresult = $_POST['UIDresult'];
        // Consulta para verificar se o ID da tag está conectado a algum email
        $sql = "SELECT tags.bd_email FROM tags WHERE tags.bd_id = '$UIDresult'";
        $result = $conexao->query($sql);

        if ($result->num_rows > 0) {
            // Se houver correspondência, envia o email encontrado de volta para o dispositivo
            $row = $result->fetch_assoc();
            echo $row['bd_email'];
        } else {
            // Se não houver correspondência, retorna uma resposta indicando que não está conectado a nenhum email
            echo "Nenhum email encontrado para este UID";
        }
    }
?>