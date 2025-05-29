<?php
session_start();
require_once 'includes/db.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Verifica envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = 'usuario'; // tipo padrão

    // Verifica se e-mail já existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $erro = "E-mail já cadastrado!";
    } else {
        // Insere novo usuário
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $tipo]);
        header("Location: index.php?cadastro=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sistema GRI</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Cadastro - Sistema GRI</h2>

    <?php if (isset($erro)): ?>
        <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nome:<br>
            <input type="text" name="nome" required>
        </label><br><br>

        <label>Email:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Senha:<br>
            <input type="password" name="senha" required>
        </label><br><br>

        <button type="submit">Cadastrar</button>
    </form>

    <p><a href="index.php">Voltar ao login</a></p>
</body>
</html>
