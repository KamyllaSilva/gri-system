<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

if (isset($_POST['export_csv'])) {
    try {
        $stmt = $pdo->query("SELECT nome, descricao, meta FROM indicadores ORDER BY nome ASC");
        $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=relatorio_indicadores.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nome', 'Descrição', 'Meta']);

        foreach ($indicadores as $ind) {
            fputcsv($output, [$ind['nome'], $ind['descricao'], $ind['meta']]);
        }

        fclose($output);
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg_error'] = "Erro ao exportar CSV: " . htmlspecialchars($e->getMessage());
        header("Location: reports.php");
        exit();
    }
} else {
    header("Location: reports.php");
    exit();
}
