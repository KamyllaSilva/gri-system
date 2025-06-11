<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$empresa_id = $_SESSION['empresa_id'] ?? null;
if (!$empresa_id) {
    http_response_code(403);
    echo json_encode(['error' => 'Empresa não especificada']);
    exit;
}

require_once __DIR__ . '/conexao.php';

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ?");
    $stmt->execute([$empresa_id]);
    $total = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ? AND status = 'preenchido'");
    $stmt->execute([$empresa_id]);
    $preenchidos = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ? AND (status IS NULL OR TRIM(status) = '' OR status != 'preenchido')");
    $stmt->execute([$empresa_id]);
    $pendentes = (int) $stmt->fetchColumn();

    $sqlLista = "
        SELECT
            ri.id,
            ri.indicador_id,
            COALESCE(ri.valor, '') AS valor,
            COALESCE(ri.status, '') AS status,
            COALESCE(i.nome, 'Sem nome') AS nome
        FROM respostas_indicadores ri
        LEFT JOIN indicadores i ON ri.indicador_id = i.id
        WHERE ri.empresa_id = ?
        ORDER BY i.nome ASC
    ";
    $stmt = $pdo->prepare($sqlLista);
    $stmt->execute([$empresa_id]);
    $indicadores = $stmt->fetchAll();

    echo json_encode([
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $indicadores,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor']);
}
