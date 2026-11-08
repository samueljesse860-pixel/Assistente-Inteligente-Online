<?php
// Ativa a execução e exibição de erros para controlo no PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. ESCUTA A REQUISIÇÃO PARA GUARDAR NO BANCO (HISTÓRICO)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'salvar') {
    try {
        $conexao = mysqli_connect("localhost", "root", "", "assistente_inteligente"); 
        
        if ($conexao) {
            $nome_campanha = mysqli_real_escape_string($conexao, $_POST['nome']);
            $url_api_ia = $_POST['imagem']; // URL vinda da API
            $data_criacao = date('Y-m-d H:i:s');
            
            // Cria um nome de arquivo único para evitar imagens duplicadas
            $nome_arquivo = 'campanha_' . time() . '_' . rand(1000, 9999) . '.jpg';
            $caminho_local = 'imagens_salvas/' . $nome_arquivo;
            
            // Faz o download físico da imagem da IA para o seu servidor local
            $conteudo_imagem = @file_get_contents($url_api_ia);
            
            if ($conteudo_imagem !== false) {
                // Salva o arquivo .jpg na pasta local
                file_put_contents($caminho_local, $conteudo_imagem);
                
                // Grava no banco de dados o caminho do arquivo local e não o link da API
                $caminho_banco = mysqli_real_escape_string($conexao, $caminho_local);
                $query = "INSERT INTO campanhas (nome, imagem, data_criacao) VALUES ('$nome_campanha', '$caminho_banco', '$data_criacao')";
                mysqli_query($conexao, $query);
            }
            
            mysqli_close($conexao);
        }
    } catch (Exception $e) {}
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Assistente IA - Estúdio de Design Premium</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #1a1a1a; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .container { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 15px 40px rgba(0,0,0,0.08); text-align: center; width: 100%; max-width: 650px; border: 1px solid #e1e8ed; }
        h1 { font-size: 32px; color: #001f54; margin-top: 0; font-weight: 800; }
        .ideia-box { background: #f8fafc; padding: 18px; border-radius: 14px; font-size: 16px; color: #475569; margin: 25px 0 35px 0; border-left: 5px solid #2563eb; text-align: left; }
        .loading-wrapper { display: flex; flex-direction: column; align-items: center; gap: 25px; }
        .spinner { width: 60px; height: 60px; border: 6px solid #e2e8f0; border-top: 6px solid #2563eb; border-radius: 50%; animation: girar 0.8s infinite linear; }
        @keyframes girar { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .campanha-img { width: 100%; height: auto; max-height: 580px; object-fit: cover; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,0.15); margin-bottom: 30px; background: #f1f5f9; border: 2px solid #e1e8ed; }
        .btn-group { display: flex; flex-wrap: wrap; gap: 14px; justify-content: center; }
        .btn { padding: 15px 25px; border: none; border-radius: 12px; font-weight: bold; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; transition: all 0.2s ease; }
        .btn:hover { transform: translateY(-3px); opacity: 0.9; }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .btn-download { background: #28a745; color: white; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; }
        .resultado-wrapper { display: none; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Gerador de Design Profissional</h1>
        
        <div class="ideia-box">
            Conceito do Cliente: <strong id="ideia-texto">A ler proposta...</strong>
        </div>

        <div id="loading-area" class="loading-wrapper">
            <div class="spinner"></div>
            <p id="status-mensagem" style="color: #64748b; font-weight: 500;">A Inteligência Artificial está a desenhar a campanha em segundos...</p>
        </div>

        <div id="resultado-area" class="resultado-wrapper">
            <img id="imagem-generada" class="campanha-img" src="" alt="Campanha Inteligente Criada por IA">
            
            <div class="btn-group">
                <a href="Telainicial00.php" class="btn btn-secondary">📋 Novo Conceito</a>
                <a href="GerenciarCampanhas.php" class="btn btn-download">📊 Ver Painel / Salvas</a>
            </div>
        </div>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        let idiaPrompt = urlParams.get('ideia') || localStorage.getItem("ideiaCampanha");
        
        const ideiaTexto = document.getElementById("ideia-texto");
        const loadingArea = document.getElementById("loading-area");
        const resultadoArea = document.getElementById("resultado-area");
        const imagemElemento = document.getElementById("imagem-generada");

        if (!idiaPrompt || idiaPrompt.trim() === "") {
            window.location.href = "Cadastro.php";
        } else {
            ideiaTexto.innerText = `"${idiaPrompt}"`;
            chamarApiDesign(idiaPrompt);
        }

        function chamarApiDesign(promptTexto) {
            loadingArea.style.display = "flex";
            resultadoArea.style.display = "none";

            let instrucaoFiltrada = promptTexto.toLowerCase()
                .replace("marketing de", "")
                .replace("propaganda de", "")
                .replace("anúncio de", "")
                .trim();

            const comandoIA = `Professional studio product photography of ${instrucaoFiltrada}, commercial advertising poster style, clean background, 8k, highly intelligent design`;
            const seedAleatorio = Math.floor(Math.random() * 999999);

            // Mantém exatamente o modelo TURBO que você quer utilizar
            const urlGeradorReal = "https://image.pollinations.ai/p/" + encodeURIComponent(comandoIA) + "?width=768&height=1024&nologo=true&private=true&model=turbo&seed=" + seedAleatorio;

            imagemElemento.src = urlGeradorReal;
            
            imagemElemento.onload = function() {
                loadingArea.style.display = "none";
                resultadoArea.style.display = "block";
                // Envia para o PHP fazer o download físico da imagem exata
                salvarCampanhaNoBanco(promptTexto, urlGeradorReal);
            };

            imagemElemento.onerror = function() {
                imagemElemento.src = "https://image.pollinations.ai/p/" + encodeURIComponent(instrucaoFiltrada) + "?width=768&height=1024&model=turbo&seed=" + seedAleatorio;
            };
        }

        function mudarVariacao() {
            chamarApiDesign(idiaPrompt);
        }

        function salvarCampanhaNoBanco(nome, linkImagem) {
            const formData = new FormData();
            formData.append('acao', 'salvar');
            formData.append('nome', nome);
            formData.append('imagem', linkImagem);
            
            fetch(window.location.pathname, { method: 'POST', body: formData })
            .then(res => console.log("Imagem descarregada e salva localmente."))
            .catch(err => console.error("Erro ao salvar:", err));
        }
    </script>
</body>
</html>
