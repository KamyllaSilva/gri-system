<?php
declare(strict_types=1);
session_start();
require_once 'includes/db.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $codigo = rand(100000, 999999);
        $_SESSION['codigo_verificacao'] = $codigo;
        $_SESSION['email_verificacao'] = $email;

        require_once 'includes/mailer.php';

        if (!enviarCodigoRedefinicao($email, $codigo)) {
            $mensagem = "Erro ao enviar e-mail. Tente novamente.";
        } else {
            header("Location: redefinir_senha.php");
            exit;
        }

    } else {
        $mensagem = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
</head>
<body>
    <h2>Esqueci minha senha</h2>
    <?php if ($mensagem): ?>
        <p style="color:red"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Seu e-mail" required>
        <button type="submit">Enviar código</button>
    </form>
</body>
</html>
