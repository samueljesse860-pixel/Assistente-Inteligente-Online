<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo "Erro: Sessão não iniciada";
    exit;
}

$apiKey = "AIzaSyCAdMziYotpYLM_lZHbOT626SD-nnsFkFw"; // Sua chave
$input = $_POST['mensagem'] ?? '';

if (empty($input)) {
    echo "Como posso ajudar hoje?";
    exit;
}

// Lista de modelos em ordem de preferência (fallback automático)
$modelos = [
    "gemini-2.0-flash-lite",      // Mais leve, menos sobrecarregado
    "gemini-2.0-flash-001",       // Estável
    "gemini-flash-lite-latest",   // Versão mais recente lite
    "gemini-2.5-flash-lite",      // Versão lite do 2.5
    "gemini-2.0-flash",           // Tentar o padrão
    "gemini-2.5-flash"            // Última tentativa
];

$respostaFinal = null;
$ultimoErro = null;

foreach ($modelos as $modelo) {
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" . $modelo . ":generateContent?key=" . $apiKey;
    
    $data = [
        "contents" => [
            [
                "role" => "user",
                "parts" => [
                    [
                        "text" => "Tu és o ChatMarkt, assistente de campanhas de produtos. Responde em português de Portugal. Pergunta: " . $input
                    ]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 1024
        ]
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    // Verifica se funcionou
    if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $respostaFinal = $result['candidates'][0]['content']['parts'][0]['text'];
        break; // Sai do loop se conseguiu resposta
    } elseif (isset($result['error'])) {
        $ultimoErro = $result['error']['message'];
    }
}

// Retorna a resposta ou erro
if ($respostaFinal) {
    echo $respostaFinal;
} else {
    echo "⚠️ Todos os modelos estão temporariamente ocupados. Por favor, tente novamente em alguns instantes.\n\n";
    echo "Erro: " . $ultimoErro;
}
?>
