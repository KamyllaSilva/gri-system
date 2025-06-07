<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// ✅ VERIFICAÇÃO DE SESSÃO
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado ou empresa não identificada']);
    exit;
}

// ✅ CONEXÃO
require_once __DIR__ . '/includes/conexao.php'; // certifique-se que $pdo está sendo criado dentro desse arquivo

if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Falha na conexão com o banco de dados']);
    exit;
}

// ✅ SANITIZAÇÃO
$empresa_id = filter_var($_SESSION['empresa_id'], FILTER_VALIDATE_INT);
if (!$empresa_id || $empresa_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID da empresa inválido']);
    exit;
}

try {
    // ✅ RESUMO DE INDICADORES
    $sqlResumo = "
        SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN preenchido = 1 THEN 1 ELSE 0 END) AS preenchidos,
            SUM(CASE WHEN preenchido = 0 THEN 1 ELSE 0 END) AS pendentes
        FROM indicadores
        WHERE empresa_id = ?
    ";
    $stmt = $pdo->prepare($sqlResumo);
    $stmt->execute([$empresa_id]);
    $resumo = $stmt->fetch(PDO::FETCH_ASSOC);

    $total = (int) ($resumo['total'] ?? 0);
    $preenchidos = (int) ($resumo['preenchidos'] ?? 0);
    $pendentes = (int) ($resumo['pendentes'] ?? 0);
    $percentual = $total > 0 ? round(($preenchidos / $total) * 100, 1) : 0.0;

    // ✅ LISTA DE INDICADORES
    $sqlLista = "
        SELECT 
            id, 
            nome, 
            COALESCE(valor, '') AS valor 
        FROM indicadores 
        WHERE empresa_id = ?
        ORDER BY nome ASC
    ";
    $stmt = $pdo->prepare($sqlLista);
    $stmt->execute([$empresa_id]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ RESPOSTA FINAL
    echo json_encode([
        'status' => 'success',
        'data' => [
            'total' => $total,
            'preenchidos' => $preenchidos,
            'pendentes' => $pendentes,
            'percentual_concluido' => $percentual,
            'indicadores' => $indicadores
        ]
    ]);

} catch (PDOException $e) {
    error_log("Erro no dashboard-data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro interno no servidor']);
}
