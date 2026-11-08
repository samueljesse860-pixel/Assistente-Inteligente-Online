-- 1. Criar a Base de Dados
CREATE DATABASE IF NOT EXISTS assistente_online;
USE assistente_online;

-- 2. Criar a Tabela de Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('usuario', 'admin') DEFAULT 'usuario',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS campanhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    objetivo VARCHAR(100),
    conteúdo TEXT NOT NULL,
    status ENUM('planeamento', 'ativa', 'concluída') DEFAULT 'planeamento',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Criar um Administrador padrão (Senha: admin123):
-- Nota: Em produção, a senha deve ser um hash, mas para teste inicial:
INSERT INTO usuarios (nome, email, senha, tipo) 
VALUES ('Administrador', 'admin@projeto.com', '$2y$10$7raff6GfO/6I/4r/5t5oEu.UfGZkU6pC.7MhR/VvYf/X.Xl8GvGKi', 'admin');

CREATE TABLE IF NOT EXISTS chat_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensagem_usuario TEXT NOT NULL,
    resposta_ia TEXT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

ALTER TABLE campanhas ADD COLUMN ideia_criativa TEXT AFTER descricao;
ALTER TABLE campanhas ADD COLUMN prompt_otimizado TEXT AFTER ideia_criativa;
ALTER TABLE campanhas ADD COLUMN imagem_gerada VARCHAR(500) AFTER prompt_otimizado;
ALTER TABLE campanhas ADD COLUMN facebook_publicado TINYINT DEFAULT 0 AFTER imagem_gerada;
ALTER TABLE campanhas ADD COLUMN facebook_post_id VARCHAR(100) AFTER facebook_publicado;
ALTER TABLE campanhas ADD COLUMN data_processamento DATETIME AFTER facebook_post_id;
