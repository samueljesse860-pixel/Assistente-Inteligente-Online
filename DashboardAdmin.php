<?php
session_start();

// --- LIGAÇÃO À BASE DE DADOS (utilizadores, galeria, etc.) ---
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'assistente_online';

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conexao->connect_error) { die("Falha na ligação (online): " . $conexao->connect_error); }
$conexao->set_charset("utf8mb4");

// --- LIGAÇÃO À BASE DAS CAMPANHAS (assistente_inteligente) ---
$dbCampanhas = 'assistente_inteligente';
$conexaoCampanhas = new mysqli($dbHost, $dbUsername, $dbPassword, $dbCampanhas);
if ($conexaoCampanhas->connect_error) { die("Falha na ligação (campanhas): " . $conexaoCampanhas->connect_error); }
$conexaoCampanhas->set_charset("utf8mb4");

// --- CRIA A TABELA GALERIA (se não existir) ---
$criar_tabela_galeria = "CREATE TABLE IF NOT EXISTS galeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255) NOT NULL,
    tipo_midia ENUM('digital','impresso') NOT NULL,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
)";
$conexao->query($criar_tabela_galeria);

// --- SEGURANÇA ---
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// ============================================================
// PROCESSAMENTO DAS AÇÕES DA GALERIA
// ============================================================

// Adicionar nova imagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_galeria']) && $_POST['acao_galeria'] === 'adicionar') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $tipo_midia = $_POST['tipo_midia'];
    $imagem_nome = '';

    // Upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'img/galeria/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem_nome = 'galeria_' . time() . '.' . $ext;
        $caminho_destino = $upload_dir . $imagem_nome;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_destino);
    }

    if ($imagem_nome) {
        $stmt = $conexao->prepare("INSERT INTO galeria (titulo, descricao, imagem, tipo_midia) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $titulo, $descricao, $imagem_nome, $tipo_midia);
        $stmt->execute();
        $stmt->close();
        header("Location: DashboardAdmin.php?msg=galeria_adicionada");
        exit;
    } else {
        header("Location: DashboardAdmin.php?erro=upload_falhou");
        exit;
    }
}

// Editar tipo de mídia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_galeria']) && $_POST['acao_galeria'] === 'editar_tipo') {
    $id = (int)$_POST['id'];
    $novo_tipo = $_POST['tipo_midia'];
    $stmt = $conexao->prepare("UPDATE galeria SET tipo_midia = ? WHERE id = ?");
    $stmt->bind_param("si", $novo_tipo, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: DashboardAdmin.php?msg=tipo_alterado");
    exit;
}

// Excluir imagem da galeria
if (isset($_GET['excluir_galeria']) && is_numeric($_GET['excluir_galeria'])) {
    $id = (int)$_GET['excluir_galeria'];
    $stmt = $conexao->prepare("SELECT imagem FROM galeria WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $arquivo = 'img/galeria/' . $row['imagem'];
        if (file_exists($arquivo)) unlink($arquivo);
    }
    $stmt->close();
    $stmt = $conexao->prepare("DELETE FROM galeria WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: DashboardAdmin.php?msg=galeria_excluida");
    exit;
}

// --- AÇÃO DE ELIMINAR CAMPANHA (da base assistente_inteligente) ---
if (isset($_GET['excluir_campanha']) && is_numeric($_GET['excluir_campanha'])) {
    $id_excluir = (int)$_GET['excluir_campanha'];
    
    // Buscar caminho da imagem para apagar o ficheiro
    $busca = $conexaoCampanhas->prepare("SELECT imagem FROM campanhas WHERE id = ?");
    $busca->bind_param("i", $id_excluir);
    $busca->execute();
    $result = $busca->get_result();
    if ($row = $result->fetch_assoc()) {
        $arquivo = $row['imagem'];
        if (file_exists($arquivo) && strpos($arquivo, 'imagens_salvas/') !== false) {
            unlink($arquivo);
        }
    }
    $busca->close();
    
    $delete = $conexaoCampanhas->prepare("DELETE FROM campanhas WHERE id = ?");
    $delete->bind_param("i", $id_excluir);
    $delete->execute();
    $delete->close();
    
    header("Location: DashboardAdmin.php?msg=campanha_eliminada");
    exit;
}

