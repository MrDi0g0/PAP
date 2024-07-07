<?php

    $bd_Host = 'localhost';
    $bd_Username = 'root';
    $bd_Password = '';
    $bd_Name = 'ENTROBOTS';

    $conexao = new mysqli($bd_Host, $bd_Username, $bd_Password, $bd_Name);

    /*if($mysqli->connect_errno)
    {
        echo "Falha na conectar: (" . $mysqli->connect_errno . ") " . $mysql->connect_error;
    }
        echo "deuuu"
        */
?>