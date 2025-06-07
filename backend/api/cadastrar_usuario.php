<?php
// backend/api/cadastrar_usuario.php
require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['nome'], $data['email'], $data['senha'], $data['empresa_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

$senhaHash = password_hash($data['senha'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (nome, email, senha_hash, empresa_id, criado_em, atualizado_em)
        VALUES (:nome, :email, :senha_hash, :empresa_id, NOW(), NOW())";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':nome' => $data['nome'],
        ':email' => $data['email'],
        ':senha_hash' => $senhaHash,
        ':empresa_id' => $data['empresa_id']
    ]);
    echo json_encode(['message' => 'Usuário cadastrado com sucesso']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao cadastrar usuário: ' . $e->getMessage()]);
}
