<?php
// ============================================================
//   API POLLINATIONS.AI - GRATUITA, SEM CHAVE E SEM SIMULAÇÃO
// ============================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $acao = $input['acao'] ?? 'gerar';
    $texto = $input['prompt'] ?? $input['texto'] ?? '';

    // ----------------------------------------------------------
    //  FUNÇÃO PARA CHAMAR A API DA POLLINATIONS (SEM CHAVE)
    // ----------------------------------------------------------
    function chamarPollinations($prompt, $model = 'openai') {
        // A API usa GET, por isso o prompt tem que ser codificado para URL
        $promptEncoded = urlencode($prompt);
        
        // Parâmetros opcionais para melhor controlo
        $params = [
            'model' => $model,        // Modelo a usar (openai, mistral, etc.)
            'json' => 'true',         // Para receber uma resposta JSON estruturada
        ];
        $query = http_build_query($params);
        
        $url = "https://text.pollinations.ai/{$promptEncoded}?{$query}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            error_log("Pollinations API error: $httpCode - $response");
            return "❌ A API da Pollinations está temporariamente indisponível. Tenta novamente dentro de alguns segundos.";
        }
        
        // Tenta decodificar a resposta como JSON
        $data = json_decode($response, true);
        if ($data && isset($data['response'])) {
            return $data['response'];
        }
        
        // Se não for JSON, devolve o texto puro
        return $response;
    }

    // ----------------------------------------------------------
    //  FUNÇÕES PARA CADA AÇÃO
    // ----------------------------------------------------------
    function gerarLandingPage($produto) {
        $prompt = "Escreve uma landing page profissional para vender: $produto. Inclui título, subtítulo, 3 benefícios e uma chamada para ação (CTA) forte.";
        $resultado = chamarPollinations($prompt);
        return "🚀 **LANDING PAGE (Gerada por IA)**\n\n" . $resultado;
    }

    function analisarSentimento($texto) {
        $prompt = "Analisa o sentimento do seguinte texto e classifica-o como Positivo, Negativo ou Neutro. Depois, dá 5 sugestões concretas para melhorar a sua persuasiveza e apelo comercial. Texto: \"$texto\"";
        $resultado = chamarPollinations($prompt);
        return "✅ **ANÁLISE DE SENTIMENTO (IA)**\n\n" . $resultado;
    }

    function sugerirPublicacao($produto) {
        $prompt = "Dá recomendações detalhadas de canais de marketing, melhor horário para publicar e um orçamento sugerido para promover o seguinte produto: $produto. Sê específico e prático.";
        $resultado = chamarPollinations($prompt);
        return "📢 **SUGESTÕES DE PUBLICAÇÃO (IA)**\n\n" . $resultado;
    }

    // ----------------------------------------------------------
    //  LÓGICA PRINCIPAL
    // ----------------------------------------------------------
    $resposta = '';
    switch ($acao) {
        case 'gerar':
            $resposta = gerarLandingPage($texto);
            break;
        case 'analisar':
            $resposta = analisarSentimento($texto);
            break;
        case 'sugerir':
            $resposta = sugerirPublicacao($texto);
            break;
        default:
            $resposta = "Ação desconhecida.";
    }

    echo json_encode(['texto' => $resposta]);
    exit;
}

