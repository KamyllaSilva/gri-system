<?php
session_start();

// Recebe dados do formulário
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// Configura variáveis do banco de dados
$host = getenv("DB_HOST") ?? 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?? 'railway';
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

// Agora prepare e execute usando $pdo
$query = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    header("Location: dashboard.php");
    exit;
}

header("Location: index.php?erro=1");
exit;
