<?php

    $host ="LocalHost";
    $user ="root";
    $pass ="Home@spSENAI2025!";
    $bd = "helpdesk";

    $conexao = mysqli_connect($host, $user, $pass, $bd);

    if (!$conexao) {
        die("falha na conexao" . mysqli_connect_error());
    }
?>