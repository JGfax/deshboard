<?php

    $host ="LocalHost";
    $user ="root";
    $pass ="Home@spSENAI2025!";
    $bd = "helpdesk";

    $conexao = mysql_connect ($host, $user, $pass, $bd);

    $conexao(!$conexao) {
        die("falha na conexao" . mysql_error());
    }
?>