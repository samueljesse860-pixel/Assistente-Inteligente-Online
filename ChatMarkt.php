<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatMarkt | Assistente Profissional</title>
    <style>
        :root { --primary: #001f54; --bg: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--bg); display: flex; flex-direction: column; height: 100vh; }

        .topo { background: var(--primary); color: white; padding: 15px 25px; display: flex; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .voltar { background: #1a3a78; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-right: 20px; }

        .container { flex: 1; display: flex; flex-direction: column; max-width: 800px; margin: 20px auto; width: 95%; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .chat-box { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background: #fafafa; }
        .msg { padding: 12px 18px; border-radius: 15px; max-width: 85%; line-height: 1.5; font-size: 15px; }
        .user { align-self: flex-end; background: #007bff; color: white; border-bottom-right-radius: 2px; }
        .bot { align-self: flex-start; background: #e9ecef; color: #333; border-bottom-left-radius: 2px; }

        .input-area { padding: 20px; border-top: 1px solid #eee; display: flex; gap: 10px; background: white; }
        input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 16px; }
        button.enviar { padding: 10px 25px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="topo">
    <button class="voltar" onclick="window.location.href='Telainicial00.php'">← Voltar</button>
    ChatMarkt - Inteligência de Marketing
</div>

<div class="container">
    <div class="chat-box" id="chat">
        <div class="msg bot">Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong>! Como posso otimizar a sua estratégia hoje?</div>
    </div>

    <div class="input-area">
        <input type="text" id="input" placeholder="Digite sua estratégia ou dúvida..." onkeypress="if(event.key === 'Enter') enviar()">
        <button class="enviar" onclick="enviar()">Enviar</button>
    </div>
</div>

<script>
let ultimaMensagem = 0;

async function enviar() {
    let input = document.getElementById("input");
    let texto = input.value.trim();
    let chat = document.getElementById("chat");

    if (texto === "" || (Date.now() - ultimaMensagem < 3000)) return;
    ultimaMensagem = Date.now();

    chat.innerHTML += `<div class='msg user'>${texto}</div>`;
    input.value = "";
    chat.scrollTop = chat.scrollHeight;

    let formData = new FormData();
    formData.append('mensagem', texto);

    try {
        const res = await fetch('gemini_engine.php', { method: 'POST', body: formData });
        const respostaIA = await res.text();
        
        chat.innerHTML += `<div class='msg bot'>${respostaIA}</div>`;
        chat.scrollTop = chat.scrollHeight;
        salvarHistorico(texto, respostaIA);
    } catch (err) {
        chat.innerHTML += `<div class='msg bot' style='color:red'>Erro de conexão.</div>`;
    }
}

function salvarHistorico(msgUsuario, msgBot) {
    let formData = new FormData();
    formData.append('mensagem', msgUsuario);
    formData.append('resposta', msgBot);
    fetch('gemini_engine.php', { method: 'POST', body: formData });
}
</script>

</body>
</html>
