<?php
// enviar_email.php - Motor de Exportação Office
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'] ?? 'Campanha';
    $imgRelativa = $_POST['imagem'] ?? ''; // Ex: "img/foto.jpg"
    $formato = $_POST['formato'] ?? 'word';

    // Garante que o caminho é absoluto desde C:\xampp\htdocs\...
    $caminhoNoDisco = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imgRelativa);

    $imagemBase64 = '';
    if (file_exists($caminhoNoDisco)) {
        $dados = file_get_contents($caminhoNoDisco);
        $tipo = pathinfo($caminhoNoDisco, PATHINFO_EXTENSION);
        $imagemBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($dados);
    }

    $ext = ($formato === 'word') ? 'doc' : 'ppt';
    header("Content-Type: application/vnd.ms-$ext");
    header("Content-Disposition: attachment; filename=briefing_" . time() . "." . $ext);

    echo "<html><head><meta charset='utf-8'></head><body>";
    echo "<h1>" . htmlspecialchars($titulo) . "</h1>";
    
    if ($imagemBase64) {
        echo "<img src='$imagemBase64' width='600' />";
    } else {
        echo "<p style='color:red;'>Erro: O servidor não encontrou a imagem em: " . htmlspecialchars($caminhoNoDisco) . "</p>";
    }
    
    echo "</body></html>";
}
?>
