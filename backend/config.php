<?php
// Configurações de conexão - pega do ambiente ou usa valores padrão
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$db   = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';
$port = getenv("DB_PORT") ?: 3306;
$charset = 'utf8mb4';

// Monta DSN para PDO
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

// Opções PDO para melhor tratamento de erros e fetch
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Cria a conexão PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro na conexão com banco: ' . $e->getMessage()]);
    exit;
}
