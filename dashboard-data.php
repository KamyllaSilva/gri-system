<?php
session_start();
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');

// Verifica se o usuário está autenticado e tem uma empresa vinculada
if (empty($_SESSION['usuario_id']) || empty($_SESSION['empresa_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

$empresaId = $_SESSION['empresa_id'];

try {
    // Contagem geral de indicadores
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN valor IS NOT NULL THEN 1 ELSE 0 END) as preenchidos,
            SUM(CASE WHEN valor IS NULL THEN 1 ELSE 0 END) as pendentes
        FROM indicadores
        WHERE empresa_id = :empresa_id
    ");
    $stmt->execute(['empresa_id' => $empresaId]);
    $resumo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta de todos os indicadores para agrupamento por categoria
    $stmt = $pdo->prepare("
        SELECT id, nome, valor, categoria
        FROM indicadores
        WHERE empresa_id = :empresa_id
        ORDER BY categoria IS NULL, categoria ASC, nome ASC
    ");
    $stmt->execute(['empresa_id' => $empresaId]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por categoria
    $agrupados = [];
    foreach ($indicadores as $ind) {
        $categoria = $ind['categoria'] ?: 'Sem Categoria';
        if (!isset($agrupados[$categoria])) {
            $agrupados[$categoria] = [];
        }
        $agrupados[$categoria][] = [
            'id' => (int) $ind['id'],
            'nome' => $ind['nome'],
            'valor' => is_numeric($ind['valor']) ? (float) $ind['valor'] : null
        ];
    }

    // Resposta JSON estruturada
    echo json_encode([
        'total' => (int) $resumo['total'],
        'preenchidos' => (int) $resumo['preenchidos'],
        'pendentes' => (int) $resumo['pendentes'],
        'indicadores' => $agrupados
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao consultar o banco de dados.']);
}
