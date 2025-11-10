    <?php
// diagnostico.php - Diagnóstico rápido do sistema

session_start();
require_once __DIR__ . '/db.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico SAEP</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .ok { color: green; } .erro { color: red; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #2c3e50; color: white; }
    </style>
</head>
<body>
    <h1>🔧 Diagnóstico SAEP</h1>

    <div class="box">
        <h3>Verificações do Sistema</h3>
        <table>
            <tr>
                <th>Item</th>
                <th>Status</th>
                <th>Detalhes</th>
            </tr>
            <tr>
                <td>Session</td>
                <td class="<?php echo isset($_SESSION) ? 'ok' : 'erro'; ?>">
                    <?php echo isset($_SESSION) ? '✓' : '✗'; ?>
                </td>
                <td><?php echo isset($_SESSION) ? 'Session ativa' : 'Session não iniciada'; ?></td>
            </tr>
            <tr>
                <td>PDO/Banco</td>
                <td class="<?php echo isset($pdo) ? 'ok' : 'erro'; ?>">
                    <?php echo isset($pdo) ? '✓' : '✗'; ?>
                </td>
                <td><?php echo isset($pdo) ? 'Conexão OK' : 'Erro na conexão'; ?></td>
            </tr>
            <tr>
                <td>Arquivo Database</td>
                <td class="<?php echo file_exists(__DIR__ . '/saep_db.sqlite') ? 'ok' : 'erro'; ?>">
                    <?php echo file_exists(__DIR__ . '/saep_db.sqlite') ? '✓' : '✗'; ?>
                </td>
                <td><?php echo file_exists(__DIR__ . '/saep_db.sqlite') ? 'saep_db.sqlite existe' : 'Arquivo não encontrado (será criado)'; ?></td>
            </tr>
            <tr>
                <td>Funções DB</td>
                <td class="<?php echo function_exists('listar_produtos') ? 'ok' : 'erro'; ?>">
                    <?php echo function_exists('listar_produtos') ? '✓' : '✗'; ?>
                </td>
                <td><?php echo function_exists('listar_produtos') ? 'Funções carregadas' : 'Funções não encontradas'; ?></td>
            </tr>
        </table>
    </div>

    <div class="box">
        <h3>Tabelas do Banco</h3>
        <?php
        try {
            $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($tables)) {
                echo '<ul>';
                foreach ($tables as $t) {
                    echo '<li>' . htmlspecialchars($t['name']) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="erro">Nenhuma tabela encontrada. Execute: <a href="import_seed.php">import_seed.php</a></p>';
            }
        } catch (Exception $e) {
            echo '<p class="erro">Erro ao listar tabelas: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="box">
        <h3>Próximos Passos</h3>
        <ol>
            <li><a href="seed_users.php">Criar usuários (seed_users.php)</a></li>
            <li><a href="import_seed.php">Importar dados de exemplo (import_seed.php)</a></li>
            <li><a href="app.php?action=login">Acessar login</a></li>
        </ol>
    </div>
</body>
</html>
