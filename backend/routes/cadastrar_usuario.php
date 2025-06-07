<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['empresa_id']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso negado.");
}

$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = password_hash(trim($_POST['senha']), PASSWORD_DEFAULT);
$tipo  = ($_POST['tipo'] === 'admin') ? 'admin' : 'usuario';

// Verifica se e-mail já existe
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("E-mail já cadastrado.");
}

$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, empresa_id, tipo) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$nome, $email, $senha, $_SESSION['empresa_id'], $tipo]);

header("Location: ../../public/index.php?page=usuarios");
exit;
