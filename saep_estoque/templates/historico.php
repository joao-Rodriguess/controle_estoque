<?php
$title = 'Histórico - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: app.php?action=login');
    exit;
}

$movimentacoes = listar_movimentacoes($pdo, 500);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="/saep_estoque/static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="flex-between">
                <h1>📦 SAEP - Controle de Estoque</h1>
                <div class="header-user">
                    <span>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></span>
                </div>
            </div>

            <nav>
                <a href="/saep_estoque/app.php?action=dashboard">Dashboard</a>
                <a href="/saep_estoque/app.php?action=produtos">Produtos</a>
                <a href="/saep_estoque/app.php?action=movimentacoes">Movimentações</a>
                <a href="/saep_estoque/app.php?action=historico" class="active">Histórico</a>
                <a href="/saep_estoque/app.php?action=perfil">Perfil</a>
                <a href="/saep_estoque/app.php?action=logout">Sair</a>
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

        <div class="card">
            <div class="card-header">📜 Histórico Completo de Movimentações</div>

            <div class="flex-between" style="margin-bottom: 1.5rem;">
                <div>
                    <small style="color: #666;">Total de movimentações: <strong><?php echo count($movimentacoes); ?></strong></div>
                </div>
                <div>
                    <form method="GET" action="app.php" style="display: flex; gap: 0.5rem;">
                        <input type="hidden" name="action" value="historico">
                        <input type="date" name="filtro_data" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                        <button type="submit" class="btn btn-secondary btn-small">Filtrar</button>
                    </form>
                </div>
            </div>

            <?php if (!empty($movimentacoes)): ?>
            <div class="overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Código</th>
                            <th>Produto</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimentacoes as $mov): ?>
                        <tr>
                            <td>
                                <small><strong><?php echo date('d/m/Y H:i:s', strtotime($mov['data'])); ?></strong></small>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($mov['sku']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($mov['produto_nome']); ?></strong>
                            </td>
                            <td>
                                <span class="badge <?php echo $mov['tipo'] === 'entrada' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $mov['tipo'] === 'entrada' ? '📥 Entrada' : '📤 Saída'; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo $mov['quantidade']; ?> unidades</strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($mov['descricao'] ?? '-'); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-muted">Nenhuma movimentação registrada.</p>
            <?php endif; ?>
        </div>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>
</html>
