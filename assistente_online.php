-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28-Abr-2026 às 12:38
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `assistente_online`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `campanhas`
--

CREATE TABLE `campanhas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `objetivo` varchar(100) DEFAULT NULL,
  `conteúdo` text NOT NULL,
  `status` enum('planeamento','ativa','concluída') DEFAULT 'planeamento',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `campanhas`
--

INSERT INTO `campanhas` (`id`, `usuario_id`, `titulo`, `objetivo`, `conteúdo`, `status`, `data_criacao`) VALUES
(1, 2, 'Lancamento inverno', 'vendas', 'Automoveis que duram', 'ativa', '2026-04-27 12:13:25'),
(2, 4, 'Lancamento Super Top', 'branding', 'Vamos a isso', 'planeamento', '2026-04-27 12:59:58'),
(3, 4, 'Perfumes para mulheres', 'vendas', 'Alta Qualidade', 'concluída', '2026-04-28 10:07:15');

-- --------------------------------------------------------

--
-- Estrutura da tabela `chat_historico`
--

CREATE TABLE `chat_historico` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensagem_usuario` text NOT NULL,
  `resposta_ia` text NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `chat_historico`
--

INSERT INTO `chat_historico` (`id`, `usuario_id`, `mensagem_usuario`, `resposta_ia`, `data_criacao`) VALUES
(1, 2, 'Quero criar campanhas', 'Uma boa campanha precisa de estratégia e criatividade.', '2026-04-27 12:10:27'),
(2, 2, 'Também necessito que me ajudes a criar uma boa campanha', 'Uma boa campanha precisa de estratégia e criatividade.', '2026-04-27 12:11:03'),
(3, 2, 'sim me ajuda', 'Estou a processar a tua ideia e a gerar uma resposta inteligente.', '2026-04-27 12:11:16'),
(4, 2, 'esta bem', 'Estou a processar a tua ideia e a gerar uma resposta inteligente.', '2026-04-27 12:11:26'),
(5, 4, 'Como fazer boas campanhas?', 'Para criar uma campanha profissional, utilize a estrutura **AIDA**: <br>• **Atenção:** Um título impactante. <br>• **Interesse:** Mostre o benefício real do seu produto. <br>• **Desejo:** Use depoimentos ou escassez. <br>• **Ação:** Um botão de compra claro (CTA).', '2026-04-28 10:08:45'),
(6, 4, 'Obrigado, tem mais sugestão?', 'Essa é uma ideia interessante. Para aprofundarmos, poderias detalhar qual é o **público-alvo** e o **orçamento** disponível para esta ação de marketing?', '2026-04-28 10:09:19');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('usuario','admin') DEFAULT 'usuario',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@projeto.com', '$2y$10$7raff6GfO/6I/4r/5t5oEu.UfGZkU6pC.7MhR/VvYf/X.Xl8GvGKi', 'admin', '2026-04-27 10:37:26'),
(2, 'Luciano Queta', 'lucianoquetajunior@gmail.com', '$2y$10$KECxz5C4npEg9SqK1Dg5gOC8oFy9jFddaNSOalrFKtuabjvIwoINS', 'admin', '2026-04-27 12:07:56'),
(4, 'Jesse', 'jesse123@gmail.com', '$2y$10$rNCHKURPdDr2LHOadyzkeOiES2zb/UBmExY2gSM8oXN9sffHN9nNS', 'usuario', '2026-04-27 12:57:13');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `campanhas`
--
ALTER TABLE `campanhas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `chat_historico`
--
ALTER TABLE `chat_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `campanhas`
--
ALTER TABLE `campanhas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `chat_historico`
--
ALTER TABLE `chat_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `campanhas`
--
ALTER TABLE `campanhas`
  ADD CONSTRAINT `campanhas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `chat_historico`
--
ALTER TABLE `chat_historico`
  ADD CONSTRAINT `chat_historico_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



ALTER TABLE campanhas ADD COLUMN ideia_criativa TEXT AFTER descricao;
ALTER TABLE campanhas ADD COLUMN prompt_otimizado TEXT AFTER ideia_criativa;
ALTER TABLE campanhas ADD COLUMN imagem_gerada VARCHAR(500) AFTER prompt_otimizado;
ALTER TABLE campanhas ADD COLUMN facebook_publicado TINYINT DEFAULT 0 AFTER imagem_gerada;
ALTER TABLE campanhas ADD COLUMN facebook_post_id VARCHAR(100) AFTER facebook_publicado;
ALTER TABLE campanhas ADD COLUMN data_processamento DATETIME AFTER facebook_post_id;
