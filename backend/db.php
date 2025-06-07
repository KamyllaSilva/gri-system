<?php
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$db   = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';
$port = getenv("DB_PORT") ?: 3306;

// Conexão segura com Railway
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("❌ Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// echo "✅ Conexão estabelecida com sucesso!";
?>
