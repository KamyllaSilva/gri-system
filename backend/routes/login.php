<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $_SESSION['error'] = 'Preencha todos os campos.';
        header('Location: ../../public/index.php?page=login');
        exit;
    }

    // Busca usuário pelo email
    $stmt = $pdo->prepare('SELECT id, nome, senha_hash, empresa_id, tipo FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        // Login ok
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['empresa_id'] = $usuario['empresa_id'];
        $_SESSION['tipo'] = $usuario['tipo']; // ex: 'admin' ou 'usuario'
        header('Location: ../../public/dashboard.php');
        exit;
    } else {
        $_SESSION['error'] = 'Email ou senha inválidos.';
        header('Location: ../../public/index.php?page=login');
        exit;
    }
} else {
    header('Location: ../../public/index.php?page=login');
    exit;
}
