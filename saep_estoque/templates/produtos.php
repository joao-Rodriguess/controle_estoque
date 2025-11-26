<?php
$title = 'Produtos - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: login.php');
    exit;
}

$produtos = listar_produtos($pdo);
$editando = isset($_GET['editar']) ? (int)$_GET['editar'] : null;
$produto_editar = $editando ? obter_produto($pdo, $editando) : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
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
                <div class="card-header">
                    <?php echo $produto_editar ? '✏️ Editar Produto' : '➕ Novo Produto'; ?>
                </div>

                <form method="POST" action="app.php?action=produtos">
                    <input type="hidden" name="subaction" value="<?php echo $produto_editar ? 'editar' : 'criar'; ?>">
                    <?php if ($produto_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $produto_editar['id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="sku">Código/SKU</label>
                        <input type="text" id="sku" name="sku" required value="<?php echo htmlspecialchars($produto_editar['sku'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="nome">Nome do Produto</label>
                        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($produto_editar['nome'] ?? ''); ?>">
                    </div>

                    <?php if (!$produto_editar): ?>
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" id="quantidade" name="quantidade" value="0" min="0">
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="preco">Preço (R$)</label>
                        <input type="number" id="preco" name="preco" step="0.01" value="0" min="0">
                    </div>

                    <div class="flex" style="gap: 0.5rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Salvar</button>
                        <?php if ($produto_editar): ?>
                        <a href="app.php?action=produtos" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Lista de Produtos -->
            <div class="card">
                <div class="card-header">📋 Lista de Produtos (<?php echo count($produtos); ?>)</div>

                <?php if (!empty($produtos)): ?>
                <div class="overflow-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $prod): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($prod['sku']); ?></strong></td>
                                <td><?php echo htmlspecialchars($prod['nome']); ?></td>
                                <td>
                                    <span class="badge <?php echo $prod['quantidade'] < 5 ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo $prod['quantidade']; ?> un.
                                    </span>
                                </td>
                                <td>R$ <?php echo number_format($prod['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <a href="app.php?action=produtos&editar=<?php echo $prod['id']; ?>" class="btn btn-small btn-danger">Editar</a>
                                    <form method="POST" action="app.php?action=produtos" style="display: inline;">
                                        <input type="hidden" name="subaction" value="deletar">
                                        <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Tem certeza?')">Deletar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-muted">Nenhum produto cadastrado ainda.</p>
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

