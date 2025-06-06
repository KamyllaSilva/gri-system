<?php
$host = getenv("DB_HOST") ?? 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?? 'railway';
$user = getenv("DB_USER") ?? 'root';
$pass = getenv("DB_PASS") ?? 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';
$charset = 'utf8mb4';  

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Se der erro na conexão, exibe e interrompe
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
