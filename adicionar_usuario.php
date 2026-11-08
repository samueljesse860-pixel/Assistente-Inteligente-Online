<?php
session_start();

// --- LIGAÇÃO À BASE DE DADOS (igual ao dashboard) ---
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'assistente_online';
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conexao->connect_error) { die("Falha na ligação: " . $conexao->connect_error); }
$conexao->set_charset("utf8mb4");

// --- SEGURANÇA ---
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: DashboardAdmin.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $senha  = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if (empty($nome) || empty($email) || empty($senha) || empty($senha2)) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif ($senha !== $senha2) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        // Verifica duplicado
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $erro = 'Este email já está registado.';
        } else {
            $stmt->close();
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $tipo = 'user';
            $data_cadastro = date('Y-m-d H:i:s');

            $stmt = $conexao->prepare("INSERT INTO usuarios (nome, email, senha, tipo, data_cadastro) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $email, $senha_hash, $tipo, $data_cadastro);
            if ($stmt->execute()) {
                $sucesso = 'Utilizador adicionado com sucesso!';
                $nome = $email = $senha = $senha2 = '';
            } else {
                $erro = 'Erro ao adicionar: ' . $stmt->error;
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
    <title>Adicionar Utilizador</title>
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
    </style>
</head>
<body>
<div class="container">
    <h1>➕ Adicionar Utilizador</h1>
    <?php if ($erro): ?><p class="erro"><?php echo htmlspecialchars($erro); ?></p><?php endif; ?>
    <?php if ($sucesso): ?><p class="sucesso"><?php echo htmlspecialchars($sucesso); ?></p><?php endif; ?>
    <form method="post">
        <label>Nome</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>

        <label>Senha (mínimo 6 caracteres)</label>
        <input type="password" name="senha" required>

        <label>Confirmar Senha</label>
        <input type="password" name="senha2" required>

        <button type="submit" class="btn">Adicionar</button>
    </form>
    <a href="DashboardAdmin.php" class="voltar">← Voltar ao Dashboard</a>
</div>
</body>
</html>
