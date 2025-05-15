<?php
require_once 'includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM rifas WHERE id = $id";
$result = mysqli_query($conn, $query);
$rifa = mysqli_fetch_assoc($result);

if (!$rifa) {
    echo "Rifa não encontrada.";
    exit;
}

if ($rifa['status'] === 'FINALIZADA') {
    echo "<h2>A rifa foi finalizada</h2>";
    exit;
}

$imagemBase64 = '';
if (!empty($rifa['image'])) {
    $imagemBase64 = 'data:image/jpeg;base64,' . base64_encode($rifa['image']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($rifa['titulo']) ?></title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: url('./images/cavalo3.avif') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }

    header {
      text-align: center;
      background-color: rgba(0, 0, 0, 0.7);
      padding: 30px 10px;
    }

    .headerIMG img {
      max-width: 100%;
      height: auto;
    }

    .container {
      max-width: 700px;
      margin: 30px auto;
      background: rgba(0, 0, 0, 0.7);
      padding: 30px;
      border-radius: 10px;
    }

    .rifa-img {
      width: 100%;
      max-height: 300px;
      object-fit: contain;
      display: block;
      margin: 0 auto 20px auto;
      border-radius: 0;
      background: none;
      border: none;
    }

    button {
      padding: 10px 15px;
      margin: 5px;
      font-size: 16px;
      cursor: pointer;
      border: none;
      background-color: #3c8dbc;
      color: white;
      border-radius: 5px;
    }

    form {
      text-align: center;
      margin-top: 20px;
    }

    @media (max-width: 600px) {
      .container {
        margin: 10px;
        padding: 20px;
      }
      button {
        font-size: 14px;
        padding: 8px 12px;
      }
    }
  </style>
  <script>
    let qtd = 0;
    function add(n) {
      qtd += n;
      document.getElementById('quantidade').innerText = qtd;
    }
  </script>
</head>
<body>
  <header>
    <div class="headerIMG">
      <img src="./images/ranchobr P.png" alt="Logo">
    </div>
  </header>

  <div class="container">
    <h1><?= htmlspecialchars($rifa['titulo']) ?></h1>

    <?php if ($imagemBase64): ?>
      <img class="rifa-img" src="<?= $imagemBase64 ?>" alt="Imagem da Rifa">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($rifa['descricao'])) ?></p>
    <p><strong>Preço por cota:</strong> R$ <?= number_format($rifa['preco'], 2, ',', '.') ?></p>

    <div style="text-align: center;">
      <button onclick="add(1)">+1</button>
      <button onclick="add(5)">+5</button>
      <button onclick="add(10)">+10</button>
      <button onclick="add(30)">+30</button>
      <button onclick="add(50)">+50</button>
      <button onclick="add(100)">+100</button>
    </div>

    <p style="text-align: center;">Total selecionado: <span id="quantidade">0</span> cotas</p>

    <form method="GET" action="comprar.php">
      <input type="hidden" name="id" value="<?= $rifa['id'] ?>">
      <input type="hidden" id="input_quantidade" name="quantidade" value="0">
      <button type="submit" onclick="document.getElementById('input_quantidade').value = qtd">Comprar</button>
    </form>
  </div>
</body>
</html>
