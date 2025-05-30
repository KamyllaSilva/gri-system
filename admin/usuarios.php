<?php
session_start();
require_once '../includes/db.php';

// Verifica se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$usuarios = $pdo->query("SELECT id, nome, email, tipo FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Usuários - Admin</title>
</head>
<body>
    <h2>Usuários cadastrados</h2>
    <table border="1">
        <tr>
            <th>Nome</th><th>Email</th><th>Tipo</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['tipo']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