// ============================================================
// DADOS PARA ESTATÍSTICAS (COM VERIFICAÇÃO DE ERROS)
// ============================================================
$totalUsuarios = 0;
$totalCampanhas = 0;
$totalGaleria = 0;

// Total de utilizadores (base assistente_online)
$result = $conexao->query("SELECT id FROM usuarios");
if ($result) {
    $totalUsuarios = $result->num_rows;
}

// Total de campanhas (base assistente_inteligente)
$result = $conexaoCampanhas->query("SELECT id FROM campanhas");
if ($result) {
    $totalCampanhas = $result->num_rows;
}

// Total de galeria (base assistente_online)
$result = $conexao->query("SELECT id FROM galeria");
if ($result) {
    $totalGaleria = $result->num_rows;
}

// --- PESQUISA DE UTILIZADORES ---
$sql = "SELECT * FROM usuarios ORDER BY data_cadastro DESC";
if (!empty($_GET['pesquisa'])) {
    $busca = $conexao->real_escape_string($_GET['pesquisa']);
    $sql = "SELECT * FROM usuarios WHERE nome LIKE '%$busca%' OR email LIKE '%$busca%' ORDER BY data_cadastro DESC";
}
$usuarios = $conexao->query($sql);
if (!$usuarios) {
    $usuarios = [];
}

// --- BUSCAR CAMPANHAS (da base assistente_inteligente) ---
$campanhas = [];
$resultCampanhas = $conexaoCampanhas->query("SELECT * FROM campanhas ORDER BY data_criacao DESC");
if ($resultCampanhas) {
    while ($linha = $resultCampanhas->fetch_assoc()) {
        $campanhas[] = $linha;
    }
}

