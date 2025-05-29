<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema GRI</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="assets/img/logo.png" alt="Logo Sistema GRI" class="logo">

            <h2>Bem-vindo de volta</h2>
            <p class="subtitle">Faça login para continuar</p>

            <?php if (isset($_GET['erro'])): ?>
                <p class="error-message">Usuário ou senha inválidos!</p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Digite seu email" required>

                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Digite sua senha" required>

                <button type="submit">Entrar</button>
            </form>

            <p class="register-link">
                Não tem conta? <a href="register.php">Cadastre-se</a>
            </p>
        </div>
    </div>
</body>
</html>

