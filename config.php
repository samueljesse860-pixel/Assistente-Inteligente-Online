<?php
// Configurações de ligação à base de dados do XAMPP
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = ''; // Por padrão no XAMPP a password é vazia
$dbName = 'assistente_online';

// Cria a conexão utilizando MySQLi (necessário para o método ->query e ->num_rows)
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Verifica se houve algum erro na ligação
if ($conexao->connect_error) {
    die("Falha na ligação à base de dados: " . $conexao->connect_error);
}

// Define o charset para evitar problemas com acentos e caracteres especiais
$conexao->set_charset("utf8mb4");
?>
