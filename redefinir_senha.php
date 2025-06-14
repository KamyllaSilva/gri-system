<?php
declare(strict_types=1);
session_start();
require_once 'includes/db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $email = $_SESSION['email_verificacao'] ?? '';
    $codigoEsperado = $_SESSION['codigo_verificacao'] ?? '';

    if ($codigo == $codigoEsperado && $email && $novaSenha) {
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email = ?");
        $stmt->execute([$novaSenha, $email]);

        unset($_SESSION['codigo_verificacao'], $_SESSION['email_verificacao']);

        header("Location: login.php?senha_alterada=1");
        exit;
    } else {
        $mensagem = "Código inválido ou dados incompletos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Redefinir Senha</title></head>
<body>
    <h2>Digite o código e nova senha</h2>
    <?php if ($mensagem): ?><p style="color:red"><?= $mensagem ?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="codigo" placeholder="Código recebido" required>
        <input type="password" name="nova_senha" placeholder="Nova senha" required>
        <button type="submit">Redefinir senha</button>
    </form>
</body>
</html>
