<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cnpj = $_POST['cnpj'];

    $stmt = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
    $stmt->execute([$nome, $cnpj]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Empresa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Cadastrar Nova Empresa</h2>
    <form method="POST">
        <label>Nome da Empresa:</label><br>
        <input type="text" name="nome" required><br><br>
        <label>CNPJ:</label><br>
        <input type="text" name="cnpj"><br><br>
        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="dashboard.php">← Voltar</a></p>
</body>
</html>
