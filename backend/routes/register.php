<?php
require_once __DIR__ . '/../config/database.php';

$nome     = trim($_POST['nome']);
$email    = trim($_POST['email']);
$senha    = trim($_POST['senha']);
$empresa  = trim($_POST['empresa']);

if (!$nome || !$email || !$senha || !$empresa) {
    die("Todos os campos são obrigatórios.");
}

// Verifica se a empresa já existe
$stmt = $pdo->prepare("SELECT id FROM empresas WHERE nome = ?");
$stmt->execute([$empresa]);
$empresaExistente = $stmt->fetch();

if (!$empresaExistente) {
    // Cria a empresa
    $stmt = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, '00.000.000/0000-00')");
    $stmt->execute([$empresa]);
    $empresaId = $pdo->lastInsertId();
} else {
    $empresaId = $empresaExistente['id'];
}

// Verifica se o e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("E-mail já cadastrado.");
}

// Cria o usuário
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, empresa_id, tipo) VALUES (?, ?, ?, ?, 'admin')");
$stmt->execute([$nome, $email, $senhaHash, $empresaId]);

header("Location: ../../public/index.php?page=login");
exit;
