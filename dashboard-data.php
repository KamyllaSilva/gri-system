<?php
session_start();
header('Content-Type: application/json');

// Verificação de sessão
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Configurações do banco, preferindo variáveis de ambiente
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

// Conexão com MySQLi
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão: ' . $conn->connect_error]);
    exit;
}

// Consulta total de indicadores
$sqlTotal = "SELECT COUNT(*) AS total FROM indicadores";
$resultTotal = $conn->query($sqlTotal);
$total = 0;
if ($resultTotal && $row = $resultTotal->fetch_assoc()) {
    $total = (int)$row['total'];
}

// Consulta indicadores preenchidos (campo status = 'preenchido')
$sqlPreenchidos = "SELECT COUNT(*) AS preenchidos FROM indicadores WHERE status = 'preenchido'";
$resultPreenchidos = $conn->query($sqlPreenchidos);
$preenchidos = 0;
if ($resultPreenchidos && $row = $resultPreenchidos->fetch_assoc()) {
    $preenchidos = (int)$row['preenchidos'];
}

$pendentes = $total - $preenchidos;

// Retorna JSON
echo json_encode([
    'total' => $total,
    'preenchidos' => $preenchidos,
    'pendentes' => $pendentes,
]);

$conn->close();
