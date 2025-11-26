<?php
// alerta_estoque.php
// Script para enviar alerta por email quando a quantidade de algum produto estiver baixa (quantidade < 5)

session_start();

// Configurações do banco de dados
require_once 'db.php'; // assumindo que db.php conecta ao banco

// Configurações do email de alerta
$destinatario = "joao.p.silva443@aluno.senai.br";  // Mudar para o email do administrador real
$assunto = "Alerta de Estoque Baixo - SAEP Controle de Estoque";

// Busca produtos com estoque baixo
function produtos_estoque_baixo($pdo) {
    $stmt = $pdo->prepare("SELECT sku, nome, quantidade FROM produtos WHERE quantidade < 5");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$produtos_baixo = produtos_estoque_baixo($pdo);

if (count($produtos_baixo) > 0) {
    $mensagem = "Os seguintes produtos estão com estoque baixo (menos de 5 unidades):\n\n";
    foreach ($produtos_baixo as $produto) {
        $mensagem .= "Código/SKU: " . $produto['sku'] . "\n";
        $mensagem .= "Produto: " . $produto['nome'] . "\n";
        $mensagem .= "Quantidade: " . $produto['quantidade'] . " un.\n\n";
    }

    $headers = "From: no-reply@seusite.com\r\n";
    $enviado = mail($destinatario, $assunto, $mensagem, $headers);

    if ($enviado) {
        echo "Email de alerta enviado com sucesso.";
    } else {
        echo "Falha ao enviar o email de alerta.";
    }
} else {
    echo "Nenhum produto com estoque baixo no momento.";
}

?>
