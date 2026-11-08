<?php
session_start();
include_once('config.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    die("Erro: Usuário não autenticado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $mensagem = $conexao->real_escape_string($_POST['mensagem']);
    $resposta = $conexao->real_escape_string($_POST['resposta']);

    $sql = "INSERT INTO chat_historico (usuario_id, mensagem_usuario, resposta_ia) 
            VALUES ('$usuario_id', '$mensagem', '$resposta')";

    if ($conexao->query($sql)) {
        echo "Sucesso";
    } else {
        echo "Erro ao guardar: " . $conexao->error;
    }
}
?>
