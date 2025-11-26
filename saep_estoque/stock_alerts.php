<?php
// stock_alerts.php
// Função para enviar alerta por email quando estoque estiver baixo

function sendStockAlertEmail($pdo) {
    $destinatario = "admin@seusite.com";  // ajuste para email real do administrador
    $assunto = "Alerta de Estoque Baixo - SAEP Controle de Estoque";

    $stmt = $pdo->prepare("SELECT sku, nome, quantidade FROM produtos WHERE quantidade < 5");
    $stmt->execute();
    $produtos_baixo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($produtos_baixo) > 0) {
        $mensagem = "Os seguintes produtos estão com estoque baixo (menos de 5 unidades):\n\n";
        foreach ($produtos_baixo as $produto) {
            $mensagem .= "Código/SKU: " . $produto['sku'] . "\n";
            $mensagem .= "Produto: " . $produto['nome'] . "\n";
            $mensagem .= "Quantidade: " . $produto['quantidade'] . " un.\n\n";
        }

        $headers = "From: no-reply@seusite.com\r\n";

        return mail($destinatario, $assunto, $mensagem, $headers);
    }

    return false;
}
