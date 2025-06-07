<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$email = trim($_POST['email']);
$senha = trim($_POST['senha']);

$stmt = $pdo->prepare("SELECT u.*, e.nome as empresa_nome FROM usuarios u JOIN empresas e ON u.empresa_id = e.id WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    die("E-mail ou senha inválidos.");
}

// Autenticado com sucesso
$_SESSION['usuario_id']   = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome'];
$_SESSION['empresa_id']   = $usuario['empresa_id'];
$_SESSION['empresa_nome'] = $usuario['empresa_nome'];
$_SESSION['tipo']         = $usuario['tipo'];

header("Location: ../../public/index.php?page=dashboard");
exit;
