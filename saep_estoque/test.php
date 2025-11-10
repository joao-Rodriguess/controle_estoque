<?php
// test.php - Arquivo de teste para verificar a instalação

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SAEP - Teste de Instalação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .test-item {
            padding: 10px;
            margin: 10px 0;
            border-left: 4px solid #ddd;
            border-radius: 2px;
        }
        .test-item.pass {
            background: #d4edda;
            border-left-color: #27ae60;
        }
        .test-item.fail {
            background: #f8d7da;
            border-left-color: #e74c3c;
        }
        .status {
            font-weight: bold;
            margin-right: 10px;
        }
        .pass .status {
            color: #27ae60;
        }
        .fail .status {
            color: #e74c3c;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .actions a {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .actions a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>📦 SAEP - Teste de Instalação</h1>
            <p>Verifique se o sistema está pronto para usar</p>
        </div>";

// Testes
$testes = [];

// 1. PHP Version
$testes['PHP Version'] = [
    'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'mensagem' => 'PHP ' . PHP_VERSION . ' (Requerido: 7.4+)'
];

// 2. PDO Extension
$testes['PDO Extension'] = [
    'status' => extension_loaded('pdo'),
    'mensagem' => extension_loaded('pdo') ? 'PDO disponível' : 'PDO não encontrado'
];

// 3. SQLite Support
$testes['SQLite Support'] = [
    'status' => extension_loaded('pdo_sqlite'),
    'mensagem' => extension_loaded('pdo_sqlite') ? 'SQLite disponível' : 'SQLite não encontrado'
];

// 4. Session Support
$testes['Session Support'] = [
    'status' => extension_loaded('session'),
    'mensagem' => extension_loaded('session') ? 'Sessions disponível' : 'Sessions não encontrado'
];

// 5. Arquivos PHP
$arquivos = ['app.php', 'db.php', 'saep_db.sql'];
foreach ($arquivos as $arquivo) {
    $testes[$arquivo] = [
        'status' => file_exists(__DIR__ . '/' . $arquivo),
        'mensagem' => file_exists(__DIR__ . '/' . $arquivo) ? 'Arquivo encontrado' : 'Arquivo não encontrado'
    ];
}

// 6. Pasta Templates
$testes['Pasta Templates'] = [
    'status' => is_dir(__DIR__ . '/templates'),
    'mensagem' => is_dir(__DIR__ . '/templates') ? 'Pasta existe' : 'Pasta não encontrada'
];

// 7. Pasta Static
$testes['Pasta Static'] = [
    'status' => is_dir(__DIR__ . '/static'),
    'mensagem' => is_dir(__DIR__ . '/static') ? 'Pasta existe' : 'Pasta não encontrada'
];

// 8. Permissão de escrita
$testes['Permissão de Escrita'] = [
    'status' => is_writable(__DIR__),
    'mensagem' => is_writable(__DIR__) ? 'Diretório gravável' : 'Diretório não gravável'
];

// Exibir resultados
echo "<h2>Resultados dos Testes</h2>";

$todos_passaram = true;
foreach ($testes as $nome => $teste) {
    $classe = $teste['status'] ? 'pass' : 'fail';
    $simbolo = $teste['status'] ? '✓' : '✗';
    if (!$teste['status']) $todos_passaram = false;
    
    echo "<div class='test-item $classe'>
        <span class='status'>$simbolo</span>
        <strong>$nome:</strong> " . htmlspecialchars($teste['mensagem']) . "
    </div>";
}

// Resultado final
echo "<div style='margin-top: 20px; padding: 15px; background: " . ($todos_passaram ? '#d4edda' : '#f8d7da') . "; border-radius: 4px;'>";
if ($todos_passaram) {
    echo "<h3 style='color: #27ae60; margin-top: 0;'>✓ Sistema Pronto!</h3>";
    echo "<p>Todos os testes passaram. Você pode acessar o sistema agora.</p>";
} else {
    echo "<h3 style='color: #e74c3c; margin-top: 0;'>✗ Problemas Detectados</h3>";
    echo "<p>Corrija os erros acima antes de usar o sistema.</p>";
}
echo "</div>";

// Ações
echo "<div class='actions'>";
echo "<a href='app.php?action=login'>Acessar Sistema</a>";
echo "<a href='test.php'>Atualizar Teste</a>";
echo "</div>";

echo "    </div>
</body>
</html>";
