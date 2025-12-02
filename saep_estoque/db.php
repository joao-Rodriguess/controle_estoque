<?php
// db.php - Conexão ao banco de dados e funções auxiliares
// Configurado para MySQL (substitua por SQLite se necessário)

// ============ CONFIGURAÇÃO ============
// Descomente uma das linhas abaixo para escolher o banco de dados

// OPÇÃO 1: MySQL (recomendado)
$db_config = [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'password' => '',
    'database' => 'controle_estoque'
];

// OPÇÃO 2: SQLite (comentado)
// $db_config = [
//     'type' => 'sqlite',
//     'file' => __DIR__ . '/saep_db.sqlite'
// ];

// ============ CONEXÃO ============
try {
    if ($db_config['type'] === 'mysql') {
        $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
        $pdo = new PDO(
            $dsn,
            $db_config['user'],
            $db_config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } else {
        // SQLite
        $pdo = new PDO(
            'sqlite:' . $db_config['file'],
            null,
            null,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
} catch (PDOException $e) {
    die('Erro na conexão com banco de dados: ' . $e->getMessage());
}

// Helper: recria a conexão PDO (usado para tentar reconectar se o servidor MySQL cair)
function recreate_pdo()
{
    global $db_config, $pdo;
    try {
        if ($db_config['type'] === 'mysql') {
            $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
            $new = new PDO(
                $dsn,
                $db_config['user'],
                $db_config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Avoid persistent connections which can be recycled by the server
                    PDO::ATTR_PERSISTENT => false,
                    // Ensure proper charset
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
                ]
            );
        } else {
            $new = new PDO('sqlite:' . $db_config['file'], null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        // replace global pdo so callers that reuse the variable can keep using it
        $pdo = $new;
        return $new;
    } catch (PDOException $e) {
        // If we cannot reconnect, rethrow to let caller handle it
        throw $e;
    }
}

// Helper genérico para executar uma operação de banco com retry automático se a conexão cair
function db_call_retry(PDO $p, callable $fn)
{
    try {
        return $fn($p);
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        $errno = $e->errorInfo[1] ?? null;
        if (stripos($msg, 'MySQL server has gone away') !== false || $errno == 2006 || stripos($msg, 'Lost connection') !== false) {
            // tentativa de reconexão
            try {
                $new = recreate_pdo();
                return $fn($new);
            } catch (PDOException $e2) {
                // rethrow the original or new exception so caller can handle/log
                throw $e2;
            }
        }
        throw $e;
    }
}

// Inicializar banco de dados se necessário (apenas para SQLite)
function init_database(PDO $pdo)
{
    // Para MySQL, o banco e tabelas já devem estar criados
    // Esta função é mantida para compatibilidade com SQLite

    // Verificar se estamos usando SQLite
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver !== 'sqlite') {
        return true; // MySQL já configurado
    }

    // Para SQLite, criar tabelas se necessário
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
function usuario_existe(PDO $pdo, $username)
{
    return db_call_retry($pdo, function ($pdo) use ($username) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->rowCount() > 0;
    });
}

function criar_usuario(PDO $pdo, $username, $password, $nome)
{
    // verificar existência com retry
    if (usuario_existe($pdo, $username)) {
        return ['sucesso' => false, 'erro' => 'Usuário já existe'];
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    try {
        return db_call_retry($pdo, function ($pdo) use ($username, $password_hash, $nome) {
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nome) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password_hash, $nome]);
            return ['sucesso' => true, 'mensagem' => 'Usuário criado com sucesso'];
        });
    } catch (PDOException $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao criar usuário'];
    }
}

function verificar_login(PDO $pdo, $username, $password)
{
    // Attempt the lookup and on a 'MySQL server has gone away' error try to reconnect once
    try {
        $stmt = $pdo->prepare("SELECT id, username, nome FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        $errno = $e->errorInfo[1] ?? null;
        if (stripos($msg, 'MySQL server has gone away') !== false || $errno == 2006) {
            // try to recreate connection and retry once
            try {
                $pdo = recreate_pdo();
                $stmt = $pdo->prepare("SELECT id, username, nome FROM usuarios WHERE username = ?");
                $stmt->execute([$username]);
            } catch (PDOException $e2) {
                // can't recover
                return ['sucesso' => false, 'erro' => 'Erro de conexão com o banco (não foi possível reconectar)'];
            }
        } else {
            // other DB error
            return ['sucesso' => false, 'erro' => 'Erro ao buscar usuário'];
        }
    }

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        return ['sucesso' => false, 'erro' => 'Usuário não encontrado'];
    }

    // now fetch password hash (with same reconnect-on-failure behavior)
    try {
        $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario['id']]);
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        $errno = $e->errorInfo[1] ?? null;
        if (stripos($msg, 'MySQL server has gone away') !== false || $errno == 2006) {
            try {
                $pdo = recreate_pdo();
                $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id = ?");
                $stmt->execute([$usuario['id']]);
            } catch (PDOException $e2) {
                return ['sucesso' => false, 'erro' => 'Erro de conexão com o banco (não foi possível reconectar)'];
            }
        } else {
            return ['sucesso' => false, 'erro' => 'Erro ao verificar senha do usuário'];
        }
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result && password_verify($password, $result['password'])) {
        return ['sucesso' => true, 'usuario' => $usuario];
    }

    return ['sucesso' => false, 'erro' => 'Senha incorreta'];
}

