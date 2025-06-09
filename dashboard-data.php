<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
file_put_contents('log-dashboard.txt', 'Início dashboard-data.php'.PHP_EOL, FILE_APPEND);

error_reporting(E_ALL);

// Inicia a sessão antes de usar $_SESSION
session_start();

// Inclui o arquivo de autenticação
require_once 'includes/auth.php';

// Define o tipo de retorno como JSON
header('Content-Type: application/json');

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado']);
    file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);

    exit;
}

// Conexão com o banco
require_once __DIR__ . '/conexao.php';
file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);


// Verifica se a empresa está definida
$empresa_id = $_SESSION['empresa_id'] ?? null;
if (!$empresa_id) {
    http_response_code(403);
    echo json_encode(['error' => 'Empresa não especificada']);
    file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);

    exit;
}

// Função para contar indicadores
function contarIndicadores($pdo, $empresa_id, $condicaoExtra = ''): int {
    $sql = "SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ?" . $condicaoExtra;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$empresa_id]);
    return (int)$stmt->fetchColumn();
    file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);

}

try {
    $total = contarIndicadores($pdo, $empresa_id);
    $preenchidos = contarIndicadores($pdo, $empresa_id, " AND preenchido = 1");
    $pendentes = contarIndicadores($pdo, $empresa_id, " AND preenchido = 0");

    // Buscar indicadores detalhados
    $sqlLista = "SELECT id, nome, COALESCE(valor, '') AS valor FROM respostas_indicadores WHERE empresa_id = ? ORDER BY nome ASC";
    $stmt = $pdo->prepare($sqlLista);
    $stmt->execute([$empresa_id]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);


    // Resposta JSON
    echo json_encode([
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $indicadores
        file_put_contents('log-dashboard.txt', 'Empresa ID: ' . ($empresa_id ?? 'nulo') . PHP_EOL, FILE_APPEND);

    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
