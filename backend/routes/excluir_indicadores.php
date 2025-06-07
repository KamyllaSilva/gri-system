<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['empresa_id'])) {
    die("Acesso negado.");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

// Exclui apenas se for da empresa do usuário
$stmt = $pdo->prepare("DELETE FROM indicadores WHERE id = ? AND empresa_id = ?");
$stmt->execute([$id, $_SESSION['empresa_id']]);

header("Location: ../../public/index.php?page=indicadores");
exit;
