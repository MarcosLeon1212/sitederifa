<?php
require 'vendor/autoload.php';
require_once 'includes/db.php'; // Inclua sua conexão com o banco de dados

use MercadoPago\SDK;
use MercadoPago\Payment;
use MercadoPago\Payer;

// Token de acesso do Mercado Pago
SDK::setAccessToken('APP_USR-7713606993879039-051319-a8bb5ff784ac484310888f5a719feb99-1147349695');

// Recebe o corpo da notificação do Mercado Pago
$body = json_decode(file_get_contents("php://input"), true);

// Adiciona o log de depuração para verificar os dados recebidos
file_put_contents('webhook_log.txt', print_r($body, true), FILE_APPEND);

// Exibe o corpo da notificação (apenas para depuração)
echo "<pre>";
print_r($body);
echo "</pre>";

// Verifica se a notificação é do tipo "payment"
if (isset($body['type']) && $body['type'] === 'payment') {
    // Obtém o ID do pagamento
    $payment_id = $body['data']['id'];

    // Faz a requisição à API do Mercado Pago para obter mais detalhes do pagamento
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/$payment_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer APP_USR-7713606993879039-051319-a8bb5ff784ac484310888f5a719feb99-1147349695"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $payment = json_decode($response, true);

    // Verifica se o pagamento foi aprovado
    $status = $payment['status']; // approved, pending, rejected, etc.
    $txid = $payment['external_reference']; // TXID (referência externa da rifa)

    // Se o pagamento foi aprovado, atualize o status da compra no banco
    if ($status === 'approved') {
        // Atualiza o status do pagamento para 'PAGO' na tabela 'compras'
        $query = "UPDATE compras SET status_pagamento = 'PAGO' WHERE txid = '$txid'";
        if (mysqli_query($conn, $query)) {
            echo "Pagamento aprovado! Status da compra atualizado para PAGO.";
        } else {
            echo "Erro ao atualizar o banco de dados: " . mysqli_error($conn);
        }
    } else {
        // Caso o pagamento não tenha sido aprovado, você pode tratar de acordo com o status
        echo "Pagamento não aprovado. Status atual: " . $status;
    }
} else {
    echo "Tipo de notificação não reconhecido.";
}

http_response_code(200); // Retorna código de sucesso para Mercado Pago
?>
