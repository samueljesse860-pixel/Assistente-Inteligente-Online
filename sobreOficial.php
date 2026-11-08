<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Criador | Studio Premium</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #060913; color: #fff; line-height: 1.6; }

        /* HERO: Mantido exatamente como tinhas, com a foto na medida original */
        .hero { 
            position: relative; 
            height: 80vh; 
            display: flex; 
            align-items: center; 
            padding: 0 10%; 
            background: url('img/Jesse Samuel Joseph.png') no-repeat center top; 
            background-size: contain; /* Mantém a medida original da tua foto */
            background-color: #060913; 
        }
        
        .hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(to right, rgba(6,9,19,0.9) 30%, transparent 100%); }
        .hero-content { position: relative; z-index: 2; max-width: 500px; border-left: 4px solid #ff3333; padding-left: 30px; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; line-height: 1.1; }

        /* BOTÃO VOLTAR */
        .btn-voltar {
            margin-top: 30px;
            padding: 12px 25px;
            background: transparent;
            color: #fff;
            border: 1px solid #ff3333;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            transition: 0.3s;
        }
        .btn-voltar:hover { background: #ff3333; }

        /* SEÇÕES */
        .section { padding: 80px 10%; }
        .grid-tech { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px; }
        .card-tech { background: #0b1120; padding: 30px; border: 1px solid #1e293b; }
        
        .img-tech { width: 100%; height: 250px; object-fit: cover; margin-bottom: 20px; border-radius: 4px; }
        
        .tech-logos { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; }
        .tech-logos img { height: 35px; opacity: 0.6; transition: 0.3s; }
        .tech-logos img:hover { opacity: 1; }

        footer { padding: 40px 10%; text-align: center; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; }
    </style>
</head>
<body>

<section class="hero">
    <div class="hero-content">
        <h1>Jesse Samuel <strong>Joseph</strong></h1>
        <p>Arquiteto de Sistemas e Visionário. A tecnologia é a ferramenta, mas a experiência humana é a minha finalidade.</p>
        <button class="btn-voltar" onclick="window.location.href='Telainicial00.php'">← Voltar</button>
    </div>
</section>

<section class="section">
    <div class="grid-tech">
        <!-- Ferramentas -->
        <div class="card-tech">
            <h2>Stack Tecnológico</h2>
            <p>Desenvolvido com o ecossistema robusto que garante escalabilidade e performance no mercado global.</p>
            <div class="tech-logos">
                <img src="https://cdn.worldvectorlogo.com/logos/javascript-1.svg" alt="JS">
                <img src="https://cdn.worldvectorlogo.com/logos/html-1.svg" alt="HTML">
                <img src="https://cdn.worldvectorlogo.com/logos/css-3.svg" alt="CSS">
            </div>
        </div>

        <!-- Chatbots/IA -->
        <div class="card-tech">
            <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=800" alt="IA Criativa" class="img-tech">
            <h3>Inteligência Computacional</h3>
            <p>O futuro da automação reside na colaboração entre a lógica de código e a criatividade generativa da IA.</p>
        </div>
    </div>
</section>

<footer>
    &copy; 2026 Jesse Samuel Joseph | Estúdio de Design e Desenvolvimento | Luanda, Angola
</footer>

</body>
</html>
