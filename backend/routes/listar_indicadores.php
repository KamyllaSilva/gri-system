<?php
require_once __DIR__ . '/../config/database.php';

$empresaId = $_SESSION['empresa_id'];

$stmt = $pdo->prepare("SELECT * FROM indicadores WHERE empresa_id = ? ORDER BY data_referencia DESC");
$stmt->execute([$empresaId]);
$indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($indicadores) === 0) {
    echo "<p>Nenhum indicador cadastrado.</p>";
} else {
    echo "<table border='1' cellpadding='6'>";
    echo "<tr><th>Título</th><th>Código GRI</th><th>Valor</th><th>Data</th><th>Ações</th></tr>";
    foreach ($indicadores as $i) {
        echo "<tr>";
        echo "<td>{$i['titulo']}</td>";
        echo "<td>{$i['codigo_gri']}</td>";
        echo "<td>{$i['valor']}</td>";
        echo "<td>" . date('d/m/Y', strtotime($i['data_referencia'])) . "</td>";
        echo "<td><!-- editar/excluir futuramente --></td>";
        echo "<td>
  <a href='../../public/index.php?page=editar_indicador&id={$i['id']}'>Editar</a> |
  <a href='../../backend/routes/excluir_indicador.php?id={$i['id']}' onclick=\"return confirm('Deseja excluir este indicador?')\">Excluir</a>
</td>";

        echo "</tr>";

    }
    echo "</table>";
}
