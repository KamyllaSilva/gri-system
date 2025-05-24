<?php
$mysqlUrl = getenv("MYSQL_URL");

if ($mysqlUrl) {
    $url = parse_url($mysqlUrl);
    $host = $url["host"];
    $port = $url["port"];
    $user = $url["user"];
    $pass = $url["pass"];
    $dbname = ltrim($url["path"], "/");

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
} else {
    die("MYSQL_URL não configurada.");
}
?>
