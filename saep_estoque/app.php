<?php
session_start();
require_once __DIR__ . '/db.php';

// Inicializar banco de dados
init_database($pdo);

// Rotas permitidas
$routes = ['login', 'cadastro', 'dashboard', 'produtos', 'movimentacoes', 'historico', 'logout', 'perfil', 'ajuda'];
$action = $_GET['action'] ?? 'login';

if (!in_array($action, $routes)) {
    $action = 'login';
}

// Verificar autenticação (exceto para login e cadastro)
$public_pages = ['login', 'cadastro'];
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado && !in_array($action, $public_pages)) {
    header('Location: app.php?action=login');
    exit;
}

// Se já está logado e tenta acessar login, redirecionar para dashboard
if ($usuario_logado && in_array($action, $public_pages)) {
    header('Location: app.php?action=dashboard');
    exit;
}

// HANDLER DE LOGIN
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $resultado = verificar_login($pdo, $username, $password);
        if ($resultado['sucesso']) {
            $_SESSION['usuario_id'] = $resultado['usuario']['id'];
            $_SESSION['username'] = $resultado['usuario']['username'];
            $_SESSION['nome'] = $resultado['usuario']['nome'];
            $_SESSION['mensagem_sucesso'] = 'Login realizado com sucesso!';
            header('Location: app.php?action=dashboard');
            exit;
        } else {
            $_SESSION['erro'] = $resultado['erro'];
        }
    } else {
        $_SESSION['erro'] = 'Preencha todos os campos';
    }
}

// HANDLER DE CADASTRO
if ($action === 'cadastro' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $nome = $_POST['nome'] ?? '';
    
    if ($username && $password && $password_confirm && $nome) {
        if ($password !== $password_confirm) {
            $_SESSION['erro'] = 'As senhas não correspondem';
        } else if (strlen($password) < 6) {
            $_SESSION['erro'] = 'A senha deve ter pelo menos 6 caracteres';
        } else {
            $resultado = criar_usuario($pdo, $username, $password, $nome);
            if ($resultado['sucesso']) {
                $_SESSION['mensagem_sucesso'] = 'Usuário criado com sucesso! Faça login.';
                header('Location: app.php?action=login');
                exit;
            } else {
                $_SESSION['erro'] = $resultado['erro'];
            }
        }
    } else {
        $_SESSION['erro'] = 'Preencha todos os campos';
    }
}

// HANDLER DE LOGOUT
if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: app.php?action=login');
    exit;
}

// HANDLER DE PRODUTOS
if ($action === 'produtos' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $subaction = $_POST['subaction'] ?? '';
    
    if ($subaction === 'criar') {
        $sku = $_POST['sku'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $quantidade = (int)($_POST['quantidade'] ?? 0);
        $preco = (float)($_POST['preco'] ?? 0);
        
        if ($nome && $sku) {
            $resultado = criar_produto($pdo, $sku, $nome, $quantidade, $preco);
            $_SESSION['mensagem_sucesso'] = $resultado['mensagem'] ?? $resultado['erro'];
        }
    } elseif ($subaction === 'editar') {
        $id = (int)($_POST['id'] ?? 0);
        $sku = $_POST['sku'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $preco = (float)($_POST['preco'] ?? 0);
        
        if ($id && $nome && $sku) {
            $resultado = atualizar_produto($pdo, $id, $sku, $nome, $preco);
            $_SESSION['mensagem_sucesso'] = $resultado['mensagem'] ?? $resultado['erro'];
        }
    } elseif ($subaction === 'deletar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $resultado = deletar_produto($pdo, $id);
            $_SESSION['mensagem_sucesso'] = $resultado['mensagem'] ?? $resultado['erro'];
        }
    }
    header('Location: app.php?action=produtos');
    exit;
}

// HANDLER DE MOVIMENTAÇÕES
if ($action === 'movimentacoes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $tipo = $_POST['tipo'] ?? '';
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $descricao = $_POST['descricao'] ?? '';
    
    if ($produto_id && $tipo && $quantidade > 0) {
        $resultado = registrar_movimentacao($pdo, $produto_id, $tipo, $quantidade, $descricao);
        $_SESSION['mensagem_sucesso'] = $resultado['mensagem'] ?? $resultado['erro'];
    } else {
        $_SESSION['erro'] = 'Dados inválidos';
    }
    header('Location: app.php?action=movimentacoes');
    exit;
}

if ($action === 'historico') {
    $filtro_data = $_GET['filtro_data'] ?? null;

    // obter todas as movimentações (limite atual mantido)
    $movimentacoes = listar_movimentacoes($pdo, 500);

    if ($filtro_data) {
        // valida formato
        $d = DateTime::createFromFormat('Y-m-d', $filtro_data);
        if ($d && $d->format('Y-m-d') === $filtro_data) {
            $movimentacoes = array_values(array_filter($movimentacoes, function($m) use ($filtro_data) {
                // assume campo 'data' está em formato compatível com strtotime
                return date('Y-m-d', strtotime($m['data'])) === $filtro_data;
            }));
            // opcional: informar usuário
            $_SESSION['mensagem_sucesso'] = 'Filtrando por: ' . $filtro_data;
        } else {
            $_SESSION['erro'] = 'Data inválida para filtro';
        }
    }
}

// Carregar template
$template = __DIR__ . '/templates/' . $action . '.php';
if (file_exists($template)) {
    require $template;
} else {
    http_response_code(404);
    echo "Página não encontrada";
}