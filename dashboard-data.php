<?php
session_start();
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['usuario_id']) || empty($_SESSION['empresa_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado.']);
    exit;
}

$empresaId = $_SESSION['empresa_id'];

try {
    // Busca todos os indicadores com as respostas (se houver)
    $stmt = $pdo->prepare("
        SELECT i.id, i.nome, i.categoria,
               r.valor AS resposta_valor
        FROM indicadores i
        LEFT JOIN respostas_indicadores r
          ON r.indicador_id = i.id AND r.empresa_id = :empresa_id
        WHERE i.empresa_id = :empresa_id
        ORDER BY i.categoria IS NULL, i.categoria ASC, i.nome ASC
    ");
    $stmt->execute(['empresa_id' => $empresaId]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar total, preenchidos e pendentes
    $total = count($indicadores);
    $preenchidos = 0;
    $pendentes = 0;

    $agrupados = [];

    foreach ($indicadores as $ind) {
        $categoria = $ind['categoria'] ?: 'Sem Categoria';
        if (!isset($agrupados[$categoria])) {
            $agrupados[$categoria] = [];
        }

        $preenchido = $ind['resposta_valor'] !== null;

        if ($preenchido) {
            $preenchidos++;
        } else {
            $pendentes++;
        }

        $agrupados[$categoria][] = [
            'id' => (int) $ind['id'],
            'nome' => $ind['nome'],
            'valor' => $preenchido ? (float) $ind['resposta_valor'] : null,
        ];
    }

    echo json_encode([
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $agrupados,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao consultar o banco de dados.']);
}
