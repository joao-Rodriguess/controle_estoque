-- saep_db.sql
-- Schema para o Sistema de Controle de Estoque SAEP
-- Compatível com SQLite e MySQL

BEGIN ;

CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY ,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    nome TEXT NOT NULL,
    email TEXT,
    ativo INTEGER DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produtos (
    id INTEGER PRIMARY KEY ,
    sku TEXT UNIQUE,
    nome TEXT NOT NULL,
    descricao TEXT,
    quantidade INTEGER DEFAULT 0,
    quantidade_minima INTEGER DEFAULT 5,
    preco REAL DEFAULT 0,
    categoria TEXT,
    ativo INTEGER DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS movimentacoes (
    id INTEGER PRIMARY KEY ,
    produto_id INTEGER NOT NULL,
    usuario_id INTEGER,
    tipo TEXT NOT NULL,
    quantidade INTEGER NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descricao TEXT,
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS auditoria (
    id INTEGER PRIMARY KEY ,
    usuario_id INTEGER,
    acao TEXT NOT NULL,
    tabela TEXT,
    registro_id INTEGER,
    dados_anteriores TEXT,
    dados_novos TEXT,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Criar índices para melhor performance
CREATE INDEX  idx_usuarios_username ON usuarios(username);
CREATE INDEX  idx_produtos_sku ON produtos(sku);
CREATE INDEX  idx_movimentacoes_produto ON movimentacoes(produto_id);
CREATE INDEX idx_movimentacoes_usuario ON movimentacoes(usuario_id);
CREATE INDEX  idx_movimentacoes_data ON movimentacoes(data);

COMMIT;
