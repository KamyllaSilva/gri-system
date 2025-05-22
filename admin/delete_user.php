<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$id = $_GET['id'] ?? null;
$empresa_id = $_SESSION['empresa_id'];

if (!$id) {
    $_SESSION['msg_error'] = "ID do usuário não informado.";
    header("Location: manage_users.php");
    exit();
}

try {
    // Verificar se usuário pertence à mesma empresa
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE id = ? AND empresa_id = ?");
    $stmt->execute([$id, $empresa_id]);
    if (!$stmt->fetch()) {
        $_SESSION['msg_error'] = "Usuário não encontrado ou sem permissão.";
        header("Location: manage_users.php");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['msg_success'] = "Usuário excluído com sucesso.";
    header("Location: manage_users.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Erro ao excluir usuário: " . htmlspecialchars($e->getMessage());
    header("Location: manage_users.php");
    exit();
}
