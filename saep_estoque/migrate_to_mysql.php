<?php
// migrate_to_mysql.php - Migrar de SQLite para MySQL com schema correto

echo "🔄 Migração SQLite → MySQL\n";
echo "============================\n\n";

try {
    // 1. Conectar ao MySQL
    echo "🔗 Conectando ao MySQL...\n";
    $mysql = new PDO(
        'mysql:host=127.0.0.1;port=3306',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✓ Conexão com MySQL OK\n\n";
    
    // 2. Criar banco de dados
    echo "📦 Criando banco de dados...\n";
    $mysql->exec("DROP DATABASE IF EXISTS controle_estoque");
    $mysql->exec("CREATE DATABASE controle_estoque DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysql->exec("USE controle_estoque");
    echo "✓ Banco 'controle_estoque' criado\n\n";
    
    // 3. Criar tabelas com schema correto (MySQL)
    echo "� Criando tabelas...\n";
    $schema = file_get_contents('saep_db_mysql.sql');
    
    // Separar e executar cada CREATE TABLE
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $mysql->exec($stmt . ";");
        }
    }
    echo "✓ Tabelas criadas\n\n";
    
    // 4. Migrar dados do SQLite
    echo "📥 Migrando dados do SQLite...\n";
    
    $sqlite = new PDO('sqlite:saep_db.sqlite');
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Migrar usuarios (com password_hash)
    $usuarios = $sqlite->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($usuarios)) {
        echo "  → Migrando " . count($usuarios) . " usuários...\n";
        foreach ($usuarios as $u) {
            $stmt = $mysql->prepare("INSERT INTO usuarios (id, username, password, nome, email, ativo, criado_em) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $u['id'],
                $u['username'],
                $u['password'],  // já está em hash do SQLite
                $u['nome'],
                $u['email'] ?? null,
                $u['ativo'] ?? 1,
                $u['criado_em'] ?? date('Y-m-d H:i:s')
            ]);
        }
        echo "    ✓ Usuários importados\n";
    }
    
    // Migrar produtos
    $produtos = $sqlite->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($produtos)) {
        echo "  → Migrando " . count($produtos) . " produtos...\n";
        foreach ($produtos as $p) {
            $stmt = $mysql->prepare("INSERT INTO produtos (id, sku, nome, descricao, quantidade, quantidade_minima, preco, categoria, ativo, criado_em) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $p['id'],
                $p['sku'],
                $p['nome'],
                $p['descricao'] ?? null,
                $p['quantidade'] ?? 0,
                $p['quantidade_minima'] ?? 5,
                $p['preco'] ?? 0,
                $p['categoria'] ?? null,
                $p['ativo'] ?? 1,
                $p['criado_em'] ?? date('Y-m-d H:i:s')
            ]);
        }
        echo "    ✓ Produtos importados\n";
    }
    
    // Migrar movimentacoes
    $movs = $sqlite->query("SELECT * FROM movimentacoes")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($movs)) {
        echo "  → Migrando " . count($movs) . " movimentações...\n";
        foreach ($movs as $m) {
            $stmt = $mysql->prepare("INSERT INTO movimentacoes (id, produto_id, usuario_id, tipo, quantidade, data, descricao) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $m['id'],
                $m['produto_id'],
                $m['usuario_id'] ?? null,
                $m['tipo'],
                $m['quantidade'],
                $m['data'] ?? date('Y-m-d H:i:s'),
                $m['descricao'] ?? null
            ]);
        }
        echo "    ✓ Movimentações importadas\n";
    }
    
    echo "\n";
    
    // 5. Verificar dados
    echo "📊 Verificando dados...\n";
    
    $tables = ['usuarios', 'produtos', 'movimentacoes', 'auditoria'];
    foreach ($tables as $table) {
        $count = $mysql->query("SELECT COUNT(*) as cnt FROM $table")->fetch(PDO::FETCH_ASSOC)['cnt'];
        echo "  - $table: $count registros\n";
    }
    
    echo "\n✅ Migração concluída com sucesso!\n";
    echo "\n📍 Próximos passos:\n";
    echo "  1. Atualizar db.php para usar MySQL\n";
    echo "  2. Testar login com dados migrados\n";
    echo "  3. Acessar: http://localhost/saep_estoque/app.php?action=login\n";
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📌 Verifique:\n";
    echo "  - MySQL está rodando?\n";
    echo "  - Arquivo saep_db_mysql.sql existe?\n";
    echo "  - Arquivo saep_db.sqlite existe?\n";
}
?>
?>
