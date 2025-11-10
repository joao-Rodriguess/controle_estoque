<?php
// import_seed.php - importa o arquivo saep_seed.sql para o banco SQLite
// Uso: acesse via navegador ou CLI (php import_seed.php)

require_once __DIR__ . '/db.php';

$sqlFile = __DIR__ . '/saep_seed.sql';
if (!file_exists($sqlFile)) {
    die('Arquivo saep_seed.sql não encontrado.');
}

$sql = file_get_contents($sqlFile);
try {
    $pdo->beginTransaction();
    $pdo->exec($sql);
    $pdo->commit();
    echo "Importação concluída com sucesso.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erro ao importar: " . htmlspecialchars($e->getMessage());
}

?>