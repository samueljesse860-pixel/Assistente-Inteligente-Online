<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['usuario_id'])) { exit; }

if (isset($_POST['id']) && isset($_POST['novo_status'])) {
    $id = $_POST['id'];
    $status = $_POST['novo_status'];
    $user_id = $_SESSION['usuario_id'];

    // Garante que o usuário só altera campanhas que lhe pertencem
    $sql = "UPDATE campanhas SET status = '$status' WHERE id = '$id' AND usuario_id = '$user_id'";
    $conexao->query($sql);
}

header('Location: GerenciarCampanhas.php');
exit;
?>
