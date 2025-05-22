<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_indicators.php");
    exit();
}

try {
    // Verifica se o indicador existe
    $stmt = $pdo->prepare("SELECT * FROM indicadores WHERE id = ?");
    $stmt->execute([$id]);
    $indicador = $stmt->fetch();

    if (!$indicador) {
        $_SESSION['msg_error'] = "Indicador não encontrado.";
        header("Location: manage_indicators.php");
        exit();
    }

    // Exclui o indicador
    $stmt = $pdo->prepare("DELETE FROM indicadores WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['msg_success'] = "Indicador excluído com sucesso.";
    header("Location: manage_indicators.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['msg_error'] = "Erro ao excluir indicador: " . htmlspecialchars($e->getMessage());
    header("Location: manage_indicators.php");
    exit();
}
