<?php
require_once 'includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT image FROM rifas WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    header("Content-Type: image/jpeg"); // ou image/png, dependendo do tipo que você salva
    echo $row['image'];
} else {
    http_response_code(404);
    echo "Imagem não encontrada.";
}
?>
