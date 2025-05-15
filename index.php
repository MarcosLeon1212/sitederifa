<?php
require_once 'includes/db.php';

if (isset($_GET['cpf'])) {
    $cpf = $_GET['cpf'];
    $query = "SELECT * FROM usuarios WHERE cpf = '$cpf'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) === 0) {
        echo "<script>alert('CPF não encontrado');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Últimas Rifas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('./images/cavalo1.avif') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }

    header {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px;
      text-align: center;
    }

    header img {
      max-width: 200px;
      height: auto;
    }

    h1 {
      text-align: center;
      margin-top: 20px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    form {
      display: flex;
      justify-content: center;
      margin: 30px 0;
    }

    input[type="text"] {
      padding: 10px;
      font-size: 16px;
      border: none;
      border-radius: 4px 0 0 4px;
      width: 300px;
      max-width: 80%;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      background-color: #3c8dbc;
      color: white;
      border-radius: 0 4px 4px 0;
      cursor: pointer;
    }

    button:hover {
      background-color: #2f6f9f;
    }

    .rifas-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      padding: 0 20px 40px;
    }

    .rifa-card {
      width: 250px;
      height: 330px;
      background-color: rgba(0, 0, 0, 0.65);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 10px;
      text-align: center;
    }

    .rifa-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    .rifa-card strong {
      font-size: 18px;
      display: block;
      margin-bottom: 5px;
    }

    .rifa-card p {
      font-size: 14px;
      margin: 5px 0 10px;
    }

    .rifa-card a {
      margin-top: auto;
      display: inline-block;
      padding: 8px 15px;
      background-color: #00aced;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }

    .rifa-card a:hover {
      background-color: #0084a0;
    }

    @media (max-width: 600px) {
      .rifa-card {
        width: 90%;
        height: auto;
      }
    }
  </style>
</head>
<body>

  <header>
    <img src="./images/ranchobr V.png" alt="Logo Rancho">
  </header>

  <h1>Últimas Rifas</h1>

  <form method="GET" action="pesquisar.php">
    <input type="text" name="cpf" placeholder="Digite CPF para olhar seu perfil" required>
    <button type="submit">🔍</button>
  </form>

  <div class="rifas-container">
    <?php
    $query = "SELECT * FROM rifas ORDER BY data_criacao DESC LIMIT 5";
    $result = mysqli_query($conn, $query);
    while ($rifa = mysqli_fetch_assoc($result)):
      $imagemBase64 = '';
      if (!empty($rifa['image'])) {
        $imagemBase64 = 'data:image/jpeg;base64,' . base64_encode($rifa['image']);
      }
    ?>
      <div class="rifa-card">
        <?php if ($imagemBase64): ?>
          <img src="<?= $imagemBase64 ?>" alt="Imagem da Rifa">
        <?php endif; ?>
        <strong><?= htmlspecialchars($rifa['titulo']) ?></strong>
        <p><?= htmlspecialchars($rifa['descricao']) ?></p>
        <a href="<?= $rifa['status'] === 'FINALIZADA' ? '#' : 'rifa.php?id=' . $rifa['id'] ?>">
          <?= $rifa['status'] === 'FINALIZADA' ? 'Finalizada' : 'Participar' ?>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>
