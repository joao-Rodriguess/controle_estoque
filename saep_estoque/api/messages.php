<?php
header('Content-Type: application/json');
session_start();

$success = $_SESSION['mensagem_sucesso'] ?? null;
$error = $_SESSION['erro'] ?? null;

// Clear session messages after reading
unset($_SESSION['mensagem_sucesso']);
unset($_SESSION['erro']);

echo json_encode([
    'success' => $success,
    'error' => $error,
]);
