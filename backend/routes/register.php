<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];

    if ($senha !== $senha_confirm) {
        $_SESSION['error'] = "As senhas não coincidem.";
        header("Location: ../../frontend/pages/register.php");
        exit();
    }

    // Verifica se email já existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = "Email já cadastrado.";
        header("Location: ../../frontend/pages/register.php");
        exit();
    }

    // Cria hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash]);

    // Redireciona para login com sucesso
    $_SESSION['success'] = "Cadastro realizado com sucesso. Faça login.";
    header("Location: ../../frontend/pages/login.php");
    exit();
} else {
    header("Location: ../../frontend/pages/register.php");
    exit();
}
