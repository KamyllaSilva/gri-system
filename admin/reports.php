<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$page_title = "Relatórios - Indicadores GRI";
include '../includes/header.php';

try {
    // Aqui você pode personalizar a query conforme os dados que quer no relatório
    $stmt = $pdo->query("SELECT nome, descricao, meta FROM indicadores ORDER BY nome ASC");
    $indicadores = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar indicadores: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<section>
    <h2>Relatório de Indicadores</h2>

    <?php if (!empty($indicadores)): ?>
    <table border="1" cellpadding="8" style="width:100%; border-collapse: collapse;">
        <thead style="background-color:#004080; color:#fff;">
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Meta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indicadores as $ind): ?>
            <tr>
                <td><?php echo htmlspecialchars($ind['nome']); ?></td>
                <td><?php echo htmlspecialchars($ind['descricao']); ?></td>
                <td><?php echo htmlspecialchars($ind['meta']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form method="POST" action="export_csv.php" style="margin-top:20px;">
        <button type="submit" name="export_csv">Exportar CSV</button>
    </form>

    <?php else: ?>
        <p>Nenhum indicador encontrado.</p>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
