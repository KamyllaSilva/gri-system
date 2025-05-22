<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_users.php");
    exit();
}

$id = $_POST['id'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$tipo = $_POST['tipo'] ?? '';
$senha = $_POST['senha'] ?? '';
$empresa_id = $_SESSION['empresa_id'];

if (!$id || !$nome || !$email || !$tipo) {
    $_SESSION['msg_error'] = "Preencha todos os campos obrigatórios.";
    header("Location: edit_user.php?id=$id");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg_error'] = "Email inválido.";
    header("Location: edit_user.php?id=$id");
    exit();
}

try {
    // Verifica se usuário pertence à empresa
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ? AND empresa_id = ?");
    $stmt->execute([$id, $empresa_id]);
    if (!$stmt->fetch()) {
        $_SESSION['msg_error'] = "Usuário não encontrado ou sem permissão.";
        header("Location: manage_users.php");
        exit();
    }

    // Verifica se email já existe para outro usuário
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id <> ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        $_SESSION['msg_error'] = "Email já cadastrado para outro usuário.";
        header("Location: edit_user.php?id=$id");
        exit();
    }

    if ($senha) {
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ?, senha = ? WHERE id = ? AND empresa_id = ?");
        $stmt->execute([$nome, $email, $tipo, $hashSenha, $id, $empresa_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id = ? AND empresa_id = ?");
        $stmt->execute([$nome, $email, $tipo, $id, $empresa_id]);
    }

    $_SESSION['msg_success'] = "Usuário atualizado com sucesso.";
    header("Location: manage_users.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Erro ao atualizar usuário: " . htmlspecialchars($e->getMessage());
    header("Location: edit_user.php?id=$id");
    exit();
}
