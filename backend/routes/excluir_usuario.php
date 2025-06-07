<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SESSION['tipo'] !== 'admin') die("Acesso negado.");

$id = $_GET['id'];
$empresaId = $_SESSION['empresa_id'];

// Impede o admin de se excluir
if ($id == $_SESSION['usuario_id']) {
    die("Você não pode se excluir.");
}

$stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND empresa_id = ?");
$stmt->execute([$id, $empresaId]);

header("Location: ../../public/index.php?page=usuarios");
exit;
