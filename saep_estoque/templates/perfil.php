<?php
$title = 'Perfil - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: app.php?action=login');
    exit;
}
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
            <!-- Informações do Usuário -->
            <div class="card">
                <div class="card-header">👤 Meu Perfil</div>

                <div style="padding: 1rem; background: #f9f9f9; border-radius: 4px; margin-bottom: 1.5rem;">
                    <p style="margin: 0.5rem 0;">
                        <strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['nome']); ?>
                    </p>
                    <p style="margin: 0.5rem 0;">
                        <strong>Usuário:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </p>
                    <p style="margin: 0.5rem 0;">
                        <strong>ID:</strong> #<?php echo $_SESSION['usuario_id']; ?>
                    </p>
                </div>

                <form method="POST" action="app.php?action=perfil">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($_SESSION['nome']); ?>" disabled>
                        <small style="color: #666;">Contato do administrador para alterar</small>
                    </div>

                    <div class="form-group">
                        <label for="username">Usuário</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled>
                        <small style="color: #666;">Contato do administrador para alterar</small>
                    </div>
                </form>
            </div>

            <!-- Ações e Informações -->
            <div class="card">
                <div class="card-header">⚙️ Ações</div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <h4 style="margin-top: 0;">Segurança</h4>
                        <p style="color: #666; font-size: 0.9rem;">Para alterar sua senha, contate o administrador do sistema.</p>
                    </div>

                    <div>
                        <h4>Sair da Conta</h4>
                        <p style="color: #666; font-size: 0.9rem;">Encerre sua sessão atual.</p>
                        <a href="app.php?action=logout" class="btn btn-danger">Sair da Conta</a>
                    </div>

                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee;">
                        <h4>Informações do Sistema</h4>
                        <p style="font-size: 0.85rem; color: #999; margin: 0;">
                            SAEP - Controle de Estoque v1.0<br>
                            © 2025 - Todos os direitos reservados
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>
</html>
