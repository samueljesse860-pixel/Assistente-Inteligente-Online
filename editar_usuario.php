<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'assistente_online';
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conexao->connect_error) die("Falha na ligação: " . $conexao->connect_error);
$conexao->set_charset("utf8mb4");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: TelaInicial00.php');
    exit;
}

$erro = '';
$sucesso = '';
$utilizador = null;

// Buscar dados atuais
$stmt = $conexao->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: DashboardAdmin.php');
    exit;
}
$utilizador = $result->fetch_assoc();
$stmt->close();

// Processar submissão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    // Validações
    if (empty($nome) || empty($email)) {
        $erro = 'Nome e email são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (!empty($senha) && $senha !== $senha2) {
        $erro = 'As senhas não coincidem.';
    } elseif (!empty($senha) && strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        // Verifica se o email já está em uso por outro utilizador
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $erro = 'Este email já está a ser utilizado por outro utilizador.';
        } else {
            $stmt->close();
            // Construir query dinamicamente
            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nome, $email, $senha_hash, $id);
            } else {
                $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $nome, $email, $id);
            }
            if ($stmt->execute()) {
                $sucesso = 'Utilizador atualizado com sucesso!';
                // Atualizar dados exibidos
                $utilizador['nome'] = $nome;
                $utilizador['email'] = $email;
            } else {
                $erro = 'Erro ao atualizar: ' . $stmt->error;
            }
            $stmt->close();
        }
    }
}
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Utilizador</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 40px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border: 1px solid #ddd; }
        h1 { font-size: 1.5em; margin-top: 0; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; }
        .btn { padding: 10px 20px; background: #000; color: #fff; border: none; cursor: pointer; }
        .btn:hover { background: #333; }
        .erro { color: #d0021b; }
        .sucesso { color: green; }
        .voltar { display: inline-block; margin-top: 20px; color: #000; }
        .info { background: #f9f9f9; padding: 10px; border-left: 4px solid #000; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ Editar Utilizador</h1>
    <?php if ($erro): ?><p class="erro"><?php echo htmlspecialchars($erro); ?></p><?php endif; ?>
    <?php if ($sucesso): ?><p class="sucesso"><?php echo htmlspecialchars($sucesso); ?></p><?php endif; ?>
    <div class="info">
        <strong>ID:</strong> <?php echo $utilizador['id']; ?> &nbsp;|&nbsp;
        <strong>Tipo:</strong> <?php echo strtoupper($utilizador['tipo']); ?>
    </div>
    <form method="post">
        <label>Nome</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>

        <label>Nova Senha (deixe em branco para não alterar)</label>
        <input type="password" name="senha">

        <label>Confirmar Nova Senha</label>
        <input type="password" name="senha2">

        <button type="submit" class="btn">Atualizar</button>
    </form>
    <a href="DashboardAdmin.php" class="voltar">← Voltar ao Dashboard</a>
</div>
</body>
</html>
