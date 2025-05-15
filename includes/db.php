<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'rifasdesenvolvimento';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die('Erro ao conectar ao banco de dados: ' . mysqli_connect_error());
}
?>