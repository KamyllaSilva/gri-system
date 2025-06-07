<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $indicadores = $pdo->query("SELECT id, codigo, descricao FROM indicadores ORDER BY codigo")->fetchAll();
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>

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

<?php include('header.php'); ?>

<main class="container">
    <section class="form-section">
        <h2>Preenchimento de Indicadores</h2>
        <form method="POST" action="salvar_indicador.php" enctype="multipart/form-data">
            <label for="indicador_id">Indicador GRI</label>
            <select name="indicador_id" id="indicador_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($indicadores as $ind): ?>
                    <option value="<?= $ind['id'] ?>">
                        <?= $ind['codigo'] ?> - <?= htmlspecialchars($ind['descricao']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="resposta">Resposta</label>
            <textarea name="resposta" id="resposta" rows="6" required></textarea>

            <label for="arquivo">Anexar Evidência</label>
            <input type="file" name="arquivo" id="arquivo">

            <button type="submit">Salvar Indicador</button>
        </form>
    </section>
</main>

<?php include('footer.php'); ?>

</body>
</html>
