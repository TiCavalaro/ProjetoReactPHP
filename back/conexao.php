<?php
$host = "localhost";
$db = "gestao_financeira";
$user = "root";
$pass = ""; // deixe em branco se estiver usando XAMPP

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['mensagem' => 'Erro na conexão com o banco.']);
    exit;
}
?>
