<?php
$title = 'Movimentações - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: app.php?action=login');
    exit;
}

$produtos = listar_produtos($pdo);
$movimentacoes = listar_movimentacoes($pdo, 50);
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
                <h1><img src="../saep_estoque/static/images/logo.png" alt="Logo SAEP"  style="height: 85px; width:85px; margin-right: 1px; background-color: white; border-radius: 8px;"> 📦Controle de Estoque</h1>
                <div class="header-user">
                    <span>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></span>
                </div>
            </div>

            <nav>
                <a href="../saep_estoque/app.php?action=dashboard">Dashboard</a>
                <a href="../saep_estoque/app.php?action=produtos">Produtos</a>
                <a href="../saep_estoque/app.php?action=movimentacoes">Movimentações</a>
                <a href="../saep_estoque/app.php?action=historico">Histórico</a>
                <a href="../saep_estoque/app.php?action=perfil">Perfil</a>
                <a href="../saep_estoque/app.php?action=ajuda" >Ajuda</a>            
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

        <div class="grid grid-2">
            <!-- Formulário -->
            <div class="card">
                <div class="card-header">➕ Registrar Movimentação</div>

                <?php if (empty($produtos)): ?>
                <div class="alert alert-warning">
                    ⚠️ Nenhum produto cadastrado. <a href="app.php?action=produtos">Criar primeiro produto</a>
                </div>
                <?php else: ?>
                <form method="POST" action="app.php?action=movimentacoes">
                    <div class="form-group">
                        <label for="produto_id">Produto</label>
                        <select id="produto_id" name="produto_id" required>
                            <option value="">-- Selecionar Produto --</option>
                            <?php foreach ($produtos as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>">
                                <?php echo htmlspecialchars($prod['nome']); ?> (<?php echo $prod['quantidade']; ?> un.)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo de Movimentação</label>
                        <select id="tipo" name="tipo" required>
                            <option value="">-- Selecionar --</option>
                            <option value="entrada">📥 Entrada (Recebimento)</option>
                            <option value="saida">📤 Saída (Venda/Uso)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" id="quantidade" name="quantidade" required min="1" value="1">
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição (Opcional)</label>
                        <textarea id="descricao" name="descricao" rows="3" placeholder="Ex: Entrada de nota fiscal #123"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Registrar Movimentação</button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Histórico Recente -->
            <div class="card">
                <div class="card-header">📊 Últimas Movimentações</div>

                <?php if (!empty($movimentacoes)): ?>
                <div class="overflow-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Produto</th>
                                <th>Tipo</th>
                                <th>Qtd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($movimentacoes, 0, 10) as $mov): ?>
                            <tr>
                                <td><small><?php echo date('d/m H:i', strtotime($mov['data'])); ?></small></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($mov['produto_nome']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge <?php echo $mov['tipo'] === 'entrada' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo ucfirst($mov['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo $mov['quantidade']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-muted">Nenhuma movimentação registrada ainda.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>
</html>

