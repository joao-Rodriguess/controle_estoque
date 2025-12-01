<?php
// Garantir que session está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

$title = 'Dashboard - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: ' . $base_url . '/app.php?action=login');
    exit;
}


// Obter dados
$stats = obter_estatisticas($pdo);
$movimentacoes_recentes = listar_movimentacoes($pdo, 5);
$produtos = listar_produtos($pdo);
$statMov = obter_estatisticasMovDia($pdo);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <style>
        body {
            background: url('static/images/mais.gif') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="flex-between">
                <h1><img src="../saep_estoque/static/images/logo.png" alt="Logo SAEP" style="height: 85px; width:85px; margin-right: 1px; background-color: white; border-radius: 8px;"> 📦Controle de Estoque</h1>
                <div class="header-user">
                    <span>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></span>
                </div>
            </div>

            <nav>
                <a href="../saep_estoque/app.php?action=dashboard" class="active">Dashboard</a>
                <a href="../saep_estoque/app.php?action=produtos">Produtos</a>
                <a href="../saep_estoque/app.php?action=movimentacoes">Movimentações</a>
                <a href="../saep_estoque/app.php?action=historico">Histórico</a>
                <a href="../saep_estoque/app.php?action=perfil">Perfil</a>
                <a href="../saep_estoque/app.php?action=ajuda">Ajuda</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php
        $mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
        $erro = $_SESSION['erro'] ?? null;
        unset($_SESSION['mensagem_sucesso']);
        unset($_SESSION['erro']);
        ?>

        <?php if ($mensagem_sucesso): ?>
            <div class="alert alert-success">
                ✓ <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </div>
        <?php endif; ?>

        <?php if ($erro): ?>
            <div class="alert alert-error">
                ✗ <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <div class="card-header" style="margin-top: 0;">
            <h2>Dashboard</h2>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-4">
            <div class="stat-card">
                <div class="stat-label">Total de Produtos</div>
                <div class="stat-number"><?php echo $stats['total_produtos']; ?></div>
            </div>

            <div class="stat-card success">
                <div class="stat-label">Valor Total</div>
                <div class="stat-number">R$ <?php echo $stats['valor_total']; ?></div>
            </div>

            <div class="stat-card warning">
                <div class="stat-label">Movimentações Hoje</div>
                <div class="stat-number"><?php echo $statMov['movimentacoes_hoje']; ?></div>
            </div>

            <div class="stat-card danger">
                <div class="stat-label">Produtos em Falta</div>
                <div class="stat-number"><?php echo $stats['produtos_baixos']; ?></div>
            </div>
        </div>



        <!-- Produtos com Baixa Quantidade -->
        <?php
        $produtos_baixos = array_filter($produtos, fn($p) => $p['quantidade'] < 5);
        if (!empty($produtos_baixos)):
        ?>
            <div class="card">
                <div class="card-header" style="color: #333;">⚠️ Produtos com Estoque Baixo</div>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos_baixos as $prod): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prod['sku']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['nome']); ?></td>
                                <td>
                                    <span class="badge badge-danger"><?php echo $prod['quantidade']; ?> unidades</span>
                                </td>
                                <td>
                                    <a href="../saep_estoque/app.php?action=movimentacoes" class="btn btn-small btn-warning">Repor</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Últimas Movimentações -->
        <?php if (!empty($movimentacoes_recentes)): ?>
            <div class="card">
                <div class="card-header" style="color: #333;">📊 Últimas Movimentações</div>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentacoes_recentes as $mov): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['data'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mov['produto_nome']); ?></strong><br>
                                    <small style="color: #999;"><?php echo htmlspecialchars($mov['sku']); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $mov['tipo'] === 'entrada' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($mov['tipo']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $mov['quantidade']; ?></strong></td>
                                <td><?php echo htmlspecialchars($mov['descricao'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card">
                <p class="text-center text-muted">Nenhuma movimentação registrada ainda.</p>
            </div>
        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>

</html>