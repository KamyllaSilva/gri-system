<?php
header("Content-Type: application/javascript");
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['empresa_id'])) {
    http_response_code(403);
    exit;
}

$empresaId = $_SESSION['empresa_id'];

// Dados para gráfico 1 (Quantidade por código GRI)
$stmt1 = $pdo->prepare("SELECT codigo_gri, COUNT(*) AS total FROM indicadores WHERE empresa_id = ? GROUP BY codigo_gri");
$stmt1->execute([$empresaId]);
$griLabels = [];
$griData = [];
foreach ($stmt1->fetchAll() as $row) {
    $griLabels[] = $row['codigo_gri'];
    $griData[] = (int)$row['total'];
}

// Dados para gráfico 2 (Evolução mensal)
$stmt2 = $pdo->prepare("SELECT DATE_FORMAT(data_referencia, '%Y-%m') AS mes, SUM(valor) AS total FROM indicadores WHERE empresa_id = ? GROUP BY mes ORDER BY mes");
$stmt2->execute([$empresaId]);
$meses = [];
$valores = [];
foreach ($stmt2->fetchAll() as $row) {
    $meses[] = $row['mes'];
    $valores[] = (float)$row['total'];
}

?>

document.addEventListener("DOMContentLoaded", function() {
    new Chart(document.getElementById('graficoPorCodigo'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($griLabels) ?>,
            datasets: [{
                label: 'Indicadores por Código GRI',
                data: <?= json_encode($griData) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        }
    });

    new Chart(document.getElementById('graficoEvolucao'), {
        type: 'line',
        data: {
            labels: <?= json_encode($meses) ?>,
            datasets: [{
                label: 'Evolução dos Indicadores (R$)',
                data: <?= json_encode($valores) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false
            }]
        }
    });
});
