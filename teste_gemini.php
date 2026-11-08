<?php
// teste_modelos.php
$apiKey = "AIzaSyCAdMziYotpYLM_lZHbOT626SD-nnsFkFw";
$testeModelos = ["gemini-2.0-flash-lite", "gemini-flash-lite-latest", "gemini-2.0-flash-001"];

foreach ($testeModelos as $modelo) {
    echo "Testando $modelo... ";
    
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $modelo . ":generateContent?key=" . $apiKey;
    $data = ["contents" => [["parts" => [["text" => "Responde apenas: OK"]]]]];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "✓ FUNCIONA!\n";
    } else {
        echo "✗ Falhou (HTTP $httpCode)\n";
    }
}
?>
