<?php
// Mostrar erros 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Coleta dados das variáveis Railway
$host = getenv("DB_HOST") ?? 'mysql.railway.internal';
$db   = getenv("DB_NAME") ?? 'railway';
$user = getenv("DB_USER") ?? 'root';
$pass = getenv("DB_PASS") ?? 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
