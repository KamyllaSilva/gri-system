<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Indicadores - Sistema GRI</title>
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
            <h2>Preenchimento de Indicadores</h2>
            <form method="POST" action="salvar_indicador.php" enctype="multipart/form-data">
                <label for="indicador">Indicador GRI</label>
                <select name="indicador" id="indicador" required>
                    <option value="">Selecione...</option>
                    <option value="GRI-102-1">GRI 102-1: Nome da organização</option>
                    <!-- Mais indicadores -->
                </select>

                <label for="resposta">Resposta</label>
                <textarea name="resposta" id="resposta" rows="6" required></textarea>

                <label for="arquivo">Anexar Evidência</label>
                <input type="file" name="arquivo" id="arquivo">

                <button type="submit">Salvar Indicador</button>
            </form>
        </section>
    </main>
</body>
</html>
