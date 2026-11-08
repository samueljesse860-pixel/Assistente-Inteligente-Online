<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Bem-vindo</title>

<style>
  body {
    margin: 0;
    height: 100vh;
    background: linear-gradient(to right, #0d47a1, #001f54);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    font-family: Arial, sans-serif;
  }

  h1 {
    color: white;
    font-size: 40px;
    text-align: center;
    opacity: 0;
    transform: scale(0.8);
    animation: aparecer 2s ease forwards, flutuar 3s ease-in-out infinite 2s;
  }

  /* Entrada suave */
  @keyframes aparecer {
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  /* Movimento leve (efeito inteligente) */
  @keyframes flutuar {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
  }

  /* brilho animado */
  h1::after {
    content: '';
    position: absolute;
    width: 200%;
    height: 100%;
    top: 0;
    left: -100%;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,0.5), transparent);
    animation: brilho 3s infinite;
  }

  @keyframes brilho {
    0% { left: -100%; }
    100% { left: 100%; }
  }
</style>
</head>

<body>

<h1>Bem - vindo ao mundo inteligente online</h1>

<script>
setTimeout(() => {
  window.location.href = "Telainicial00.php";
}, 4000);
</script>

</body>
</html>
