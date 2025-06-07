<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['empresa_id'])) {
    die("Acesso negado.");
}

$id = $_POST['id'];
$titulo = trim($_POST['titulo']);
$descricao = trim($_POST['descricao']);
$codigo_gri = trim($_POST['codigo_gri']);
$valor = floatval($_POST['valor']);
$data_referencia = $_POST['data_referencia'];

// Atualiza apenas se for da mesma empresa
$stmt = $pdo->prepare("
  UPDATE indicadores
  SET titulo = ?, descricao = ?, codigo_gri = ?, valor = ?, data_referencia = ?
  WHERE id = ? AND empresa_id = ?
");
$stmt->execute([
    $titulo,
    $descricao,
    $codigo_gri,
    $valor,
    $data_referencia,
    $id,
    $_SESSION['empresa_id']
]);

header("Location: ../../public/index.php?page=indicadores");
exit;
