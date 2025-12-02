<?php
$title = 'Login - SAEP Estoque';

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <style>
        .login-page {
            background: url('static/images/isso.gif') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div style="text-align: center; height: auto;">
                <img src="../saep_estoque/static/images/logo.png" alt="Logo SAEP" class="site-logo">
            </div>
            <h3 class="text-center text-muted" style="margin-bottom: 2rem; font: size 2px;">Controle de Estoque</h3>

            <?php
            $erro = $_SESSION['erro'] ?? null;
            $mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
            unset($_SESSION['erro']);
            unset($_SESSION['mensagem_sucesso']);
            ?>

            <?php if ($erro): ?>
                <div class="alert alert-error">
                    ✗ <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>

            <?php if ($mensagem_sucesso): ?>
                <div class="alert alert-success">
                    ✓ <?php echo htmlspecialchars($mensagem_sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="app.php?action=login">
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary btn-block" style="margin-bottom: 1rem;">Entrar</button>
            </form>

            <div class="text-center" style="border-top: 1px solid #eee; padding-top: 1.5rem;">
                <p style="margin: 0; color: #666;">Não possui conta?</p>
                <a href="app.php?action=cadastro" class="btn btn-secondary"
                    style="width: 100%; margin-top: 0.75rem;">Criar Conta</a>
            </div>

            <div style="margin-top: 2rem; padding: 1rem; background: #f9f9f9; border-radius: 4px; text-align: center;">
                <p style="font-size: 0.85rem; color: #666; margin: 0;">Contas de teste:</p>
                <p style="font-size: 0.85rem; color: #666; margin: 0.5rem 0 0 0;"><strong>User:</strong> admin |
                    <strong>Pass:</strong> 123456
                </p>
            </div>
        </div>
    </div>
</body>

</html>