// --- BUSCAR IMAGENS DA GALERIA (base assistente_online) ---
$galeria = [];
$resultGaleria = $conexao->query("SELECT * FROM galeria ORDER BY data_cadastro DESC");
if ($resultGaleria) {
    while ($linha = $resultGaleria->fetch_assoc()) {
        $galeria[] = $linha;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Assistente</title>
    <style>
        :root { --preto: #000; --cinza-fundo: #f4f4f4; --branco: #fff; --vermelho: #d0021b; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: var(--cinza-fundo); margin: 0; display: flex; color: var(--preto); }
        .sidebar { width: 260px; background: var(--preto); color: var(--branco); height: 100vh; padding: 40px 20px; box-sizing: border-box; position: sticky; top: 0; }
        .sidebar h2 { font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a { color: var(--branco); text-decoration: none; display: block; padding: 15px 0; border-bottom: 1px solid #333; opacity: 0.8; transition: 0.3s; }
        .sidebar a:hover { opacity: 1; }
        .main-content { flex: 1; padding: 40px; }
        h1 { font-weight: 900; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 30px; }
        .cards { display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
        .card { background: var(--branco); padding: 30px; flex: 1; min-width: 150px; border: 1px solid #e0e0e0; }
        .card h3 { font-size: 0.75em; text-transform: uppercase; letter-spacing: 1px; color: #767676; margin: 0; }
        .card p { font-size: 2.5em; font-weight: 900; margin: 10px 0 0 0; }
        table { width: 100%; border-collapse: collapse; background: var(--branco); margin-top: 20px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid var(--preto); text-transform: uppercase; font-size: 0.75em; letter-spacing: 1px; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 0.9em; vertical-align: middle; }
        .btn { padding: 8px 16px; text-decoration: none; font-size: 0.7em; font-weight: bold; text-transform: uppercase; border: 1px solid var(--preto); cursor: pointer; transition: 0.3s; display: inline-block; }
        .btn-add { background: var(--preto); color: var(--branco); display: inline-block; margin-bottom: 20px; padding: 12px 24px; }
        .btn-edit { color: var(--preto); border-color: #ccc; }
        .btn-edit:hover { background: #eee; }
        .btn-delete { color: var(--vermelho); border-color: var(--vermelho); }
        .btn-delete:hover { background: var(--vermelho); color: var(--branco); }
        .btn-success { background: #28a745; color: white; border-color: #28a745; }
        .btn-success:hover { background: #218838; }

        .grid-campanhas, .grid-galeria { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; margin-top: 20px; }
        .card-campanha, .card-galeria { background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; display: flex; flex-direction: column; }
        .card-campanha .container-img, .card-galeria .container-img { height: 200px; background: #f1f5f9; overflow: hidden; }
        .card-campanha .container-img img, .card-galeria .container-img img { width: 100%; height: 100%; object-fit: cover; }
        .card-campanha .info, .card-galeria .info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; }
        .card-campanha .nome, .card-galeria .titulo { font-weight: bold; margin: 0 0 5px 0; }
        .card-campanha .data, .card-galeria .data { font-size: 0.8em; color: #64748b; margin-bottom: 15px; }
        .card-campanha .acoes, .card-galeria .acoes { display: flex; gap: 10px; margin-top: auto; }
        .card-campanha .acoes .btn, .card-galeria .acoes .btn { flex: 1; text-align: center; padding: 8px; }

        .form-galeria { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e0e0e0; }
        .form-galeria input, .form-galeria textarea, .form-galeria select { width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ccc; }
        .form-galeria .btn { margin-top: 10px; }
        .badge-midia { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.7em; font-weight: bold; }
        .badge-digital { background: #e0f7fa; color: #00695c; }
        .badge-impresso { background: #fff3e0; color: #e65100; }

        .flash-msg { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .flash-msg.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash-msg.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ADMIN</h2>
    <p style="opacity: 0.6; font-size: 0.8em;">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?></p>
    <nav style="margin-top: 20px;">
        <a href="TelaInicial00.php">Ver Site</a>
        <a href="index.php">Sair</a>
    </nav>
</div>

<div class="main-content">
    <h1>Dashboard</h1>

    <?php if (isset($_GET['msg'])): ?>
        <div class="flash-msg success">✅ <?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['erro'])): ?>
        <div class="flash-msg error">❌ <?php echo htmlspecialchars($_GET['erro']); ?></div>
    <?php endif; ?>

    <div class="cards">
        <div class="card"><h3>Usuários</h3><p><?php echo $totalUsuarios; ?></p></div>
        <div class="card"><h3>Campanhas</h3><p><?php echo $totalCampanhas; ?></p></div>
        <div class="card"><h3>Galeria</h3><p><?php echo $totalGaleria; ?></p></div>
    </div>

    <!-- ========================================================= -->
    <!-- GERENCIAR GALERIA                                          -->
    <!-- ========================================================= -->
    <h2 style="margin-top: 50px;">🖼️ Gerenciar Galeria</h2>

    <div class="form-galeria">
        <h3>Adicionar nova imagem</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao_galeria" value="adicionar">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Título *</label>
                    <input type="text" name="titulo" required>
                </div>
                <div>
                    <label>Tipo de Mídia *</label>
                    <select name="tipo_midia" required>
                        <option value="digital">Digital</option>
                        <option value="impresso">Impresso</option>
                    </select>
                </div>
            </div>
            <div>
                <label>Descrição</label>
                <textarea name="descricao" rows="2"></textarea>
            </div>
            <div>
                <label>Imagem (JPG, PNG, etc.) *</label>
                <input type="file" name="imagem" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-add">➕ Adicionar</button>
        </form>
    </div>

    <?php if (empty($galeria)): ?>
        <p style="color: #64748b;">Nenhuma imagem na galeria ainda.</p>
    <?php else: ?>
        <div class="grid-galeria">
            <?php foreach ($galeria as $item): 
                $tipo_class = ($item['tipo_midia'] == 'digital') ? 'badge-digital' : 'badge-impresso';
            ?>
                <div class="card-galeria">
                    <div class="container-img">
                        <img src="img/galeria/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                    </div>
                    <div class="info">
                        <p class="titulo"><?php echo htmlspecialchars($item['titulo']); ?></p>
                        <p style="font-size:0.8em; color:#475569;"><?php echo htmlspecialchars($item['descricao']); ?></p>
                        <p class="data">📅 <?php echo date('d/m/Y H:i', strtotime($item['data_cadastro'])); ?></p>
                        <p><span class="badge-midia <?php echo $tipo_class; ?>"><?php echo strtoupper($item['tipo_midia']); ?></span></p>
                        <div class="acoes">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="acao_galeria" value="editar_tipo">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <select name="tipo_midia" onchange="this.form.submit()" style="padding:4px;">
                                    <option value="digital" <?php if($item['tipo_midia']=='digital') echo 'selected'; ?>>Digital</option>
                                    <option value="impresso" <?php if($item['tipo_midia']=='impresso') echo 'selected'; ?>>Impresso</option>
                                </select>
                            </form>
                            <a href="DashboardAdmin.php?excluir_galeria=<?php echo $item['id']; ?>" class="btn btn-delete" onclick="return confirm('Eliminar esta imagem?')">❌ Apagar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ========================================================= -->
    <!-- UTILIZADORES                                               -->
    <!-- ========================================================= -->
    <h2 style="margin-top: 50px;">👥 Utilizadores</h2>
    <a href="adicionar_usuario.php" class="btn btn-add">+ ADICIONAR NOVO USUÁRIO</a>

    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="pesquisa" placeholder="Pesquisar utilizador..." value="<?php echo htmlspecialchars($_GET['pesquisa'] ?? ''); ?>" style="padding: 10px; width: 300px; border: 1px solid #ccc;">
        <button type="submit" class="btn">Pesquisar</button>
    </form>

    <?php if ($usuarios && $usuarios->num_rows > 0): ?>
    <table>
        <thead><tr><th>Nome</th><th>Email</th><th>Cargo</th><th>Data</th><th>Ações</th></tr></thead>
        <tbody>
            <?php while($user_data = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user_data['nome']); ?></td>
                <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                <td><strong><?php echo strtoupper($user_data['tipo']); ?></strong></td>
                <td><?php echo date('d/m/Y', strtotime($user_data['data_cadastro'])); ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?php echo $user_data['id']; ?>" class="btn btn-edit">EDITAR</a>
                    <?php if($user_data['tipo'] !== 'admin'): ?>
                        <a href="excluir_usuario.php?id=<?php echo $user_data['id']; ?>" class="btn btn-delete" onclick="return confirm('Eliminar este utilizador?')">ELIMINAR</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="color: #64748b;">Nenhum utilizador encontrado.</p>
    <?php endif; ?>

    <!-- ========================================================= -->
    <!-- CAMPANHAS (da base assistente_inteligente)                -->
    <!-- ========================================================= -->
    <h2 style="margin-top: 50px;">📋 Campanhas</h2>
    <?php if (empty($campanhas)): ?>
        <p style="color: #64748b;">Nenhuma campanha criada ainda.</p>
    <?php else: ?>
        <div class="grid-campanhas">
            <?php foreach ($campanhas as $campanha): 
                $nome = !empty($campanha['nome']) ? $campanha['nome'] : (isset($campanha['ideia']) ? $campanha['ideia'] : 'Sem nome');
                $imagem = isset($campanha['imagem']) && !empty($campanha['imagem']) ? $campanha['imagem'] : 'img/Creative-Designs.jpg';
                $data = date('d/m/Y H:i', strtotime($campanha['data_criacao']));
            ?>
                <div class="card-campanha">
                    <div class="container-img">
                        <img src="<?php echo htmlspecialchars($imagem); ?>" alt="Design da campanha">
                    </div>
                    <div class="info">
                        <p class="nome">"<?php echo htmlspecialchars($nome); ?>"</p>
                        <p class="data">📅 <?php echo $data; ?></p>
                        <div class="acoes">
                            <a href="<?php echo htmlspecialchars($imagem); ?>" target="_blank" class="btn btn-edit">👁️ Ver</a>
                            <a href="DashboardAdmin.php?excluir_campanha=<?php echo $campanha['id']; ?>" class="btn btn-delete" onclick="return confirm('Eliminar esta campanha?')">❌ Apagar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
