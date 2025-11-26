-- saep_db_mysql.sql
-- Schema para MySQL/MariaDB
-- Versão melhorada com suporte completo a constraints

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    ativo INT DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    quantidade INT DEFAULT 0,
    quantidade_minima INT DEFAULT 5,
    preco DOUBLE DEFAULT 0,
    categoria VARCHAR(100),
    ativo INT DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    usuario_id INT,
    tipo VARCHAR(50) NOT NULL COMMENT 'entrada ou saida',
    quantidade INT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descricao TEXT,
    KEY idx_produto (produto_id),
    KEY idx_usuario (usuario_id),
    KEY idx_data (data),
    CONSTRAINT fk_movimentacoes_produto FOREIGN KEY (produto_id) 
        REFERENCES produtos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_movimentacoes_usuario FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    tabela VARCHAR(100),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_usuario (usuario_id),
    KEY idx_data (data),
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
