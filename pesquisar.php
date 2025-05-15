<?php
require_once 'includes/db.php';

$usuario = null; // Variável para armazenar as informações do usuário
$cotas = []; // Variável para armazenar os números de cotas

// Verificar se foi passado o CPF via GET
if (isset($_GET['cpf'])) {
    $cpf = mysqli_real_escape_string($conn, $_GET['cpf']); // Captura o CPF do GET

    // Consultar as informações do usuário baseado no CPF
    $usuarioQuery = mysqli_query($conn, "
        SELECT * FROM usuarios WHERE cpf = '$cpf'
    ");

    if (mysqli_num_rows($usuarioQuery) > 0) {
        $usuario = mysqli_fetch_assoc($usuarioQuery); // Armazenar as informações do usuário

        // Consultar as cotas compradas pelo usuário
        $cotasQuery = mysqli_query($conn, "
            SELECT c.numero_cota, r.titulo AS rifa_titulo
            FROM cotas c
            JOIN compras cp ON cp.id = c.compra_id
            JOIN rifas r ON r.id = cp.rifa_id
            WHERE cp.usuario_id = {$usuario['id']} AND cp.status_pagamento = 'PAGO'
        ");

        while ($row = mysqli_fetch_assoc($cotasQuery)) {
            $cotas[] = $row; // Armazenar as cotas associadas ao usuário
        }
    } else {
        echo "Usuário com CPF $cpf não encontrado.";
    }
} else {
    echo "CPF não fornecido.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
            padding: 20px;
        }

        header .headerIMG {
            width: 100%;
            height: auto;
            max-height: 150px; /* Tamanho reduzido da imagem */
            overflow: hidden;
        }

        header img {
            width: auto;
            height: 100%;
            max-height: 150px; /* Ajuste de altura */
            margin: 0 auto;
            display: block;
        }

        .container {
            width: 80%;
            margin: 20px auto;
        }

        .user-info {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .rifa-info {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        p {
            margin: 5px 0;
        }

        .cota-list {
            list-style-type: none;
            padding: 0;
        }

        .cota-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="headerIMG">
            <img src="./images/ranchobr V.png" alt="Logo Rancho">
        </div>
    </header>

    <div class="container">
        <?php if ($usuario): ?>
            <div class="user-info">
                <h2>Informações do Usuário</h2>
                <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
                <p><strong>CPF:</strong> <?= htmlspecialchars($usuario['cpf']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($usuario['telefone']) ?></p>
            </div>

            <?php if (!empty($cotas)): ?>
                <div class="rifa-info">
                    <h2>Cotas Compradas</h2>
                    <ul class="cota-list">
                        <?php foreach ($cotas as $cota): ?>
                            <li><strong>Cota:</strong> <?= htmlspecialchars($cota['numero_cota']) ?> - <strong>Rifa:</strong> <?= htmlspecialchars($cota['rifa_titulo']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p>Este usuário não possui cotas compradas.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Usuário não encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
