<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Buscar dados da campanha
$query = $conexao->query("SELECT * FROM campanhas WHERE id = $campanha_id AND usuario_id = {$_SESSION['usuario_id']}");
$campanha = $query->fetch_assoc();

if (!$campanha) {
    header('Location: gerar_campanhas.php');
    exit;
}

// Configurações da API (coloque suas chaves aqui)
$DEEPSEEK_API_KEY = 'sk-0ba4350d8dbd472c8cc600e0cf46527d'; // Sua chave do DeepSeek
$STABILITY_API_KEY = 'sua_chave_stability_aqui'; // Você precisa criar conta em stability.ai

$status_processo = 'iniciado';
$imagem_gerada = '';
$erro = '';

// Função para otimizar prompt com DeepSeek
function otimizarPromptDeepSeek($ideia, $apiKey) {
    $url = 'https://api.deepseek.com/v1/chat/completions';
    
    $data = [
        'model' => 'deepseek-chat',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Você é um especialista em criar prompts detalhados para geração de imagens com IA. Transforme a ideia do usuário em um prompt profissional em INGLÊS.'
            ],
            [
                'role' => 'user',
                'content' => "Crie um prompt detalhado em inglês para gerar uma imagem de marketing baseada nesta ideia: {$ideia}. O prompt deve descrever cena, cores, estilo, composição e sentimento."
            ]
        ],
        'temperature' => 0.7
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? $ideia;
}

// Função para gerar imagem (usando Stability AI como exemplo)
function gerarImagemStability($prompt, $apiKey) {
    $url = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image';
    
    $data = [
        'text_prompts' => [
            ['text' => $prompt, 'weight' => 1]
        ],
        'cfg_scale' => 7,
        'height' => 1024,
        'width' => 1024,
        'samples' => 1,
        'steps' => 30
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result['artifacts'][0]['base64'])) {
        return base64_decode($result['artifacts'][0]['base64']);
    }
    
    return false;
}

