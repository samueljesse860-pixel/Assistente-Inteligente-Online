<?php
session_start(); // <--- ESSENCIAL

// --- LIGAÇÃO À BASE DE DADOS ---
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'assistente_online';
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conexao->connect_error) {
    die("Falha na ligação: " . $conexao->connect_error);
}
$conexao->set_charset("utf8mb4");

// --- SEGURANÇA ---
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: DashboardAdmin.php?erro=sem_permissao');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: DashboardAdmin.php?erro=id_invalido');
    exit;
}

// Verifica se o utilizador existe e não é admin
$stmt = $conexao->prepare("SELECT tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: DashboardAdmin.php?erro=usuario_inexistente');
    exit;
}
$user = $result->fetch_assoc();
if ($user['tipo'] === 'admin') {
    header('Location: DashboardAdmin.php?erro=nao_permite_admin');
    exit;
}
$stmt->close();

// --- Tentar eliminar ---
$stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header('Location: DashboardAdmin.php?msg=utilizador_eliminado');
    } else {
        // Nenhuma linha foi afetada (não deveria acontecer, mas por precaução)
        header('Location: DashboardAdmin.php?erro=nenhuma_linha_afetada');
    }
} else {
    // Erro na execução (ex.: foreign key)
    $erro = $stmt->error;
    // Pode registar o erro num log
    error_log("Erro ao eliminar utilizador $id: $erro");
    header('Location: DashboardAdmin.php?erro=erro_eliminar');
}
$stmt->close();
$conexao->close();
exit;
?>
