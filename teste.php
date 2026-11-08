<?php
// lista_modelos.php - Descobre quais modelos estão disponíveis

$apiKey = "AIzaSyCAdMziYotpYLM_lZHbOT626SD-nnsFkFw"; // SUA CHAVE

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código HTTP: " . $httpCode . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Modelos disponíveis:\n";
    echo "==================\n\n";
    
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'gemini') !== false) {
            echo "• " . $model['name'] . "\n";
            echo "  Métodos suportados: " . implode(', ', $model['supportedGenerationMethods']) . "\n\n";
        }
    }
} else {
    echo "Erro: " . $response;
}
?>