// ============================================================
//  HTML (Mantido exatamente igual ao original)
// ============================================================
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Growth Engine | Marketing Inteligente</title>
    <style>
        /* (mesmo CSS original, inalterado) */
        * { box-sizing: border-box; margin: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #f8f8f8;
            color: #111;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            max-width: 1100px;
            width: 100%;
            background: #fff;
            padding: 40px 50px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .logo-area h1 {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -1.5px;
            font-size: 2.5rem;
            margin: 0;
            color: #000;
        }
        .logo-area .badge {
            display: inline-block;
            background: #000;
            color: #fff;
            padding: 2px 14px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 20px;
            margin-left: 12px;
            vertical-align: middle;
        }
        .logo-area .sub {
            color: #777;
            font-weight: 300;
            font-size: 1rem;
            margin-top: 4px;
        }
        .btn-voltar {
            background: #000;
            color: #fff;
            padding: 12px 28px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: background 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-voltar:hover { background: #333; }
        .form-group { margin: 30px 0 20px; }
        textarea {
            width: 100%;
            height: 100px;
            padding: 20px 24px;
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            font-size: 1rem;
            font-family: inherit;
            outline: none;
            transition: border 0.2s;
            resize: vertical;
            background: #fafafa;
        }
        textarea:focus { border-color: #000; background: #fff; }
        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: center;
            min-width: 160px;
        }
        .btn-primary {
            background: #000;
            color: #fff;
        }
        .btn-primary:hover { background: #222; }
        .btn-secondary {
            background: #f0f0f0;
            color: #000;
            border: 2px solid #ddd;
        }
        .btn-secondary:hover {
            background: #e5e5e5;
            border-color: #aaa;
        }
        .btn-secondary:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        .output-box {
            margin: 30px 0 20px;
            padding: 30px 35px;
            background: #fafafa;
            border-left: 6px solid #000;
            border-radius: 12px;
            line-height: 1.7;
            white-space: pre-wrap;
            min-height: 120px;
            font-size: 1rem;
            color: #222;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        .card {
            background: #fafafa;
            padding: 25px 30px;
            border-radius: 16px;
            border: 1px solid #eee;
        }
        .card h3 {
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            color: #888;
            margin-top: 0;
            margin-bottom: 12px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .card-content {
            white-space: pre-wrap;
            line-height: 1.6;
            font-size: 0.95rem;
            color: #222;
        }
        .api-status {
            background: #f0f0f0;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 10px;
        }
        .api-status .ok { color: green; font-weight: bold; }
        @media (max-width: 768px) {
            .container { padding: 25px 20px; }
            .header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .btn-voltar { align-self: stretch; justify-content: center; }
            .grid { grid-template-columns: 1fr; gap: 20px; }
            .btn-group { flex-direction: column; }
            .btn { flex: 1; }
        }
        .hidden { display: none !important; }
        .mt-2 { margin-top: 20px; }
        .text-center { text-align: center; }
        .fade-in {
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <div class="logo-area">
            <h1>Gerador profissional <span class="badge">exclusive</span></h1>
            <div class="sub">Geração profissional de análise e sugestão de publicação</div>
        </div>
        <a href="TelaInicial00.php" class="btn-voltar">← Voltar</a>
    </div>

    <!-- Indicador de estado da API -->
    <div class="api-status">
     <span class="ok"></span>
        <span style="font-size:0.7rem; margin-left:10px;">
          
        </span>
    </div>

    <div class="form-group">
        <textarea id="prompt" placeholder="O que tu vendes?"></textarea>
    </div>

    <div class="btn-group">
        <button id="btnGerar" class="btn btn-primary" onclick="executarAcao('gerar')">📄 Publicidade do produto</button>
        <button id="btnAnalisar" class="btn btn-secondary" onclick="executarAcao('analisar')" disabled>📊 Estrategia de marketing</button>
        <button id="btnSugerir" class="btn btn-secondary" onclick="executarAcao('sugerir')" disabled>📅 Sugerir Publicação</button>
    </div>

    <div id="output" class="output-box">Aguardando comando...</div>

    <div class="grid" id="gridExtra" style="display:none;">
        <div class="card">
            <h3>📊 Estrategia de marketing</h3>
            <div id="analiseConteudo" class="card-content">Clique em "Estrategia de marketing" após gerar a landing page.</div>
        </div>
        <div class="card">
            <h3>📅 Sugestão de Publicação</h3>
            <div id="sugestaoConteudo" class="card-content">Clique em "Sugerir Publicação" para ver recomendações.</div>
        </div>
    </div>

</div>

<script>
let ultimoTextoGerado = '';

async function executarAcao(acao) {
    const promptEl = document.getElementById('prompt');
    const output = document.getElementById('output');
    const btnGerar = document.getElementById('btnGerar');
    const btnAnalisar = document.getElementById('btnAnalisar');
    const btnSugerir = document.getElementById('btnSugerir');
    const gridExtra = document.getElementById('gridExtra');
    const analiseDiv = document.getElementById('analiseConteudo');
    const sugestaoDiv = document.getElementById('sugestaoConteudo');

    let promptValue = promptEl.value.trim();
    if (acao === 'analisar' || acao === 'sugerir') {
        if (!ultimoTextoGerado) {
            alert('Primeiro gere uma landing page!');
            return;
        }
        promptValue = ultimoTextoGerado;
    } else {
        if (!promptValue) {
            alert('Digite algo para vender.');
            return;
        }
    }

    [btnGerar, btnAnalisar, btnSugerir].forEach(b => b.disabled = true);
    output.innerText = "⏳ A processar...";
    if (acao === 'gerar') {
        gridExtra.style.display = 'none';
        analiseDiv.innerHTML = '🔍 Clique em "Analisar Sentimento" para obter a análise.';
        sugestaoDiv.innerHTML = '📢 Clique em "Sugerir Publicação" para recomendações.';
    }

    try {
        const payload = { acao: acao };
        if (acao === 'gerar') {
            payload.prompt = promptValue;
        } else {
            payload.texto = promptValue;
        }

        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.error) {
            output.innerText = "❌ Erro: " + (data.error.message || data.error || JSON.stringify(data.error));
        } else if (data.texto) {
            const texto = data.texto;

            if (acao === 'gerar') {
                ultimoTextoGerado = texto;
                output.innerText = texto;
                btnAnalisar.disabled = false;
                btnSugerir.disabled = false;
                gridExtra.style.display = 'grid';
                analiseDiv.innerHTML = '🔍 Clique em "Analisar Sentimento" para obter a análise.';
                sugestaoDiv.innerHTML = '📢 Clique em "Sugerir Publicação" para recomendações.';
            } else if (acao === 'analisar') {
                analiseDiv.innerText = texto;
                output.innerText = ultimoTextoGerado + "\n\n--- ANÁLISE DE SENTIMENTO (acima) ---";
            } else if (acao === 'sugerir') {
                sugestaoDiv.innerText = texto;
                output.innerText = ultimoTextoGerado + "\n\n--- SUGESTÕES DE PUBLICAÇÃO (acima) ---";
            }
        } else {
            output.innerText = "⚠️ Resposta inesperada. Tente novamente.";
        }
    } catch (e) {
        output.innerText = "❌ Erro de rede: " + e.message;
    } finally {
        [btnGerar, btnAnalisar, btnSugerir].forEach(b => b.disabled = false);
    }
}
</script>

</body>
</html>
