<?php
// db.php - Conexão ao banco de dados e funções auxiliares

$sqliteFile = __DIR__ . '/saep_db.sqlite';

try {
    $pdo = new PDO('sqlite:' . $sqliteFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro na conexão com banco de dados: ' . $e->getMessage());
}

// Inicializar banco de dados se necessário
function init_database(PDO $pdo) {
    $sqlFile = __DIR__ . '/saep_db.sql';
    if (!file_exists($sqlFile)) {
        return false;
    }
    
    $sql = file_get_contents($sqlFile);
    try {
        $pdo->exec($sql);
        return true;
    } catch (Exception $e) {
        // Tabelas podem já existir
        return true;
    }
}

// Funções de usuário
function usuario_existe(PDO $pdo, $username) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->rowCount() > 0;
}

function criar_usuario(PDO $pdo, $username, $password, $nome) {
    if (usuario_existe($pdo, $username)) {
        return ['sucesso' => false, 'erro' => 'Usuário já existe'];
    }
    
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nome) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hash, $nome]);
        return ['sucesso' => true, 'mensagem' => 'Usuário criado com sucesso'];
    } catch (Exception $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao criar usuário'];
    }
}

function verificar_login(PDO $pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT id, username, nome FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        return ['sucesso' => false, 'erro' => 'Usuário não encontrado'];
    }
    
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($password, $result['password'])) {
        return ['sucesso' => true, 'usuario' => $usuario];
    }
    
    return ['sucesso' => false, 'erro' => 'Senha incorreta'];
}

// Funções de produtos
function listar_produtos(PDO $pdo) {
    $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obter_produto(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function criar_produto(PDO $pdo, $sku, $nome, $quantidade, $preco) {
    try {
        $stmt = $pdo->prepare("INSERT INTO produtos (sku, nome, quantidade, preco) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sku, $nome, $quantidade, $preco]);
        return ['sucesso' => true, 'id' => $pdo->lastInsertId(), 'mensagem' => 'Produto criado'];
    } catch (Exception $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao criar produto'];
    }
}

function atualizar_produto(PDO $pdo, $id, $sku, $nome, $preco) {
    try {
        $stmt = $pdo->prepare("UPDATE produtos SET sku = ?, nome = ?, preco = ? WHERE id = ?");
        $stmt->execute([$sku, $nome, $preco, $id]);
        return ['sucesso' => true, 'mensagem' => 'Produto atualizado'];
    } catch (Exception $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao atualizar produto'];
    }
}

function deletar_produto(PDO $pdo, $id) {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM movimentacoes WHERE produto_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
        $pdo->commit();
        return ['sucesso' => true, 'mensagem' => 'Produto deletado'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['sucesso' => false, 'erro' => 'Erro ao deletar produto'];
    }
}

// Funções de movimentações
function listar_movimentacoes(PDO $pdo, $limit = 100) {
    $stmt = $pdo->query("
        SELECT m.*, p.nome as produto_nome, p.sku
        FROM movimentacoes m
        JOIN produtos p ON m.produto_id = p.id
        ORDER BY m.data DESC
        LIMIT $limit
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registrar_movimentacao(PDO $pdo, $produto_id, $tipo, $quantidade, $descricao = '') {
    try {
        $pdo->beginTransaction();
        
        // Inserir movimentação
        $stmt = $pdo->prepare("
            INSERT INTO movimentacoes (produto_id, tipo, quantidade, descricao)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$produto_id, $tipo, $quantidade, $descricao]);
        
        // Atualizar quantidade do produto
        $operador = ($tipo === 'entrada') ? '+' : '-';
        $pdo->prepare("UPDATE produtos SET quantidade = quantidade $operador ? WHERE id = ?")
            ->execute([$quantidade, $produto_id]);
        
        $pdo->commit();
        return ['sucesso' => true, 'mensagem' => 'Movimentação registrada'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['sucesso' => false, 'erro' => 'Erro ao registrar movimentação'];
    }
}

// Funções de dashboard
function obter_estatisticas(PDO $pdo) {
    $total_produtos = $pdo->query("SELECT COUNT(*) as count FROM produtos")->fetch(PDO::FETCH_ASSOC)['count'];
    $valor_total = $pdo->query("SELECT SUM(quantidade * preco) as total FROM produtos")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $movimentacoes_hoje = $pdo->query("SELECT COUNT(*) as count FROM movimentacoes WHERE DATE(data) = DATE('now')")->fetch(PDO::FETCH_ASSOC)['count'];
    $produtos_baixos = $pdo->query("SELECT COUNT(*) as count FROM produtos WHERE quantidade < 5")->fetch(PDO::FETCH_ASSOC)['count'];
    
    return [
        'total_produtos' => $total_produtos,
        'valor_total' => number_format($valor_total, 2, ',', '.'),
        'movimentacoes_hoje' => $movimentacoes_hoje,
        'produtos_baixos' => $produtos_baixos
    ];
}
