<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASSISTENTE INTELIGENTE ONLINE | MARKETING ESTRATÉGICO</title>
    <!-- Importação de Fonte Premium e Ícones Modernos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Archivo:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --black: #000000;
            --white: #ffffff;
            --blue-dark: #0a192f; /* Azul escuro premium estilo Adidas Tech */
            --blue-electric: #0052cc;
            --gray-light: #f5f5f5;
            --gray-dark: #767677;
            --border-color: #ebeded;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            letter-spacing: .05em;
        }

        body { 
            font-family: 'Archivo', sans-serif; 
            background-color: var(--white); 
            color: var(--black); 
            line-height: 1.5; 
            -webkit-font-smoothing: antialiased;
        }

        /* --- NAVBAR --- */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 0 40px; 
            height: 80px;
            background: var(--white); 
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo { 
            font-family: 'Archivo Black', sans-serif;
            font-size: 1.1rem; 
            font-weight: 900; 
            color: var(--black); 
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-links a { 
            text-decoration: none; 
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .btn-entrar { 
            color: var(--black);
            padding: 10px 20px;
        }
        
        .btn-entrar:hover {
            color: var(--blue-electric);
        }

        .btn-login { 
            background: var(--blue-dark); 
            color: var(--white); 
            padding: 12px 24px; 
            border: 1px solid var(--blue-dark);
            transition: 0.2s ease;
        }

        .btn-login:hover { 
            background: var(--white);
            color: var(--blue-dark);
        }

        /* --- HERO SECTION COM VÍDEO DE FUNDO --- */
        .hero { 
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left; 
            padding: 140px 8%; 
            min-height: 80vh;
            overflow: hidden;
            background: var(--black); /* Fallback caso o vídeo falhe */
        }

        /* Elemento do Vídeo */
           .hero-video {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100vw; /* Força a largura da janela */
    height: 100vh; /* Força a altura da janela */
    z-index: 1;
    transform: translate(-50%, -50%);
    object-fit: cover; /* Garante que o vídeo preencha a área sem distorcer */
    pointer-events: none; /* Impede cliques no vídeo */
}

        /* Filtro de Cor Azul sobre o vídeo */
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(10, 25, 47, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%);
            z-index: 2;
        }

        .hero-content {
            position: relative;
            z-index: 3; /* Fica acima do vídeo e do overlay */
            max-width: 900px;
        }

        .hero h1 { 
            font-family: 'Archivo Black', sans-serif;
            font-size: 4.5rem; 
            line-height: 0.95;
            text-transform: uppercase; 
            margin-bottom: 24px; 
            color: var(--white); 
            font-weight: 900;
        }

        .hero h1 span {
            display: block;
            -webkit-text-stroke: 2px var(--white);
            -webkit-text-fill-color: transparent;
        }

        .hero p { 
            font-size: 1.2rem; 
            color: #cbd5e1; 
            max-width: 600px; 
            margin-bottom: 40px; 
            font-weight: 400;
            letter-spacing: 0;
        }

        /* Botão Principal em Azul Escuro */
        .cta-main { 
            background: var(--blue-dark); 
            color: var(--white); 
            padding: 18px 32px; 
            text-decoration: none; 
            font-size: 0.85rem; 
            font-weight: 900; 
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 20px;
            border: 1px solid var(--blue-dark);
            transition: 0.2s ease-in-out;
        }

        .cta-main i {
            font-size: 1.1rem;
            transition: transform 0.2s ease;
        }

        .cta-main:hover { 
            background: var(--white);
            color: var(--blue-dark);
            border-color: var(--white);
        }

        .cta-main:hover i {
            transform: translateX(5px);
        }

        /* --- FEATURES SECTION --- */
        .features { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            border-top: 1px solid var(--border-color);
            background: var(--white); 
        }

        .feature-card { 
            padding: 60px 40px; 
            border-right: 1px solid var(--border-color); 
            border-bottom: 1px solid var(--border-color);
            text-align: left; 
            transition: background 0.2s ease;
        }

        .feature-card:last-child {
            border-right: none;
        }

        .feature-card i { 
            font-size: 2rem; 
            color: var(--blue-dark);
            margin-bottom: 30px; 
            display: block; 
        }

        .feature-card h3 {
            font-family: 'Archivo Black', sans-serif;
            font-size: 1.3rem;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: var(--gray-dark);
            letter-spacing: 0;
            line-height: 1.6;
        }

        .feature-card:hover {
            background: var(--gray-light);
        }

        /* --- FOOTER AZUL ESCURO --- */
        footer { 
            text-align: center; 
            padding: 60px 40px; 
            background: var(--blue-dark); 
            color: var(--white);
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        footer p {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            nav { padding: 0 20px; }
            .hero { padding: 100px 20px; }
            .hero h1 { font-size: 2.8rem; }
            .feature-card { border-right: none; padding: 40px 20px; }
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">SITE Empresarial // Assistente Inteligente Online</div>
    <div class="nav-links">
        <a href="Login.php" class="btn-entrar">Entrar</a>
        <a href="Cadastro.php" class="btn-login">Criar Conta</a>
    </div>
</nav>

<section class="hero">
   <video autoplay loop muted playsinline preload="auto" poster="img/fallback-image.jpg" class="hero-video">
    <source src="https://www.w3schools.com/howto/rain.mp4" type="video/mp4">
    O seu navegador não suporta vídeos em background.
</video>
    
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1>Transforma as tuas ideias em <span>Campanhas de Sucesso</span></h1>
        <p>O Assistente Inteligente Online ajuda-te a pensar, criar e gerir as tuas campanhas de marketing com inteligência estratégica  e tecnologia de ponta.</p>
        <a href="Cadastro.php" class="cta-main">Começar Agora <i class="bi bi-arrow-right"></i></a>
    </div>
</section>

<section class="features">
    <div class="feature-card">
        <i class="bi bi-cpu"></i>
        <h3>ChatMarkt</h3>
        <p>Conversa com a nossa Inteligência Artificial de elite treinada exclusivamente para gerar respostas de conversão imediata.</p>
    </div>
    <div class="feature-card">
        <i class="bi bi-kanban"></i>
        <h3>Gestão Completa</h3>
        <p>Cria, monitoriza e altera o status das tuas campanhas em tempo real com o nosso ecossistema modular de alta performance.</p>
    </div>
    <div class="feature-card">
        <i class="bi bi-shield-check"></i>
        <h3>Segurança de Dados</h3>
        <p>Os teus dados e estratégias corporativas são guardados sob criptografia avançada na nossa infraestrutura dedicada MySQL.</p>
    </div>
</section>

<footer>
    <p>&copy; 2026 Assistente Inteligente Online. Todos os direitos reservados.</p>
</footer>

</body>
</html>
