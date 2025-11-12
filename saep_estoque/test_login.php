<?php
require __DIR__ . '/db.php';

try {
    $res = verificar_login($pdo, 'joao.p.silva443@aluno.senai.br', '123456');
    echo "RESULT:\n";
    var_export($res);
    echo "\n";
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
