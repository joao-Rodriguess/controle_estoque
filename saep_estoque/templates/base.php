<?php
// base.php - Template base para todas as páginas autenticadas
$usuario_logado = isset($_SESSION['usuario_id']);
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
$erro = $_SESSION['erro'] ?? null;

// Limpar mensagens da sessão
unset($_SESSION['mensagem_sucesso']);
unset($_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'SAEP - Estoque'); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="flex-between">
                <h1>📦 SAEP - Controle de Estoque</h1>
                <?php if ($usuario_logado): ?>
                <div class="header-user">
                    <span>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></span>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($usuario_logado): ?>
            <nav>
                <a href="/saep_estoque/app.php?action=dashboard" class="<?php echo ($_GET['action'] ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="/saep_estoque/app.php?action=produtos" class="<?php echo ($_GET['action'] ?? '') === 'produtos' ? 'active' : ''; ?>">Produtos</a>
                <a href="/saep_estoque/app.php?action=movimentacoes" class="<?php echo ($_GET['action'] ?? '') === 'movimentacoes' ? 'active' : ''; ?>">Movimentações</a>
                <a href="/saep_estoque/app.php?action=historico" class="<?php echo ($_GET['action'] ?? '') === 'historico' ? 'active' : ''; ?>">Histórico</a>
                <a href="/saep_estoque/app.php?action=perfil" class="<?php echo ($_GET['action'] ?? '') === 'perfil' ? 'active' : ''; ?>">Perfil</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <main class="container">
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

        <!-- Conteúdo específico da página será inserido aqui -->
    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>
</html>


