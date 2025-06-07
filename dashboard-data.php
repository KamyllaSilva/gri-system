<?php
session_start();
header('Content-Type: application/json');

// Verifica se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Inclui conexão com o banco
require_once __DIR__ . '/../includes/conexao.php';


// Pega o id da empresa do usuário logado (supondo que você tenha isso na sessão)
$empresa_id = $_SESSION['empresa_id'] ?? 0;

try {
    // Consulta total de indicadores da empresa
    $sqlTotal = "SELECT COUNT(*) FROM indicadores WHERE empresa_id = ?";
    $stmt = $pdo->prepare($sqlTotal);
    $stmt->execute([$empresa_id]);
    $total = (int)$stmt->fetchColumn();

    // Consulta indicadores preenchidos
    $sqlPreenchidos = "SELECT COUNT(*) FROM indicadores WHERE empresa_id = ? AND preenchido = 1";
    $stmt = $pdo->prepare($sqlPreenchidos);
    $stmt->execute([$empresa_id]);
    $preenchidos = (int)$stmt->fetchColumn();

    // Consulta indicadores pendentes
    $sqlPendentes = "SELECT COUNT(*) FROM indicadores WHERE empresa_id = ? AND preenchido = 0";
    $stmt = $pdo->prepare($sqlPendentes);
    $stmt->execute([$empresa_id]);
    $pendentes = (int)$stmt->fetchColumn();

    // Lista os indicadores para o dashboard
    $sqlLista = "SELECT id, nome, valor FROM indicadores WHERE empresa_id = ?";
    $stmt = $pdo->prepare($sqlLista);
    $stmt->execute([$empresa_id]);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os dados em JSON
    echo json_encode([
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $indicadores
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}
