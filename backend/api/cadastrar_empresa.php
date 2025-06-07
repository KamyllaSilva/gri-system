<?php
// backend/api/cadastrar_empresa.php
require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['nome'], $data['cnpj'], $data['endereco'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

$sql = "INSERT INTO empresas (nome, cnpj, endereco, criado_em, atualizado_em)
        VALUES (:nome, :cnpj, :endereco, NOW(), NOW())";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':nome' => $data['nome'],
        ':cnpj' => $data['cnpj'],
        ':endereco' => $data['endereco']
    ]);
    echo json_encode(['message' => 'Empresa cadastrada com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao cadastrar empresa: ' . $e->getMessage()]);
}
