<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../public/index.php?page=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>Dashboard - GRI</title>
  <?php include __DIR__ . '/../includes/styles.php'; ?>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main>
  <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
  <p>Empresa: <?= htmlspecialchars($_SESSION['empresa_nome']) ?></p>

  <div id="dashboardGraficos">
    <h2>Gráficos de Indicadores</h2>
    <canvas id="graficoPorCodigo"></canvas>
    <canvas id="graficoEvolucao"></canvas>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="../../backend/routes/graficos.js.php"></script>
</body>
</html>
