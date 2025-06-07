<?php
session_start();
if (!isset($_SESSION['empresa_id'])) {
    header("Location: ../../public/index.php?page=login");
    exit;
}

require_once __DIR__ . '/../../backend/config/database.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID não fornecido.");
}

// Busca o indicador
$stmt = $pdo->prepare("SELECT * FROM indicadores WHERE id = ? AND empresa_id = ?");
$stmt->execute([$id, $_SESSION['empresa_id']]);
$indicador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$indicador) {
    die("Indicador não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Indicador - GRI</title>
</head>
<body>
  <h1>Editar Indicador</h1>
  <form method="POST" action="../../backend/routes/atualizar_indicador.php">
    <input type="hidden" name="id" value="<?= $indicador['id'] ?>">
    <input type="text" name="titulo" value="<?= $indicador['titulo'] ?>" required />
    <input type="text" name="codigo_gri" value="<?= $indicador['codigo_gri'] ?>" required />
    <textarea name="descricao"><?= $indicador['descricao'] ?></textarea>
    <input type="number" step="0.01" name="valor" value="<?= $indicador['valor'] ?>" required />
    <input type="date" name="data_referencia" value="<?= $indicador['data_referencia'] ?>" required />
    <button type="submit">Atualizar</button>
  </form>
  <p><a href="../../public/index.php?page=indicadores">← Voltar</a></p>
</body>
</html>
