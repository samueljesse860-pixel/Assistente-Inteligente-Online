<?php
$DEEPSEEK_API_KEY = 'sk-0ba4350d8dbd472c8cc600e0cf46527d';
$ideia = "Uma praia tropical com coqueiros, pôr do sol laranja";

function testarDeepSeek($ideia, $apiKey) {
    $url = 'https://api.deepseek.com/v1/chat/completions';
    
    $data = [
        'model' => 'deepseek-chat',
        'messages' => [
            ['role' => 'system', 'content' => 'Você é um especialista em prompts para IA de imagens.'],
            ['role' => 'user', 'content' => "Crie um prompt detalhado em inglês para gerar esta imagem: {$ideia}"]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

$resultado = testarDeepSeek($ideia, $DEEPSEEK_API_KEY);
echo "<h2>Teste do DeepSeek:</h2>";
echo "<pre>";
print_r($resultado);
echo "</pre>";

// Teste de imagem grátis
$imagemURL = "https://image.pollinations.ai/prompt/" . urlencode("beach sunset palm trees beautiful");
echo "<h2>Teste de imagem grátis:</h2>";
echo "<img src='$imagemURL' width='300'>";
?>
