<?php
// teste_chat.php
$_SESSION['usuario_id'] = 1; // Simula login

$_POST['mensagem'] = "Crie uma campanha para um novo café artesanal chamado 'Café do Bairro'";

include 'gemini_engine.php';
?>
