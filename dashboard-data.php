<?php
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado ou empresa não identificada']);
    exit;
}

require_once __DIR__ . '/includes/conexao.php';

$empresa_id = (int) $_SESSION['empresa_id'];

if ($empresa_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da empresa inválido']);
    exit;
}

try {
    // Consulta agregada de totais
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

    // Lista os indicadores
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

    // Retorno para consumo no front-end
    echo json_encode([
        'status' => 'success',
        'dados' => [
            'total' => $total,
            'preenchidos' => $preenchidos,
            'pendentes' => $pendentes,
            'percentual_concluido' => $total > 0 ? round(($preenchidos / $total) * 100, 1) : 0.0,
            'indicadores' => $indicadores
        ]
    ]);

} catch (PDOException $e) {
    // Evita exibir detalhes internos ao usuário
    error_log("Erro no dashboard: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno no servidor']);
}
