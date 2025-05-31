<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');

require 'conexao.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['acao']) || $data['acao'] !== 'financa') {
        echo json_encode(['mensagem' => 'Requisição inválida.']);
        exit;
    }

    $tipo = $conn->real_escape_string($data['tipo']);
    $descricao = $conn->real_escape_string($data['descricao']);
    $valor = floatval($data['valor']);
    $categoria = $conn->real_escape_string($data['categoria']);
    $data_fin = $conn->real_escape_string($data['data']);

    $sql = "INSERT INTO financas (tipo, descricao, valor, categoria, data) 
            VALUES ('$tipo', '$descricao', $valor, '$categoria', '$data_fin')";

    if ($conn->query($sql)) {
        echo json_encode(['mensagem' => 'Finança registrada com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Erro ao registrar finança.']);
    }
} elseif ($method === 'GET') {
    $response = ['financas' => []];
    $sql = "SELECT id, tipo, descricao, valor, categoria, data FROM financas ORDER BY data DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response['financas'][] = $row;
        }
    }

    echo json_encode($response);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = intval($data['id']);
    $tipo = $conn->real_escape_string($data['tipo']);
    $descricao = $conn->real_escape_string($data['descricao']);
    $valor = floatval($data['valor']);
    $categoria = $conn->real_escape_string($data['categoria']);
    $data_fin = $conn->real_escape_string($data['data']);

    $sql = "UPDATE financas SET tipo='$tipo', descricao='$descricao', valor=$valor, categoria='$categoria', data='$data_fin' WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['mensagem' => 'Finança atualizada com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Erro ao atualizar finança.']);
    }
} elseif ($method === 'DELETE') {
    parse_str(file_get_contents('php://input'), $data);
    $id = intval($data['id']);

    $sql = "DELETE FROM financas WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(['mensagem' => 'Finança excluída com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Erro ao excluir finança.']);
    }
} else {
    echo json_encode(['mensagem' => 'Método HTTP não suportado.']);
}

$conn->close();
?>
