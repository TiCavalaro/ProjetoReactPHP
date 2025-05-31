<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');

require 'conexao.php'; 

function atualizarArquivoJson($pdo) {
    $sql = "SELECT id, tipo, descricao, valor, categoria, data FROM financas ORDER BY data DESC";
    $stmt = $pdo->query($sql);
    $financas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents(__DIR__ . '/financas.json', json_encode(['financas' => $financas], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['acao']) || $data['acao'] !== 'financa') {
            echo json_encode(['mensagem' => 'Requisição inválida.']);
            exit;
        }

        $sql = "INSERT INTO financas (tipo, descricao, valor, categoria, data) 
                VALUES (:tipo, :descricao, :valor, :categoria, :data_fin)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':tipo' => $data['tipo'],
            ':descricao' => $data['descricao'],
            ':valor' => floatval($data['valor']),
            ':categoria' => $data['categoria'],
            ':data_fin' => $data['data'],
        ]);

        atualizarArquivoJson($pdo);

        echo json_encode(['mensagem' => 'Finança registrada com sucesso!']);

    } elseif ($method === 'GET') {
        $sql = "SELECT id, tipo, descricao, valor, categoria, data FROM financas ORDER BY data DESC";
        $stmt = $pdo->query($sql);
        $financas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['financas' => $financas]);

    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);

        $sql = "UPDATE financas SET tipo = :tipo, descricao = :descricao, valor = :valor, categoria = :categoria, data = :data_fin WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':tipo' => $data['tipo'],
            ':descricao' => $data['descricao'],
            ':valor' => floatval($data['valor']),
            ':categoria' => $data['categoria'],
            ':data_fin' => $data['data'],
            ':id' => intval($data['id']),
        ]);

        atualizarArquivoJson($pdo);

        echo json_encode(['mensagem' => 'Finança atualizada com sucesso!']);

    } elseif ($method === 'DELETE') {
        parse_str(file_get_contents('php://input'), $data);
        
        $sql = "DELETE FROM financas WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([':id' => intval($data['id'])]);

        echo json_encode(['mensagem' => 'Finança excluída com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Método HTTP não suportado.']);
    }
} catch (PDOException $e) {
    echo json_encode(['mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