// Função para publicar no Facebook
function publicarFacebook($imagemBinaria, $legenda, $accessToken, $pageId) {
    // Salvar imagem temporariamente
    $tempFile = tempnam(sys_get_temp_dir(), 'fb_') . '.png';
    file_put_contents($tempFile, $imagemBinaria);
    
    $url = "https://graph.facebook.com/v18.0/{$pageId}/photos";
    
    $postData = [
        'source' => new CURLFile($tempFile),
        'caption' => $legenda . "\n\n#GeradoPorIA #CampanhaMarketing",
        'access_token' => $accessToken
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    unlink($tempFile);
    
    return json_decode($response, true);
}

// Executar o processo
try {
    // Passo 1: Otimizar prompt com DeepSeek
    $status_processo = 'Otimizando sua ideia com DeepSeek...';
    $promptOtimizado = otimizarPromptDeepSeek($campanha['ideia_criativa'], $DEEPSEEK_API_KEY);
    
    // Salvar prompt otimizado no banco
    $conexao->query("UPDATE campanhas SET prompt_otimizado = '" . $conexao->real_escape_string($promptOtimizado) . "' WHERE id = $campanha_id");
    
    // Passo 2: Gerar imagem
    $status_processo = 'Gerando imagem com IA...';
    $imagemBinaria = gerarImagemStability($promptOtimizado, $STABILITY_API_KEY);
    
    if (!$imagemBinaria) {
        throw new Exception('Falha ao gerar imagem');
    }
    
    // Salvar imagem no servidor
    $pastaImagens = 'uploads/campanhas/';
    if (!file_exists($pastaImagens)) {
        mkdir($pastaImagens, 0777, true);
    }
    
    $nomeImagem = 'campanha_' . $campanha_id . '_' . date('Ymd_His') . '.png';
    $caminhoImagem = $pastaImagens . $nomeImagem;
    file_put_contents($caminhoImagem, $imagemBinaria);
    
    // Atualizar banco com caminho da imagem
    $conexao->query("UPDATE campanhas SET imagem_gerada = '$caminhoImagem' WHERE id = $campanha_id");
    
    // Passo 3: Publicar no Facebook (se configurado)
    $facebook_publicado = 0;
    $facebook_post_id = null;
    
    // Configurações do Facebook (coloque suas credenciais)
    $FB_ACCESS_TOKEN = 'SEU_TOKEN_DE_ACESSO_FACEBOOK';
    $FB_PAGE_ID = 'ID_DA_SUA_PAGINA';
    
    if ($FB_ACCESS_TOKEN != 'SEU_TOKEN_DE_ACESSO_FACEBOOK' && !empty($campanha['descricao'])) {
        $status_processo = 'Publicando no Facebook...';
        $resultadoFacebook = publicarFacebook($imagemBinaria, $campanha['descricao'], $FB_ACCESS_TOKEN, $FB_PAGE_ID);
        
        if (isset($resultadoFacebook['id'])) {
            $facebook_publicado = 1;
            $facebook_post_id = $resultadoFacebook['id'];
            $conexao->query("UPDATE campanhas SET facebook_publicado = 1, facebook_post_id = '$facebook_post_id' WHERE id = $campanha_id");
        }
    }
    
    // Atualizar status final
    $conexao->query("UPDATE campanhas SET status = 'ativa', data_processamento = NOW() WHERE id = $campanha_id");
    
    $_SESSION['sucesso'] = "✅ Campanha processada com sucesso! Imagem gerada" . ($facebook_publicado ? " e publicada no Facebook!" : "!");
    header("Location: ver_campanha.php?id=$campanha_id");
    
} catch (Exception $e) {
    $erro = $e->getMessage();
    $conexao->query("UPDATE campanhas SET status = 'erro' WHERE id = $campanha_id");
}
// ==============================================
// CONFIGURAÇÃO QUE FUNCIONA SEM REVIEW
// ==============================================

// 1. Cria uma app no Facebook Developers
// 2. Adiciona-te como Administrador/Desenvolvedor
// 3. Gera um Page Access Token (vai expirar, mas podes renovar)

$FB_PAGE_ID = ' 61590057286302';  // ID numérico da tua página
$FB_PAGE_ACCESS_TOKEN = 'EAAMa6ADYlBQBRi6mRopWIwDhiahFekcvsFkGpHA9DVHeoECJxUinZAl5VARyYlQUZBYjbNEh3l5xeXnkz7ZCGGCsCBNPU9hrHL7ZB9vpZCwRjUELy9EV97jnsSdROZA9cJjfZC1BZAZCRByLXzXLwVYOaXEMNY6WoLudtZCKiKA3jZAlfQFRR1xUhkcX3TnSnJLpeIHAExR6qKfik9HfiaK2sadi2TeIehXN0ZCgyHcTkjwrvrR3o02N4vtwrPnLiW4EPJAR57qNRzIwgueh4ptE4wSIQhVdOj4agZAwDZCw6EySJbDCvRwDEbQYs3BhFjpe9g1nLJdSleoVqt4EggmgZDZD';  // Token da página


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Processando Campanha - Assistente IA</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .processador {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .status {
            color: #666;
            margin: 20px 0;
        }
        
        .erro {
            color: #e74c3c;
            background: #fee;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
    <meta http-equiv="refresh" content="3;url=ver_campanha.php?id=<?php echo $campanha_id; ?>">
</head>
<body>
    <div class="processador">
        <h2>🎨 Processando sua campanha</h2>
        
        <?php if($erro): ?>
            <div class="erro">
                <strong>❌ Erro:</strong> <?php echo $erro; ?>
                <br><br>
                <a href="gerar_campanhas.php" class="btn">Voltar</a>
            </div>
        <?php else: ?>
            <div class="spinner"></div>
            <div class="status"><?php echo $status_processo; ?></div>
            <p>Aguarde, estamos usando IA para criar algo incrível...</p>
        <?php endif; ?>
    </div>
</body>
</html>
