<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['empresa_id'])) {
    die("Acesso negado.");
}

$titulo = trim($_POST['titulo']);
$descricao = trim($_POST['descricao']);
$codigo_gri = trim($_POST['codigo_gri']);
$valor = floatval($_POST['valor']);
$data_referencia = $_POST['data_referencia'];

$stmt = $pdo->prepare("INSERT INTO indicadores (empresa_id, titulo, descricao, codigo_gri, valor, data_referencia) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $_SESSION['empresa_id'],
    $titulo,
    $descricao,
    $codigo_gri,
    $valor,
    $data_referencia
]);

header("Location: ../../public/index.php?page=indicadores");
exit;
