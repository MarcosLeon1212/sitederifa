<?php
require_once 'includes/db.php'; // Inclua sua conexão com o banco de dados

$numeroCota = null;
if (isset($_GET['cota'])) {
    $numeroCota = mysqli_real_escape_string($conn, $_GET['cota']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pesquisa por Cota - Rifas</title>
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

    .search-form {
      text-align: center;
      margin: 20px;
    }

    .search-form input[type="text"] {
      padding: 10px;
      width: 300px;
      margin-right: 10px;
    }

    .search-form button {
      padding: 10px 20px;
      background-color: #3c8dbc;
      color: white;
      border: none;
      cursor: pointer;
    }

    .search-form button:hover {
      background-color: #357ebd;
    }
  </style>
</head>
<body>
  <header>
    <h1>Pesquisar Cota</h1>
  </header>

  <div class="search-form">
    <form method="GET" action="winner.php"> <!-- Formulário de pesquisa -->
      <input type="text" name="cota" placeholder="Digite o número da cota (6 dígitos)" required>
      <button type="submit">🔍</button>
    </form>
  </div>

  <?php if ($numeroCota): ?>
    <?php
    // Consultar a cota no banco de dados para verificar se ela existe
    $query = "SELECT c.id, c.numero_cota, cp.usuario_id
              FROM cotas c
              JOIN compras cp ON c.compra_id = cp.id
              WHERE c.numero_cota = '$numeroCota' AND cp.status_pagamento = 'PAGO'";

    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $usuario_id = $row['usuario_id'];
        
        // Consultar as informações do usuário
        $userQuery = "SELECT nome, cpf, email, telefone FROM usuarios WHERE id = '$usuario_id'";
        $userResult = mysqli_query($conn, $userQuery);
        
        if (mysqli_num_rows($userResult) > 0) {
            $user = mysqli_fetch_assoc($userResult);
            // Exibir as informações do usuário
            echo "<h2>Informações do Usuário</h2>";
            echo "<p><strong>Nome:</strong> " . htmlspecialchars($user['nome']) . "</p>";
            echo "<p><strong>CPF:</strong> " . substr($user['cpf'], 0, 3) . "******" . substr($user['cpf'], -2) . "</p>";  // Exibir CPF parcialmente
            echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
            echo "<p><strong>Telefone:</strong> " . htmlspecialchars($user['telefone']) . "</p>";
        } else {
            echo "<p>Usuário não encontrado.</p>";
        }
    } else {
        echo "<p>Cota não encontrada ou pagamento não confirmado.</p>";
    }
    ?>
  <?php endif; ?>
</body>
</html>
