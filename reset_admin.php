<?php
// Configurações de ligação à base de dados
$host = '127.0.0.1';
$db   = 'assistente_online';
$user = 'root'; // Altere se o seu utilizador do MySQL for diferente
$pass = '';     // Altere se a sua password do MySQL for diferente
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}

// Configuração da nova password
$emailAdmin = 'admin@projeto.com';
$novaSenhaTexto = 'Admin1234@'; // Esta será a sua nova password de acesso

// Gera a hash segura compatível com o seu sistema
$novaSenhaHash = password_hash($novaSenhaTexto, PASSWORD_DEFAULT);

try {
    // Atualiza a password do administrador principal
    $sql = "UPDATE usuarios SET senha = :senha WHERE email = :email AND tipo = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'senha' => $novaSenhaHash,
        'email' => $emailAdmin
    ]);

    if ($stmt->rowCount() > 0) {
        echo "<h3>Sucesso!</h3>";
        echo "A password do administrador foi redefinida com êxito.<br><br>";
        echo "<strong>Dados de Acesso:</strong><br>";
        echo "Email: " . htmlspecialchars($emailAdmin) . "<br>";
        echo "Nova Password: " . htmlspecialchars($novaSenhaTexto) . "<br><br>";
        echo "<strong style='color:red;'>AVISO DE SEGURANÇA: Elimine este ficheiro (reset_admin.php) imediatamente após o login!</strong>";
    } else {
        echo "Nenhum utilizador com o email '$emailAdmin' e tipo 'admin' foi encontrado.";
    }
} catch (\PDOException $e) {
    echo "Erro ao atualizar a base de dados: " . $e->getMessage();
}