// Funções de produtos
function listar_produtos(PDO $pdo)
{
    try {
        return db_call_retry($pdo, function ($pdo) {
            $stmt = $pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    } catch (PDOException $e) {
        return [];
    }
}

function obter_produto(PDO $pdo, $id)
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($id) {
            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        });
    } catch (PDOException $e) {
        return null;
    }
}

function criar_produto(PDO $pdo, $sku, $nome, $quantidade, $preco)
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($sku, $nome, $quantidade, $preco) {
            $stmt = $pdo->prepare("INSERT INTO produtos (sku, nome, quantidade, preco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sku, $nome, $quantidade, $preco]);
            return ['sucesso' => true, 'id' => $pdo->lastInsertId(), 'mensagem' => 'Produto criado'];
        });
    } catch (PDOException $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao criar produto'];
    }
}

function atualizar_produto(PDO $pdo, $id, $sku, $nome, $preco)
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($id, $sku, $nome, $preco) {
            $stmt = $pdo->prepare("UPDATE produtos SET sku = ?, nome = ?, preco = ? WHERE id = ?");
            $stmt->execute([$sku, $nome, $preco, $id]);
            return ['sucesso' => true, 'mensagem' => 'Produto atualizado'];
        });
    } catch (PDOException $e) {
        return ['sucesso' => false, 'erro' => 'Erro ao atualizar produto'];
    }
}

function deletar_produto(PDO $pdo, $id)
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($id) {
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM movimentacoes WHERE produto_id = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM produtos WHERE id = ?")->execute([$id]);
            $pdo->commit();
            return ['sucesso' => true, 'mensagem' => 'Produto deletado'];
        });
    } catch (PDOException $e) {
        // tentamos rollback se possível
        try {
            $pdo->rollBack();
        } catch (Exception $_) {
        }
        return ['sucesso' => false, 'erro' => 'Erro ao deletar produto'];
    }
}

// Funções de movimentações
function listar_movimentacoes(PDO $pdo, $limit = 100)
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($limit) {
            $sql = "SELECT m.*, p.nome as produto_nome, p.sku
                FROM movimentacoes m
                JOIN produtos p ON m.produto_id = p.id
                ORDER BY m.data DESC
                LIMIT " . intval($limit);
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    } catch (PDOException $e) {
        return [];
    }
}

function registrar_movimentacao(PDO $pdo, $produto_id, $tipo, $quantidade, $descricao = '')
{
    try {
        return db_call_retry($pdo, function ($pdo) use ($produto_id, $tipo, $quantidade, $descricao) {
            $pdo->beginTransaction();

            // Verificar estoque disponível antes de processar saída
            if ($tipo === 'saida') {
                $stmt = $pdo->prepare("SELECT quantidade, nome FROM produtos WHERE id = ?");
                $stmt->execute([$produto_id]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$produto) {
                    $pdo->rollBack();
                    return ['sucesso' => false, 'erro' => 'Produto não encontrado'];
                }

                if ($produto['quantidade'] < $quantidade) {
                    $pdo->rollBack();
                    return [
                        'sucesso' => false,
                        'erro' => "Estoque insuficiente! Disponível: {$produto['quantidade']} unidades. Solicitado: {$quantidade} unidades."
                    ];
                }
            }

            // Inserir movimentação
            $stmt = $pdo->prepare("INSERT INTO movimentacoes (produto_id, tipo, quantidade, descricao) VALUES (?, ?, ?, ?)");
            $stmt->execute([$produto_id, $tipo, $quantidade, $descricao]);

            // Atualizar quantidade do produto
            $operador = ($tipo === 'entrada') ? '+' : '-';
            $pdo->prepare("UPDATE produtos SET quantidade = quantidade $operador ? WHERE id = ?")->execute([$quantidade, $produto_id]);

            $pdo->commit();
            return ['sucesso' => true, 'mensagem' => 'Movimentação registrada'];
        });
    } catch (PDOException $e) {
        try {
            $pdo->rollBack();
        } catch (Exception $_) {
        }
        return ['sucesso' => false, 'erro' => 'Erro ao registrar movimentação'];
    }
}

// Funções de dashboard
function obter_estatisticas(PDO $pdo)
{
    try {
        return db_call_retry($pdo, function ($pdo) {
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
        });
    } catch (PDOException $e) {
        return [
            'total_produtos' => 0,
            'valor_total' => number_format(0, 2, ',', '.'),
            'movimentacoes_hoje' => 0,
            'produtos_baixos' => 0
        ];
    }
}

function obter_estatisticasMovDia(PDO $pdo)
{
    try {
        return db_call_retry($pdo, function ($pdo) {
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            $sql = "SELECT COUNT(*) as count 
            FROM movimentacoes 
            WHERE data >= :start AND data <= :end";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':start' => $start, ':end' => $end]);

            $movimentacoes_hoje = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            return [
                'movimentacoes_hoje' => $movimentacoes_hoje,
            ];
        });
    } catch (PDOException $e) {
        return [
            'movimentacoes_hoje' => 0,
        ];
    }
}
