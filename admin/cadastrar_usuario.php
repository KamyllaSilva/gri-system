<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuários - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="header">
        <img src="assets/img/logo.png" alt="Logo" class="logo-small">
        <h1>Sistema GRI</h1>
        <nav>
            <a href="dashboard.php">Painel</a>
            <a href="indicadores.php">Indicadores</a>
            <a href="usuarios.php">Usuários</a>
            <a href="logout.php">Sair</a>
        </nav>
    </header>

    <main class="container">
        <section class="form-section">
            <h2>Cadastro de Usuário</h2>
            <form method="POST" action="criar_usuario.php">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>

                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required>

                <label for="permissao">Permissão</label>
                <select name="permissao" id="permissao" required>
                    <option value="admin">Administrador</option>
                    <option value="usuario">Usuário</option>
                </select>

                <button type="submit">Cadastrar</button>
            </form>
        </section>
    </main>
</body>
</html>