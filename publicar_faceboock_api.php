<?php
// ==============================================
// CONFIGURAÇÕES - SUBSTITUIR PELOS TEUS DADOS
// ==============================================

$FB_PAGE_ID = '2505738363230023';  // ID numérico da tua página
$FB_PAGE_ACCESS_TOKEN = 'EAAMa6ADYlBQBRow0NxBUO6jFS2lWy5xOj9fevsBCngjPVp7syxqTrnponhWAftFE6oXahRqzij7eoqixcZAGwFjtNkYInMMQh6hnMql69x7is7035ViCTdFOjGBDK2PZAitQwmGhpua9d1hkv5JVJEsg8EJImYZCB0JzpXpbdsbARHMQYebYSY2FDFrwiFR6kCfnbWZCt9ficr89GL4KDU0Sd345f9AoGWYi6wKUzWl4TAPZBNHwIPVZByuXfZB2ffKy6HjM8zCFeTViGqXE9JXggXgzAbTqbzBFZBdvQ2VaCti3kgZDZD';

';   // ← Token de acesso da página

// ==============================================
// FUNÇÃO PARA PUBLICAR AUTOMATICAMENTE
// ==============================================

header('Content-Type: application/json');

// Verificar se veio imagem
if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'erro' => 'Nenhuma imagem enviada']);
    exit;
}

$legenda = $_POST['legenda'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$textoCompleto = $legenda . "\n\n" . $descricao . "\n\n#PublicadoAutomaticamente #IA";

$imagemTemp = $_FILES['imagem']['tmp_name'];

// Publicar no Facebook
function publicarNoFacebook($imagemPath, $legenda, $pageId, $accessToken) {
    // Usar CURLFile para enviar a imagem
    $imagem = new CURLFile($imagemPath, mime_content_type($imagemPath), 'imagem.jpg');
    
    $url = "https://graph.facebook.com/v18.0/{$pageId}/photos";
    
    $postData = [
        'source' => $imagem,
        'caption' => $legenda,
        'access_token' => $accessToken
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $dados = json_decode($response, true);
        return [
            'success' => true,
            'post_id' => $dados['id'],
            'post_url' => "https://facebook.com/{$dados['id']}"
        ];
    }
    
    return [
        'success' => false,
        'erro' => "HTTP $httpCode: " . $response
    ];
}

// Executar publicação
$resultado = publicarNoFacebook($imagemTemp, $textoCompleto, $FB_PAGE_ID, $FB_PAGE_ACCESS_TOKEN);

echo json_encode($resultado);
?>
