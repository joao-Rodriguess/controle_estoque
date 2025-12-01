<?php
$title = 'Histórico - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: app.php?action=login');
    exit;
}

// Carrega as movimentações iniciais
$movimentacoes = listar_movimentacoes($pdo, 500);

// --- LÓGICA DE FILTRAGEM (NOVA) ---
$filtro_tipo = $_GET['filtro_tipo'] ?? 'todos';
$busca = $_GET['busca'] ?? '';
$filtro_data = $_GET['filtro_data'] ?? '';

// Filtra o array de movimentações via PHP
if ($filtro_tipo !== 'todos' || !empty($busca) || !empty($filtro_data)) {
    $movimentacoes = array_filter($movimentacoes, function ($m) use ($filtro_tipo, $busca, $filtro_data) {
        // 1. Filtro de Tipo (Entrada/Saída)
        if ($filtro_tipo !== 'todos' && $m['tipo'] !== $filtro_tipo) {
            return false;
        }

        // 2. Filtro de Busca (Nome do Produto) - stripos é case-insensitive
        if (!empty($busca) && stripos($m['produto_nome'], $busca) === false) {
            return false;
        }

        // 3. Filtro de Data (Mantendo a funcionalidade anterior)
        if (!empty($filtro_data)) {
            $dataMovimentacao = date('Y-m-d', strtotime($m['data']));
            if ($dataMovimentacao !== $filtro_data) {
                return false;
            }
        }

        return true;
    });
}
// ----------------------------------
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
                <a href="../saep_estoque/app.php?action=dashboard">Dashboard</a>
                <a href="../saep_estoque/app.php?action=produtos">Produtos</a>
                <a href="../saep_estoque/app.php?action=movimentacoes">Movimentações</a>
                <a href="../saep_estoque/app.php?action=historico" class="active">Histórico</a>
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

        <div class="card">
            <div class="card-header" style="color: #333;">📜 Histórico Completo de Movimentações</div>

            <!-- ÁREA DE FILTROS ATUALIZADA -->
            <div style="background-color: #f8f9fa; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                <form method="GET" action="app.php" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
                    <input type="hidden" name="action" value="historico">

                    <!-- Busca por Nome -->
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="busca"
                            value="<?php echo htmlspecialchars($busca); ?>"
                            placeholder="🔍 Buscar nome do produto..."
                            style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <!-- Filtros em Linha -->
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <!-- Seletor de Tipo -->
                        <select name="filtro_tipo" style="padding: 0.6rem; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                            <option value="todos" <?php echo $filtro_tipo === 'todos' ? 'selected' : ''; ?>>Todos os Tipos</option>
                            <option value="entrada" <?php echo $filtro_tipo === 'entrada' ? 'selected' : ''; ?>>📥 Entradas</option>
                            <option value="saida" <?php echo $filtro_tipo === 'saida' ? 'selected' : ''; ?>>📤 Saídas</option>
                        </select>

                        <!-- Data -->
                        <input type="date" name="filtro_data"
                            value="<?php echo htmlspecialchars($filtro_data); ?>"
                            style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">

                        <!-- Botão Filtrar -->
                        <button type="submit" class="btn btn-secondary btn-small" style="height: 38px;">Filtrar</button>

                        <!-- Botão Limpar (Opcional) -->
                        <?php if ($filtro_tipo !== 'todos' || !empty($busca) || !empty($filtro_data)): ?>
                            <a href="app.php?action=historico" class="btn btn-small" style="background: #ccc; text-decoration: none; color: #333; display: flex; align-items: center; height: 38px;">Limpar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div style="margin-bottom: 1rem; color: #666;">
                <small>Exibindo <strong><?php echo count($movimentacoes); ?></strong> resultados encontrados.</small>
            </div>
            <!-- FIM ÁREA DE FILTROS -->

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
                <div style="padding: 2rem; text-align: center; color: #888;">
                    <p>Nenhuma movimentação encontrada para os filtros selecionados.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>

</html>