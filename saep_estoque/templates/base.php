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
    <title><?php echo htmlspecialchars($title ?? 'SAEP - Controle de Estoque'); ?></title>
    <link rel="stylesheet" href="/saep_estoque/static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="flex-between header-logo-container">
                <img src="/saep_estoque/static/images/logo.png" alt="Logo SAEP" class="site-logo" />
                <h1>📦 SAEP - Controle de Estoque</h1>
                <?php if ($usuario_logado): ?>
                <div class="header-user" style="display:flex; align-items:center; gap:10px;">
                    <?php
                    $user_id = $_SESSION['usuario_id'];
                    $user_photo_path = "/saep_estoque/static/images/users/" . $user_id . ".jpg";
                    $user_photo_full_path = __DIR__ . "/../static/images/users/" . $user_id . ".jpg";
                    if (!file_exists($user_photo_full_path)) {
                        $user_photo_path = "/saep_estoque/static/images/users/default-avatar.png";
                    }
                    ?>
                    <img src="<?php echo $user_photo_path; ?>" alt="Foto do usuário" class="profile-photo-mini" />
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
        <div id="alert-container"></div>
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
    <script>
        // Fetch messages from API and display alerts
        async function fetchMessages() {
            try {
                const response = await fetch('/saep_estoque/api/messages.php');
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();

                const alertContainer = document.getElementById('alert-container');
                alertContainer.innerHTML = '';

                if (data.success) {
                    const div = document.createElement('div');
                    div.className = 'alert alert-success';
                    div.textContent = '✓ ' + data.success;
                    alertContainer.appendChild(div);
                }

                if (data.error) {
                    const div = document.createElement('div');
                    div.className = 'alert alert-error';
                    div.textContent = '✗ ' + data.error;
                    alertContainer.appendChild(div);
                }
                
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        window.addEventListener('load', fetchMessages);
    </script>
</body>
</html>
