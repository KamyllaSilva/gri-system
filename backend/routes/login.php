<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../login.php");
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    $_SESSION['error'] = "Email e senha são obrigatórios.";
    header("Location: ../../login.php");
    exit();
}

// Buscar usuário no banco
$stmt = $pdo->prepare("SELECT u.id, u.nome, u.email, u.senha, u.tipo, u.empresa_id, e.nome AS empresa_nome 
                       FROM usuarios u
                       JOIN empresas e ON e.id = u.empresa_id
                       WHERE u.email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    $_SESSION['error'] = "Usuário ou senha inválidos.";
    header("Location: ../../login.php");
    exit();
}

// Verifica senha (supondo hash bcrypt)
if (!password_verify($senha, $usuario['senha'])) {
    $_SESSION['error'] = "Usuário ou senha inválidos.";
    header("Location: ../../login.php");
    exit();
}

// Login ok, criar sessão
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['usuario_nome'] = $usuario['nome'];
$_SESSION['empresa_id'] = $usuario['empresa_id'];
$_SESSION['empresa_nome'] = $usuario['empresa_nome'];
$_SESSION['tipo'] = $usuario['tipo'];

header("Location: ../../dashboard.php");
exit();
