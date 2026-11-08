<?php
// Conectar à base para buscar imagens da galeria
$dbHost = '127.0.0.1';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'assistente_online';

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
if ($conexao->connect_error) { die("Falha na ligação: " . $conexao->connect_error); }
$conexao->set_charset("utf8mb4");

// Buscar todas as imagens da galeria
$galeria = [];
$result = $conexao->query("SELECT * FROM galeria ORDER BY data_cadastro DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $galeria[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assistente Inteligente Online</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
/* (todo o CSS permanece IDÊNTICO ao que já tinha) */
body {
  margin: 0;
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: #f8fafc; 
  -webkit-font-smoothing: antialiased;
}
.modal-close { 
    position: absolute; top: 15px; right: 15px; z-index: 9999; 
    background: #001133; color: white; border: none; 
    width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
}
.premium-modal { z-index: 10000; }
.hero-top {
    position: relative;
    background-color: #0a0a0a;
    height: 60vh; 
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: flex-start; 
    text-align: left;
    overflow: hidden;
}
.hero-top .overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 40%, transparent 100%);
    z-index: 10;
    pointer-events: none;
}
.hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background-image: url('img/hhh.avif');
    background-size: cover;
    background-position: center;
    z-index: 1;
    transition: transform 4000ms ease;
}
.hero-top:hover .hero-bg {
    transform: scale(1.03);
}
.hero-content {
    position: relative;
    z-index: 20;
    color: #ffffff;
    max-width: 44rem; 
    width: 100%;
    box-sizing: border-box;
    padding: 24px 24px 24px 32px; 
}
@media (min-width: 640px) { .hero-content { padding-left: 48px; } }
@media (min-width: 1024px) { .hero-content { padding-left: 80px; } }
.hero-tag {
    font-size: 11px;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    font-weight: 800;
    color: #e5e5e5;
    display: block;
    margin-bottom: 16px;
}
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.25rem;
    font-weight: 300;
    font-style: italic;
    letter-spacing: 0.02em;
    line-height: 1.1;
    margin: 0 0 16px 0;
    color: #ffffff;
}
@media (min-width: 640px) { .hero-title { font-size: 3.75rem; } }
.hero-desc {
    font-size: 12px;
    letter-spacing: 0.025em;
    color: #e5e5e5;
    max-width: 28rem;
    line-height: 1.625;
    margin: 0 0 28px 0;
    font-weight: 500;
}
@media (min-width: 640px) { .hero-desc { font-size: 14px; } }
.hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #ffffff;
    color: #0a0a0a;
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 16px 24px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
}
.hero-btn:hover { background-color: #e5e5e5; }
.hero-btn span { font-size: 18px; font-weight: 300; }
.input-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    margin: 60px 0;
    padding: 0 20px;
}
.search-row {
    display: flex;
    width: 100%;
    max-width: 650px;
    gap: 0px; 
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}
.search-row input {
    flex: 1;
    padding: 16px 20px;
    border: 1px solid #e2e8f0;
    border-right: none;
    font-size: 15px;
    outline: none;
    font-family: inherit;
    background: #ffffff;
}
.search-row button {
    padding: 0 28px;
    border: none;
    background: #001133; 
    color: white;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s ease;
}
.search-row button:hover { background: #002266; }
.buttons-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
}
.btn-prof {
    padding: 12px 24px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    color: #334155;
    font-weight: 600;
    font-size: 13px;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-prof:hover {
    background: #f1f5f9;
    color: #001133;
    border-color: #cbd5e1;
}
.main-quote-banner {
    max-width: 1200px;
    margin: 0 auto 40px auto;
    padding: 0 30px;
    box-sizing: border-box;
}
.quote-box-premium {
    background: linear-gradient(135deg, #001133 0%, #002266 100%);
    color: #ffffff;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-style: italic;
    box-shadow: 0 10px 30px rgba(0,17,51,0.15);
}
.filter-section-bar {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-sizing: border-box;
}
.filter-title-section {
    font-size: 20px;
    font-weight: 800;
    text-transform: uppercase;
    color: #001133;
    letter-spacing: -0.5px;
}
.filter-nav-buttons { display: flex; gap: 8px; }
.filter-nav-btn {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 20px;
    cursor: pointer;
    color: #64748b;
    transition: all 0.2s ease;
}
.filter-nav-btn.active, .filter-nav-btn:hover {
    background: #001133;
    color: #ffffff;
    border-color: #001133;
}
.produtos {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
    gap: 30px;
    padding: 30px;
    box-sizing: border-box;
}
@media(max-width: 640px) {
    .produtos { grid-template-columns: 1fr; padding: 15px; }
}
.card {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    cursor: pointer;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    height: 400px;
}
.card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.card:hover img { transform: scale(1.02); }
.motivation-grid-box {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    color: #475569;
    font-style: italic;
    box-sizing: border-box;
    height: 400px;
}
.card-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 11, 51, 0.88); 
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
    padding: 20px;
    text-align: center;
    z-index: 5;
}
.card:hover .card-overlay { opacity: 1; }
.card-overlay h4 { color: #ffffff; margin: 0 0 5px 0; font-size: 18px; font-weight: 700; }
.overlay-btn {
    width: 80%;
    max-width: 180px;
    padding: 10px 16px;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 0.05em;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-view { background: #ffffff; color: #001133; }
.btn-view:hover { background: #e2e8f0; }
.btn-ia { background: #00ff95; color: #001133; }
.btn-ia:hover { background: #00cc78; }
.btn-exp { background: transparent; color: #ffffff; border: 1px solid rgba(255,255,255,0.3); }
.btn-exp:hover { background: rgba(255,255,255,0.1); }
.media-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(0, 17, 51, 0.85);
    color: #ffffff;
    padding: 6px 14px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 20px;
    z-index: 4;
}
.premium-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(5px);
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}
.premium-modal.active { opacity: 1; pointer-events: auto; }
.modal-wrapper {
    background: #ffffff;
    width: 90%;
    max-width: 850px;
    display: flex;
    flex-direction: row;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #001133;
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.modal-left { width: 50%; background: #f1f5f9; display: flex; align-items: center; }
.modal-left img { width: 100%; height: 500px; object-fit: cover; }
.modal-right { width: 50%; padding: 40px; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;}
.modal-meta-tag { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; font-weight: 800; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; display: block; margin-bottom: 15px;}
.modal-camp-title { font-family: 'Playfair Display', serif; font-size: 26px; font-style: italic; color: #001133; margin: 0 0 12px 0; }
.modal-camp-desc { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; }
.modal-stats { background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
.modal-stats-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; font-weight: 600; color: #334155; }
.modal-stats-row span:last-child { color: #28a745; font-weight: 800; }
.modal-action-btn { background: #001133; color: white; border: none; padding: 16px; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; width: 100%; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
.modal-action-btn:hover { background: #002266; }
@media(max-width: 640px) {
    .modal-wrapper { flex-direction: column; }
    .modal-left, .modal-right { width: 100%; }
    .modal-left img { height: 280px; }
    .modal-right { padding: 25px; }
}
footer {
    background: #001133;
    color: #ffffff;
    padding: 80px 40px 30px 40px;
    border-top: 4px solid #00ff95;
    margin-top: 60px;
}
.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
}
.footer-column h5 {
    color: #00ff95;
    font-size: 14px;
    font-weight: 800;
    margin: 0 0 25px 0;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}
.footer-column ul { list-style: none; padding: 0; margin: 0; }
.footer-column ul li { margin-bottom: 14px; }
.footer-column ul li a { color: #cbd5e1; text-decoration: none; font-size: 14px; transition: color 0.2s ease; }
.footer-column ul li a:hover { color: #ffffff; text-decoration: underline; }
.footer-bottom { max-width: 1200px; margin: 60px auto 0 auto; padding-top: 25px; border-top: 1px solid rgba(255, 255, 255, 0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.footer-links-base { display: flex; gap: 20px; }
.footer-links-base a { color: #94a3b8; text-decoration: none; font-size: 13px; }
.footer-links-base a:hover { color: #ffffff; }
.footer-title { color: #94a3b8; font-size: 13px; }
@media (max-width: 768px) {
    .footer-container { grid-template-columns: 1fr; gap: 30px; }
    .footer-bottom { flex-direction: column; text-align: center; }
}
</style>
</head>

<body>

<section class="hero-top">
    <div class="overlay"></div>
    <div class="hero-bg"></div>
    <div class="hero-content">
        <span class="hero-tag">Exclusivo Online</span>
        <h1 class="hero-title">Assistente Inteligente Online</h1>
        <p class="hero-desc">Cria campanhas profissionais com inteligência artificial de ponta e máxima criatividade para o mercado global.</p>
        <a href="#ideia" class="hero-btn">Começar Campanhas <span>→</span></a>
    </div>
</section>

<div class="input-area">
    <div class="search-row">
        <input type="text" id="ideia" placeholder="Digite sua ideia de marketing...">
        <button onclick="irGerar()">Criar Campanha</button>
    </div>

    <div class="buttons-row">
        <a href="GerenciarCampanhas.php" class="btn-prof">📋 Campanhas</a>
        <a href="ChatMarkt.php" class="btn-prof" onclick="abrirChat()">ChatMarkt</a>
        <a href="gcr.php" class="btn-prof" onclick="abrirGCR()">GP</a>
        <a href="mapa.php" class="btn-prof" onclick="abrirMapa()">GRV</a>
         <a href="index.php" class="btn-prof" onclick="()">Sair do sistema</a>
    </div>
</div>

<div class="main-quote-banner">
    <div class="quote-box-premium">
        "Grandes campanhas nascem de pequenas ideias bem executadas."
    </div>
</div>

<div class="filter-section-bar">
    <span class="filter-title-section" id="filter-status-text">Galeria de Criatividade</span>
    <div class="filter-nav-buttons">
        <button class="filter-nav-btn active" id="btn-flt-all" onclick="filtrarMidia('todas')">Tudo</button>
        <button class="filter-nav-btn" id="btn-flt-dig" onclick="filtrarMidia('digital')">Digital</button>
        <button class="filter-nav-btn" id="btn-flt-imp" onclick="filtrarMidia('impresso')">Impresso</button>
    </div>
</div>

<div class="produtos" id="galeria-container">
    <!-- ========================================================= -->
    <!-- CARDS FIXOS (IMAGENS QUE JÁ ESTAVAM NO CÓDIGO)            -->
    <!-- ========================================================= -->
    <div class="card" data-midia="digital">
        <span class="media-badge">Digital</span>
        <img src="img/Creative-Designs.jpg">
        <div class="card-overlay">
            <h4>Creative Designs Pack</h4>
            <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('img/Creative-Designs.jpg', 'Creative Designs Pack', 'Pack completo de criativos otimizados para conversão em redes sociais.', 'Digital', '94%')">Visualizar</button>
            <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
            <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('Creative Designs Pack', 'img/Creative-Designs.jpg')">Exportar</button>
        </div>
    </div>

    <div class="motivation-grid-box" data-type="motivation">
        "Marketing não vende produtos, vende emoções."
    </div>

    <div class="card" data-midia="digital">
        <span class="media-badge">Digital</span>
        <img src="img/Setembro Amarelo.jpg">
        <div class="card-overlay">
            <h4>Campanha Consciencialização</h4>
            <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('img/Setembro Amarelo.jpg', 'Campanha Consciencialização', 'Peça institucional focada em empatia e engajamento orgânico.', 'Digital', '89%')">Visualizar</button>
            <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
            <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('Campanha Consciencialização', 'img/Setembro Amarelo.jpg')">Exportar</button>
        </div>
    </div>
    
    <div class="card" data-midia="impresso">
        <span class="media-badge">Impresso</span>
        <img src="img/AF_CIG_mulher_259x95mm.jpg">
        <div class="card-overlay">
            <h4>Editorial Alta Costura</h4>
            <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('img/AF_CIG_mulher_259x95mm.jpg', 'Editorial Alta Costura', 'Layout premium configurado para revistas de moda físicas.', 'Impresso', '96%')">Visualizar</button>
            <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
            <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('Editorial Alta Costura', 'img/AF_CIG_mulher_259x95mm.jpg')">Exportar</button>
        </div>
    </div>

    <div class="motivation-grid-box" data-type="motivation">
        "Criatividade é o motor das grandes marcas."
    </div>

    <div class="card" data-midia="digital">
        <span class="media-badge">Digital</span>
        <img src="img/abstract-art-colorful-swirls-abstract-design-abstract-art-abstract-design-abstract-art-free-photo.jpeg">
        <div class="card-overlay">
            <h4>Artes Fluidas Conceituais</h4>
            <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('img/abstract-art-colorful-swirls-abstract-design-abstract-art-abstract-design-abstract-art-free-photo.jpeg', 'Artes Fluidas Conceituais', 'Fundo abstrato e dinâmico ideal para marcas disruptivas.', 'Digital', '82%')">Visualizar</button>
            <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
            <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('Artes Fluidas Conceituais', 'img/abstract-art-colorful-swirls-abstract-design-abstract-art-abstract-design-abstract-art-free-photo.jpeg')">Exportar</button>
        </div>
    </div>
    
    <div class="card" data-midia="impresso">
        <span class="media-badge">Impresso</span>
        <img src="img/s960_Designs_consultation_Profile.jpg">
        <div class="card-overlay">
            <h4>Corporate Identity Look</h4>
            <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('img/s960_Designs_consultation_Profile.jpg', 'Corporate Identity Look', 'Layout de papelaria e portefólio limpo voltado para consultorias.', 'Impresso', '91%')">Visualizar</button>
            <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
            <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('Corporate Identity Look', 'img/s960_Designs_consultation_Profile.jpg')">Exportar</button>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- CARDS DINÂMICOS DA GALERIA (GERIDOS PELO ADMIN)           -->
    <!-- ========================================================= -->
    <?php if (!empty($galeria)): ?>
        <?php foreach ($galeria as $item): 
            $imagem_path = 'img/galeria/' . $item['imagem'];
            $titulo = htmlspecialchars($item['titulo']);
            $descricao = htmlspecialchars($item['descricao']);
            $tipo = $item['tipo_midia'];
            $tipo_label = ($tipo == 'digital') ? 'Digital' : 'Impresso';
        ?>
            <div class="card" data-midia="<?php echo $tipo; ?>">
                <span class="media-badge"><?php echo $tipo_label; ?></span>
                <img src="<?php echo $imagem_path; ?>" alt="<?php echo $titulo; ?>">
                <div class="card-overlay">
                    <h4><?php echo $titulo; ?></h4>
                    <button class="overlay-btn btn-view" onclick="abrirPreviewCampanha('<?php echo $imagem_path; ?>', '<?php echo $titulo; ?>', '<?php echo $descricao; ?>', '<?php echo $tipo_label; ?>', '92%')">Visualizar</button>
                    <button class="overlay-btn btn-ia" onclick="event.stopPropagation(); alert('IA a processar...')">Adaptar via IA</button>
                    <button class="overlay-btn btn-exp" onclick="event.stopPropagation(); exportarImagem('<?php echo $titulo; ?>', '<?php echo $imagem_path; ?>')">Exportar</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal de preview (idêntico ao original) -->
<div id="campanha-modal" class="premium-modal" onclick="fecharPreviewCampanha()">
    <div class="modal-wrapper" onclick="event.stopPropagation()">
        <button class="modal-close" onclick="fecharPreviewCampanha()">✕</button>
        <div class="modal-left">
            <img id="modal-img-target" src="" alt="Campanha">
        </div>
        <div class="modal-right">
            <div>
                <span class="modal-meta-tag" id="modal-tag-target">Ativo de Campanha</span>
                <h3 class="modal-camp-title" id="modal-title-target">Nome do Criativo</h3>
                <p class="modal-camp-desc" id="modal-desc-target">Descrição detalhada.</p>
                
                <div class="modal-stats">
                    <div class="modal-stats-row"><span>Tipo de Mídia:</span><span id="modal-type-label" style="color:#001133;">-</span></div>
                    <div class="modal-stats-row"><span>Score de Relevância IA:</span><span id="modal-score-target">0%</span></div>
                    <div class="modal-stats-row"><span>Status:</span><span>Pronto para Lançamento</span></div>
                </div>
            </div>
            <div>
                <button class="modal-action-btn" onclick="exportarImagem(document.getElementById('modal-title-target').innerText, document.getElementById('modal-img-target').src)">Exportar Corporativo</button>
            </div>
        </div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h5>Soluções</h5>
            <ul>
                <li><a href="#" onclick="irGerar()">Gerar Campanhas</a></li>
                <li><a href="#">Análise de Mercado</a></li>
                <li><a href="#">Estratégia Digital</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h5>Recursos</h5>
            <ul>
                <li><a href="GerenciarCampanhas.php">Painel de Campanhas</a></li>
                <li><a href="ChatMarkt.php" onclick="abrirChat()">ChatMarkt</a></li>
                <li><a href="gcr.php" onclick="abrirGCR()">Sistema GCR</a></li>
                <li><a href="index.php" onclick="abrirGCR()">Sair do sistema</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h5>Institucional</h5>
            <ul>
                <li><a href="sobreOficial.php" onclick="abrirRelatorios()">Sobre o Oficial</a></li>
                <li><a href="#">Privacidade</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-links-base">
            <a href="#">Termos de Uso</a>
            <a href="#">Cookies</a>
        </div>
        <div class="footer-title">
            © 2026 Assistente Inteligente Online. Todos os direitos reservados.
        </div>
    </div>
</footer>

<script>
// Função para filtrar por tipo de mídia
function filtrarMidia(tipo) {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        if (tipo === 'todas') {
            card.style.display = 'block';
        } else {
            const midia = card.getAttribute('data-midia');
            card.style.display = (midia === tipo) ? 'block' : 'none';
        }
    });

    // Atualizar classe ativa nos botões
    document.querySelectorAll('.filter-nav-btn').forEach(btn => btn.classList.remove('active'));
    if (tipo === 'todas') document.getElementById('btn-flt-all').classList.add('active');
    else if (tipo === 'digital') document.getElementById('btn-flt-dig').classList.add('active');
    else if (tipo === 'impresso') document.getElementById('btn-flt-imp').classList.add('active');
}

function irGerar() {
    const inputIdeia = document.getElementById("ideia");
    const ideia = inputIdeia.value.trim();
    if (ideia === "") {
        alert("Por favor, digite a sua ideia de marketing antes de continuar.");
        inputIdeia.focus();
        return;
    }
    window.location.href = "Gerar.php?ideia=" + encodeURIComponent(ideia);
}

function abrirPreviewCampanha(imgSrc, titulo, descricao, tipo, score) {
    const modal = document.getElementById('campanha-modal');
    document.getElementById('modal-img-target').src = imgSrc;
    document.getElementById('modal-title-target').innerText = titulo;
    document.getElementById('modal-desc-target').innerText = descricao;
    document.getElementById('modal-type-label').innerText = tipo;
    document.getElementById('modal-score-target').innerText = score;
    modal.classList.add('active');
}

function fecharPreviewCampanha() {
    document.getElementById('campanha-modal').classList.remove('active');
}

function exportarImagem(titulo, imgSrc) {
    let caminhoRelativo = imgSrc.substring(imgSrc.indexOf("img/"));
    let escolha = prompt("Escolha o formato:\n1 - PDF\n2 - Word\n3 - PowerPoint");
    if (!escolha) return;

    let formato = (escolha === "1") ? "pdf" : (escolha === "2") ? "word" : "powerpoint";

    if (formato === "pdf") {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text("Relatório: " + titulo, 14, 20);
        let img = new Image();
        img.src = imgSrc;
        img.onload = () => {
            doc.addImage(img, 'JPEG', 14, 30, 180, 120);
            doc.save("briefing_" + titulo.replace(/\s+/g, '_') + ".pdf");
        };
        return;
    }

    let formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('imagem', caminhoRelativo);
    formData.append('formato', formato);

    fetch('enviar_email.php', { method: 'POST', body: formData })
    .then(r => r.blob())
    .then(blob => {
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = "briefing_" + titulo.replace(/\s+/g, '_') + (formato === "word" ? ".doc" : ".ppt");
        document.body.appendChild(a);
        a.click();
        a.remove();
    })
    .catch(err => alert("Erro ao conectar com servidor de exportação."));
}
</script>

</body>
</html>
