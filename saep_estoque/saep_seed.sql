-- saep_seed.sql
-- Dados de exemplo para SAEP - Controle de Estoque
-- Execute após criar as tabelas (ou use import_seed.php)

BEGIN TRANSACTION;

-- Produtos de exemplo
INSERT INTO produtos (sku, nome, descricao, quantidade, quantidade_minima, preco, categoria, ativo, criado_em) VALUES
('SKU-001', 'Parafuso 3/8', 'Parafuso zincado 3/8x20', 150, 10, 0.15, 'Ferramentas', 1, CURRENT_TIMESTAMP),
('SKU-002', 'Porca 3/8', 'Porca sextavada 3/8', 300, 20, 0.08, 'Ferramentas', 1, CURRENT_TIMESTAMP),
('SKU-003', 'Fita isolante 10m', 'Fita isolante preta 10 metros', 45, 5, 2.50, 'Elétrica', 1, CURRENT_TIMESTAMP),
('SKU-004', 'Tinta Acrílica 18L', 'Tinta acrílica PVA branca 18 litros', 12, 2, 199.90, 'Pintura', 1, CURRENT_TIMESTAMP),
('SKU-005', 'Lâmpada LED 9W', 'Lâmpada LED 9W Bivolt', 85, 10, 7.99, 'Elétrica', 1, CURRENT_TIMESTAMP);

-- Movimentações de exemplo (referenciam ids dos produtos inseridos acima)
INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, data, descricao) VALUES
(1, NULL, 'entrada', 200, DATETIME('now','-10 days'), 'Compra fornecedor A'),
(1, NULL, 'saida', 50, DATETIME('now','-8 days'), 'Uso em serviço #123'),
(3, NULL, 'entrada', 50, DATETIME('now','-5 days'), 'Compra fornecedor B'),
(4, NULL, 'entrada', 10, DATETIME('now','-2 days'), 'Reposição estoque'),
(2, NULL, 'saida', 20, DATETIME('now','-1 days'), 'Venda ao cliente X');

-- Auditoria de exemplo
INSERT INTO auditoria (usuario_id, acao, tabela, registro_id, dados_anteriores, dados_novos) VALUES
(NULL, 'seed', 'produtos', NULL, NULL, 'Seed inicial de produtos'),
(NULL, 'seed', 'movimentacoes', NULL, NULL, 'Seed inicial de movimentacoes');

COMMIT;
