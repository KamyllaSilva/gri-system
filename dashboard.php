<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: frontend/pages/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard - Sistema GRI</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
<header>
  <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
  <p>Empresa: <?= htmlspecialchars($_SESSION['empresa_nome']) ?></p>
  <nav>
    <a href="dashboard.php?page=indicadores">Gerenciar Indicadores</a> |
    <?php if ($_SESSION['tipo'] === 'admin'): ?>
    <a href="dashboard.php?page=usuarios">Gerenciar Usuários</a> |
    <?php endif; ?>
    <a href="backend/routes/logout.php">Sair</a>
  </nav>
</header>

<main>
  <h2>Gráficos de Indicadores</h2>
  <div id="dashboardGraficos" style="max-width:900px; margin: 20px auto;">
    <canvas id="graficoPorCodigo" style="width:100%; height:300px; border:1px solid #c1d3f8; background:#fff; border-radius:5px; margin-bottom:40px;"></canvas>
    <canvas id="graficoEvolucao" style="width:100%; height:300px; border:1px solid #c1d3f8; background:#fff; border-radius:5px;"></canvas>
  </div>
</main>

<script src="backend/routes/graficos.js.php"></script>
</body>
</html>
