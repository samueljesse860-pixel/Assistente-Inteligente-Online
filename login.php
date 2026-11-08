<?php
include_once("includes/config.php");



?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Login</title>

<!-- Importação de Fonte Premium para o padrão Adidas -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;700;900&display=swap" rel="stylesheet">

<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    letter-spacing: .05em;
  }

  body {
    font-family: 'Archivo', sans-serif;
    background: linear-gradient(to right, #0d47a1, #002171); /* Fundo original mantido */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    -webkit-font-smoothing: antialiased;
  }

  /* Container Profissional Minimalista (Estilo Adidas Tech) */
  .form-container {
    background: #ffffff;
    padding: 45px 40px;
    border-radius: 0px; /* Bordas retas premium */
    width: 380px;
    text-align: left; /* Alinhamento mais corporativo à esquerda */
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  }

  .form-container h2 {
    font-family: 'Archivo Black', sans-serif;
    font-size: 1.8rem;
    text-transform: uppercase;
    margin-bottom: 25px;
    color: #000000;
  }

  /* Inputs Sofisticados */
  input {
    width: 100%;
    padding: 14px 15px;
    margin: 8px 0 16px 0;
    border-radius: 0px; /* Bordas retas */
    border: 1px solid #b0bec5;
    font-family: 'Archivo', sans-serif;
    font-size: 0.95rem;
    color: #000000;
    transition: border-color 0.2s ease;
  }

  input:focus {
    outline: none;
    border-color: #0d47a1;
  }

  .password-box {
    position: relative;
  }

  .password-box span {
    position: absolute;
    right: 15px;
    top: 25px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
  }
  
  .password-box span:hover {
    opacity: 1;
  }

  /* Botão Premium Plano com Alto Impacto Visual */
  button {
    width: 100%;
    height: 52px;
    background: #000000; /* Preto brutalista para destacar do fundo */
    color: #ffffff;
    border: 1px solid #000000;
    border-radius: 0px; /* Sem cantos arredondados amadores */
    font-family: 'Archivo', sans-serif;
    font-weight: 900;
    font-size: 0.9rem;
    text-transform: uppercase;
    cursor: pointer;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    transition: all 0.2s ease-in-out;
    margin-top: 10px;
  }

  button:hover {
    background: #ffffff;
    color: #000000;
    border-color: #000000;
    transform: none; /* Remoção do efeito 3D */
    box-shadow: none;
  }

  button:active {
    background: #f5f5f5;
    transform: none;
    box-shadow: none;
  }

  /* Erro estilizado de forma limpa */
  .erro {
    color: #d32f2f;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 10px;
    min-height: 20px;
  }
</style>
</head>

<?php
session_start(); // Inicia a sessão para manter o usuário logado

if (isset($_POST['submit'])) {
    
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Procura o usuário pelo email
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conexao->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica se a senha coincide com o Hash guardado
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            // Redireciona conforme o tipo de usuário
            if ($usuario['tipo'] == 'admin') {
                header('Location: DashboardAdmin.php'); // Página que podes criar para o Admin
            } else {
                header('Location: Bemvindo.php'); // Loading...
            }
        } else {
            $erro_login = "Senha incorreta!";
        }
    } else {
        $erro_login = "Usuário não encontrado!";
    }
}
?>

<body>
<div class="form-container">
  <h2>Login</h2>

  <form action="Login.php" method="POST">
    <input type="email" name="email" id="email" placeholder="Email" required>

    <div class="password-box">
      <input type="password" name="senha" id="senha" placeholder="Palavra-passe" required>
      <span onclick="toggleSenha()">👁️</span>
    </div>

    <!-- Exibe erro vindo do PHP -->
    <p class="erro"><?php echo isset($erro_login) ? $erro_login : ''; ?></p>

    <button type="submit" name="submit">Login</button>
  </form>
</div>

<script>
function toggleSenha() {
  let senha = document.getElementById("senha");
  senha.type = (senha.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
