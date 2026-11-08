<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conexao = mysqli_connect("localhost", "root", "");

if ($conexao) {
    mysqli_query($conexao, "CREATE DATABASE IF NOT EXISTS assistente_inteligente");
    mysqli_select_db($conexao, "assistente_inteligente");
    
    $criar_tabela = "CREATE TABLE IF NOT EXISTS campanhas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NULL,
        ideia VARCHAR(255) NULL,
        imagem TEXT NOT NULL,
        url TEXT NULL,
        data_criacao DATETIME NOT NULL
    )";
    mysqli_query($conexao, $criar_tabela);
}

// 2. AÇÃO DE ELIMINAR CAMPANHA (Apaga do banco e remove o arquivo da pasta)
if (isset($_GET['excluir']) && $conexao) {
    $id_excluir = (int)$_GET['excluir'];
    
    // Busca o caminho do arquivo para apagá-lo do computador também
    $busca_img = mysqli_query($conexao, "SELECT imagem FROM campanhas WHERE id = $id_excluir");
    if ($busca_img && $dados_img = mysqli_fetch_assoc($busca_img)) {
        $arquivo_deletar = $dados_img['imagem'];
        if (file_exists($arquivo_deletar) && strpos($arquivo_deletar, 'imagens_salvas/') !== false) {
            unlink($arquivo_deletar); // Deleta o arquivo físico .jpg
        }
    }
    
    mysqli_query($conexao, "DELETE FROM campanhas WHERE id = $id_excluir");
    header("Location: GerenciarCampanhas.php");
    exit;
}

// 3. BUSCA AS CAMPANHAS SALVAS
$campanhas = [];
if ($conexao) {
    $resultado = mysqli_query($conexao, "SELECT * FROM campanhas ORDER BY data_criacao DESC");
    if ($resultado) {
        while ($linha = mysqli_fetch_assoc($resultado)) {
            $campanhas[] = $linha;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel - Gerenciar Campanhas</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #fafafa; color: #222; padding: 40px 20px; }
        .header-painel { max-width: 1200px; margin: 0 auto 40px auto; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
        h1 { color: #001f54; margin: 0; font-size: 28px; }
        .btn-voltar { padding: 12px 20px; background: #007bff; color: white; text-decoration: none; font-weight: bold; border-radius: 8px; }
        .grid-campanhas { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
        .card-campanha { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; border: 1px solid #e2e8f0; }
        .container-img { width: 100%; height: 350px; background: #f1f5f9; overflow: hidden; }
        .container-img img { width: 100%; height: 100%; object-fit: cover; }
        .info-campanha { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .nome-campanha { font-size: 16px; font-weight: bold; color: #1e293b; margin: 0 0 10px 0; }
        .data-campanha { font-size: 13px; color: #64748b; margin-bottom: 15px; }
        .acoes-card { margin-top: auto; display: flex; flex-direction: column; gap: 10px; }
        .linha-botoes { display: flex; gap: 10px; }
        .btn-card { flex: 1; padding: 10px; text-align: center; text-decoration: none; font-weight: bold; font-size: 13px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; border: none; }
        .btn-ver { background: #f1f5f9; color: #475569; }
        .btn-excluir { background: #fee2e2; color: #ef4444; }
        .btn-facebook { background: #1877F2; color: white; width: 100%; }
        .btn-facebook svg { width: 16px; height: 16px; fill: white; }
        .sem-campanhas { text-align: center; grid-column: 1 / -1; padding: 60px; background: white; border-radius: 15px; border: 2px dashed #cbd5e1; color: #64748b; }
    </style>
</head>
<body>

    <div class="header-painel">
        <div>
            <h1>📋 Painel de Campanhas Guardadas</h1>
            <p style="color: #64748b; margin: 5px 0 0 0;">Histórico de designs profissionais criados</p>
        </div>
        <a href="Telainicial00.php" class="btn-voltar">← Voltar</a>
    </div>

    <div class="grid-campanhas">
        <?php if (empty($campanhas)): ?>
            <div class="sem-campanhas">
                <h2>Ainda não criou nenhuma campanha.</h2>
                <p>Volte ao painel inicial para testar as suas ideias de marketing!</p>
            </div>
        <?php else: ?>
            <?php foreach ($campanhas as $campanha): 
                $nomeFinal = isset($campanha['nome']) && !empty($campanha['nome']) ? $campanha['nome'] : (isset($campanha['ideia']) ? $campanha['ideia'] : 'Campanha Sem Nome');
                $linkImagem = isset($campanha['imagem']) && !empty($campanha['imagem']) ? $campanha['imagem'] : (isset($campanha['url']) ? $campanha['url'] : 'img/Creative-Designs.jpg');
                $idCampanha = isset($campanha['id']) ? $campanha['id'] : 0;
                $dataRegistro = isset($campanha['data_criacao']) ? date('d/m/Y \à\s H:i', strtotime($campanha['data_criacao'])) : date('d/m/Y');
            ?>
                <div class="card-campanha">
                    <div class="container-img">
                        <img src="<?php echo htmlspecialchars($linkImagem); ?>" alt="Design da Campanha">
                    </div>
                    <div class="info-campanha">
                        <p class="nome-campanha">"<?php echo htmlspecialchars($nomeFinal); ?>"</p>
                        <div class="data-campanha">
                            📅 <?php echo $dataRegistro; ?>
                        </div>
                        
                        <div class="acoes-card">
                            <button class="btn-card btn-facebook" onclick="partilharNoFacebook('<?php echo urlencode($linkImagem); ?>')">
                                <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                Publicar no Facebook
                            </button>

                            <div class="linha-botoes">
                                <a href="<?php echo htmlspecialchars($linkImagem); ?>" target="_blank" class="btn-card btn-ver">👁️ Ver</a>
                                <a href="GerenciarCampanhas.php?excluir=<?php echo $idCampanha; ?>" onclick="return confirm('Tem a certeza que deseja eliminar esta campanha?');" class="btn-card btn-excluir">❌ Apagar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    function partilharNoFacebook(urlImagem) {
        const urlCompartilhar = "https://www.facebook.com/sharer/sharer.php?u=" + window.location.origin + "/" + urlImagem;
        window.open(urlCompartilhar, 'facebook-share-dialog', 'width=626,height=436');
    }
    </script>
</body>
</html>
