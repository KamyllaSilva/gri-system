<?php
// backend/api/login.php
require '../config.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'], $data['senha'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute([':email' => $data['email']]);
$user = $stmt->fetch();

if (!$user || !password_verify($data['senha'], $user['senha_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciais inválidas']);
    exit;
}

// Login OK: criar sessão
$_SESSION['user_id'] = $user['id'];
$_SESSION['empresa_id'] = $user['empresa_id'];
$_SESSION['nome'] = $user['nome'];

echo json_encode(['message' => 'Login realizado com sucesso', 'user' => ['id' => $user['id'], 'nome' => $user['nome'], 'empresa_id' => $user['empresa_id']]]);
