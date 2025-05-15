<?php
require 'vendor/autoload.php';
require_once 'includes/db.php'; // Conexão com o banco de dados

use MercadoPago\SDK;

// Seu token de acesso real
SDK::setAccessToken('SEU_ACCESS_TOKEN_REAL');

// Recebe o corpo da notificação do Mercado Pago
$body = json_decode(file_get_contents("php://input"), true);

// Adiciona um log para verificar os dados que estão sendo recebidos
file_put_contents('webhook_log.txt', print_r($body, true), FILE_APPEND);

// Verifica se é do tipo "payment"
if (isset($body['type']) && $body['type'] === 'payment' && isset($body['data']['id'])) {
    $payment_id = $body['data']['id'];

    // Consulta a API para obter os detalhes do pagamento
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/$payment_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer SEU_ACCESS_TOKEN_REAL"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $payment = json_decode($response, true);

    if (isset($payment['status']) && isset($payment['external_reference'])) {
        $status = $payment['status'];
        $txid = $payment['external_reference'];

        if ($status === 'approved') {
            // Atualiza o banco de dados
            $stmt = $conn->prepare("UPDATE compras SET status_pagamento = 'PAGO' WHERE txid = ?");
            $stmt->bind_param("s", $txid);

            if ($stmt->execute()) {
                echo "Pagamento aprovado e atualizado no banco.";
            } else {
                echo "Erro ao atualizar o banco: " . $stmt->error;
            }
        } else {
            echo "Pagamento recebido, mas com status: $status";
        }
    } else {
        echo "Erro ao interpretar os dados do pagamento.";
    }
} else {
    echo "Tipo de notificação não reconhecido ou dados faltando.";
}

http_response_code(200); // Retorna código OK para o Mercado Pago
?>
