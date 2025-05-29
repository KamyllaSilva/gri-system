<?php
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Painel de Controle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Painel do Administrador</h2>
    <p>Bem-vindo, <?= $_SESSION['user_nome'] ?>!</p>
    <ul>
        <li><a href="cadastrar_usuario.php">Cadastrar Usuário</a></li>
        <li><a href="cadastrar_empresa.php">Cadastrar Empresa</a></li>
        <li><a href="../logout.php">Sair</a></li>
    </ul>
</body>
</html>
