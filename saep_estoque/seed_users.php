<?php
// seed_users.php - cria usuários iniciais com password_hash
// Use via CLI: php seed_users.php
// Ou acesse via navegador: http://localhost/saep_estoque/seed_users.php

require_once __DIR__ . '/db.php';

// Inicializa o banco (cria tabelas se necessário)
init_database($pdo);

$usuarios = [
    ['username' => 'admin', 'password' => '123456', 'nome' => 'Administrador'],
    ['username' => 'user1', 'password' => 'senha123', 'nome' => 'Usuário Teste']
];

$results = [];
foreach ($usuarios as $u) {
    // evitar criar duplicados
    if (usuario_existe($pdo, $u['username'])) {
        $results[] = [
            'username' => $u['username'],
            'sucesso' => false,
            'mensagem' => 'já existe'
        ];
        continue;
    }

    $res = criar_usuario($pdo, $u['username'], $u['password'], $u['nome']);
    $results[] = array_merge(['username' => $u['username']], $res);
}

// Exibir resultado simples
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Seed Users - SAEP</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;padding:20px;background:#f5f5f5} .box{background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.08)}</style>
</head>
<body>
    <div class="box">
        <h2>Seed de usuários</h2>
        <ul>
        <?php foreach ($results as $r): ?>
            <li>
                <strong><?php echo htmlspecialchars($r['username']); ?></strong> —
                <?php if (!empty($r['sucesso'])): ?>
                    <span style="color:green">criado com sucesso</span>
                <?php else: ?>
                    <span style="color:orange"><?php echo htmlspecialchars($r['mensagem'] ?? 'erro'); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <p>Você pode agora acessar: <a href="app.php?action=login">Login</a></p>
    </div>
</body>
</html>
