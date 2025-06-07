<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Corrigido: caminho para o novo local de conexao.php
require_once __DIR__ . '/includes/conexao.php';

// Pega o id da empresa do usuário logado
$empresa_id = $_SESSION['empresa_id'] ?? null;
if (!$empresa_id) {
    http_response_code(403);
    echo json_encode(['error' => 'Empresa não especificada']);
    exit;
}

try {
    $sqlTotal = "SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ?";
    $stmt = $pdo->prepare($sqlTotal);
    $stmt->execute([$empresa_id]);
    $total = (int)$stmt->fetchColumn();

    $sqlPreenchidos = "SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ? AND preenchido = 1";
    $stmt = $pdo->prepare($sqlPreenchidos);
    $stmt->execute([$empresa_id]);
    $preenchidos = (int)$stmt->fetchColumn();

    $sqlPendentes = "SELECT COUNT(*) FROM respostas_indicadores WHERE empresa_id = ? AND preenchido = 0";
    $stmt = $pdo->prepare($sqlPendentes);
    $stmt->execute([$empresa_id]);
    $pendentes = (int)$stmt->fetchColumn();

    $sqlLista = "SELECT id, nome, valor FROM respostas_indicadores WHERE empresa_id = ?";
    $stmt = $pdo->prepare($sqlLista);
    $stmt->execute([$empresa_id]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $indicadores
    ];

    // Envia resposta ao frontend
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
