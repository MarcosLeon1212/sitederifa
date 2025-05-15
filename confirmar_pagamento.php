<?php
require_once '../includes/db.php';

if (isset($_GET['compra_id'])) {
    $compra_id = intval($_GET['compra_id']);

    // Atualiza status para PAGO
    mysqli_query($conn, "UPDATE compras SET status_pagamento = 'PAGO' WHERE id = $compra_id");

    // Gera cotas numeradas para essa compra
    $compra = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM compras WHERE id = $compra_id"));
    $quantidade = $compra['quantidade'];
    $rifa_id = $compra['rifa_id'];

    $ultima_cota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(numero_cota) AS ultima FROM cotas WHERE rifa_id = $rifa_id"));
    $numero_inicial = isset($ultima_cota['ultima']) ? intval($ultima_cota['ultima']) + 1 : 0;

    for ($i = 0; $i < $quantidade; $i++) {
        $numero_cota = str_pad($numero_inicial + $i, 6, '0', STR_PAD_LEFT);
        mysqli_query($conn, "INSERT INTO cotas (rifa_id, compra_id, numero_cota) VALUES ($rifa_id, $compra_id, '$numero_cota')");
    }

    echo "Pagamento confirmado e cotas atribuídas.";
} else {
    echo "ID de compra inválido.";
}
?>
