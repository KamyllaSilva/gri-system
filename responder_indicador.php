<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Conexão
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
    die("Erro ao conectar ao banco: " . $e->getMessage());
}

$empresa_id = $_SESSION['empresa_id'];
$usuario_id = $_SESSION['usuario_id'];

// Obtém o indicador
if (!isset($_GET['id'])) {
    die("Indicador não informado.");
}

$indicador_id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM indicadores WHERE id = ?");
$stmt->execute([$indicador_id]);
$indicador = $stmt->fetch();

if (!$indicador) {
    die("Indicador não encontrado.");
}

// Salvar resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resposta = $_POST['resposta'] ?? '';
    $evidencia = null;

    // Upload de arquivo
    if (!empty($_FILES['arquivo']['name'])) {
        $nome_arquivo = time() . '_' . basename($_FILES['arquivo']['name']);
        $caminho = 'uploads/' . $nome_arquivo;
        move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho);
        $evidencia = $nome_arquivo;
    }

    // Salva no banco
    $stmt = $pdo->prepare("
        INSERT INTO respostas_indicadores (empresa_id, indicador_id, resposta, evidencia, criado_por, status)
        VALUES (?, ?, ?, ?, ?, 'preenchido')
        ON DUPLICATE KEY UPDATE 
            resposta = VALUES(resposta),
            evidencia = VALUES(evidencia),
            status = 'preenchido'
    ");
    $stmt->execute([$empresa_id, $indicador_id, $resposta, $evidencia, $usuario_id]);

    header("Location: indicadores.php");
    exit();
}
?>
