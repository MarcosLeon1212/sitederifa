<?php
require_once 'includes/db.php'; // Conexão com o banco de dados

// Função para validar CPF
function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    return true;
}

$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
$qtd = isset($_GET['quantidade']) ? intval($_GET['quantidade']) : 0;
if ($id <= 0 || $qtd <= 0) die("Dados inválidos.");

$query  = "SELECT * FROM rifas WHERE id = $id";
$result = mysqli_query($conn, $query);
$rifa   = mysqli_fetch_assoc($result);
if (!$rifa || $rifa['status'] !== 'ABERTA') die("Rifa não disponível.");

$cpf_error  = "";
$nome_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf      = $_POST['cpf'];
    $nome     = $_POST['nome'];
    $email    = $_POST['email'];
    $telefone = $_POST['telefone'];

    if (!validarCPF($cpf)) {
        $cpf_error = "CPF inválido.";
    } else {
        $query_usuario = "SELECT * FROM usuarios WHERE cpf = '$cpf'";
        $res_usuario   = mysqli_query($conn, $query_usuario);
        if (mysqli_num_rows($res_usuario) == 0) {
            $ins = "INSERT INTO usuarios (cpf,nome,email,telefone) VALUES 
                    ('$cpf','$nome','$email','$telefone')";
            if (mysqli_query($conn, $ins)) {
                $usuario_id = mysqli_insert_id($conn);
            } else {
                $cpf_error = "Erro ao criar o usuário: " . mysqli_error($conn);
            }
        } else {
            $usuario = mysqli_fetch_assoc($res_usuario);
            if ($usuario['nome'] !== $nome) {
                $nome_error = "Esse nome não é ligado ao CPF digitado.";
            } else {
                $usuario_id = $usuario['id'];
            }
        }
        if (empty($cpf_error) && empty($nome_error)) {
            header("Location: confirmar.php?usuario_id=$usuario_id&rifa_id=$id&quantidade=$qtd");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Identificação</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f4f9;
    }
    header {
      background-color: #3c8dbc;
      padding: 10px;              /* Padding reduzido */
      text-align: center;
    }
    .headerIMG {
      max-width: 300px;           /* Largura máxima menor */
      margin: 0 auto;
    }
    .headerIMG img {
      width: 100%;
      height: auto;
      max-height: 80px;           /* Altura máxima reduzida */
      display: block;
    }
    .container {
      max-width: 500px;
      margin: 20px auto;
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    form label {
      display: block;
      margin-bottom: 10px;
      color: #333;
    }
    form input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    form button {
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 15px;
    }
    form button:hover {
      background-color: #218838;
    }
    .error {
      color: #d9534f;
      font-size: 14px;
      margin-top: 5px;
    }
    @media (max-width: 400px) {
      .container { margin: 10px; padding: 10px; }
      form button { font-size: 14px; }
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
    <h1>Identifique-se para comprar</h1>
    <form method="POST" action="">
      <input type="hidden" name="rifa_id" value="<?= $id ?>">
      <input type="hidden" name="quantidade" value="<?= $qtd ?>">

      <label>CPF
        <input type="text" name="cpf" required value="<?= isset($cpf)?htmlspecialchars($cpf):'' ?>">
        <?php if ($cpf_error): ?>
          <div class="error"><?= htmlspecialchars($cpf_error) ?></div>
        <?php endif; ?>
      </label>

      <label>Nome completo
        <input type="text" name="nome" required value="<?= isset($nome)?htmlspecialchars($nome):'' ?>">
        <?php if ($nome_error): ?>
          <div class="error"><?= htmlspecialchars($nome_error) ?></div>
        <?php endif; ?>
      </label>

      <label>Email
        <input type="email" name="email" required value="<?= isset($email)?htmlspecialchars($email):'' ?>">
      </label>

      <label>Telefone
        <input type="text" name="telefone" required value="<?= isset($telefone)?htmlspecialchars($telefone):'' ?>">
      </label>

      <button type="submit">Gerar PIX</button>
    </form>
  </div>

</body>
</html>
