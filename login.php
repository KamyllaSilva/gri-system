<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container">
        <div class="login-box">
            <img src="assets/img/logo.png" alt="Logo Sistema GRI" class="logo-small">
            <h2>Bem-vindo de volta</h2>
            <p class="subtitle">Faça login para continuar</p>

            <?php if (isset($_GET['erro'])): ?>
                <p class="error-message">Usuário ou senha inválidos!</p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>

                <button type="submit">Entrar</button>
            </form>

            <p class="register-link">Não tem conta? <a href="register.php">Cadastre-se</a></p>
        </div>
    </main>
</body>
</html>