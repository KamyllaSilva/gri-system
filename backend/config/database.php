<?php
// Configurações do banco - ideal usar variáveis de ambiente em produção
$host = getenv("DB_HOST") ?? 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?? 'railway';
$user = getenv("DB_USER") ?? 'root';
$pass = getenv("DB_PASS") ?? 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';
$charset = 'utf8mb4';

// DSN para PDO
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Options para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lança exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch assoc arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // usa prepared statements nativos
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Em produção, não exibir erro direto
    exit('Erro ao conectar no banco de dados: ' . $e->getMessage());
}
