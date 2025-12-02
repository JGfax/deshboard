<?php

    $host ="LocalHost";
    $user ="root";
    $pass ="Home@spSENAI2025!";
    $bd = "helpdesk";

    $conn = new mysqli($host, $user, $pass, $bd);

// Verifica a conexão
if ($conn->connect_error) {
    // Para ambientes de produção, use apenas "Erro de Conexão."
    die("Falha na Conexão com o Banco de Dados: " . $conn->connect_error);
}

// Define o charset para evitar problemas com acentuação
$conn->set_charset("utf8mb4");

// A variável $conn agora contém a conexão ativa com o banco.
?>