<?php
session_start();

// Redireciona se o acesso não for via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// Recebe dados do formulário com segurança
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// Verifica se os campos estão preenchidos
if (empty($email) || empty($senha)) {
    header("Location: login.php?erro=1");
    exit();
}

// Conexão segura com o banco de dados
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Consulta usuário
$sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$usuario = $stmt->fetch();

// Valida usuário e senha
if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_tipo'] = $usuario['tipo']; // Opcional: tipo de usuário
    header("Location:../dashboard.php");
    exit();
}

// Redireciona em caso de erro
header("Location: login.php?erro=1");
exit();
