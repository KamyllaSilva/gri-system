<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    $empresa_id = $_SESSION['empresa_id'];

    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
        $_SESSION['msg_error'] = "Preencha todos os campos.";
        header("Location: manage_users.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg_error'] = "Email inválido.";
        header("Location: manage_users.php");
        exit();
    }

    try {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['msg_error'] = "Email já cadastrado.";
            header("Location: manage_users.php");
            exit();
        }

        // Inserir usuário com senha hash
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $hashSenha, $tipo, $empresa_id]);

        $_SESSION['msg_success'] = "Usuário criado com sucesso.";
        header("Location: manage_users.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg_error'] = "Erro ao criar usuário: " . htmlspecialchars($e->getMessage());
        header("Location: manage_users.php");
        exit();
    }
} else {
    header("Location: manage_users.php");
    exit();
}
