<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SESSION['tipo'] !== 'admin') die("Acesso negado.");

$id = $_GET['id'];
$empresaId = $_SESSION['empresa_id'];

// Busca usuário
$stmt = $pdo->prepare("SELECT tipo FROM usuarios WHERE id = ? AND empresa_id = ?");
$stmt->execute([$id, $empresaId]);
$usuario = $stmt->fetch();

if (!$usuario) die("Usuário não encontrado.");

// Alterna tipo
$novoTipo = $usuario['tipo'] === 'admin' ? 'usuario' : 'admin';

$stmt = $pdo->prepare("UPDATE usuarios SET tipo = ? WHERE id = ? AND empresa_id = ?");
$stmt->execute([$novoTipo, $id, $empresaId]);

header("Location: ../../public/index.php?page=usuarios");
exit;
