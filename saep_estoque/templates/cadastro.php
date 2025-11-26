<?php
$title = 'Cadastro - SAEP Estoque';
$base_url = '/saep_estoque';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <style>

        .login-container {
            background: url('static/images/isso.gif') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">

             <img src="../saep_estoque/static/images/logo.png" alt="Logo SAEP"  style="margin-left: 60px; WIDTH: 200px; HEIGHT: 200px; background-color: white; border-radius: 8px;">
            <p class="text-center text-muted" style="margin-bottom: 2rem;">Criar Conta</p>

            <?php
            $erro = $_SESSION['erro'] ?? null;
            unset($_SESSION['erro']);
            ?>

            <?php if ($erro): ?>
            <div class="alert alert-error">
                ✗ <?php echo htmlspecialchars($erro); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="app.php?action=cadastro">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required autofocus>
                </div>

                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <small style="color: #666;">Mínimo 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Senha</label>
                    <input type="password" id="password_confirm" name="password_confirm" required minlength="6">
                </div>

                <button type="submit" class="btn-primary btn-block" style="margin-bottom: 1rem;">Criar Conta</button>
            </form>

            <div class="text-center" style="border-top: 1px solid #eee; padding-top: 1.5rem;">
                <p style="margin: 0; color: #666;">Já possui conta?</p>
                <a href="app.php?action=login" class="btn btn-secondary" style="width: 100%; margin-top: 0.75rem;">Fazer Login</a>
            </div>
        </div>
    </div>
</body>
</html>
