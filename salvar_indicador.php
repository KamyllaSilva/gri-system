<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifica se o usuário está autenticado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    die(json_encode(["error" => "Usuário ou empresa não autenticado."]));
}

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: indicadores.php");
    exit();
}

// Conexão com o banco
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Erro na conexão com o banco: " . $e->getMessage());
}

// Dados do formulário
$indicador_id = $_POST['indicador_id'] ?? null;
$resposta = trim($_POST['resposta'] ?? '');
$usuario_id = $_SESSION['usuario_id'];
$empresa_id = $_SESSION['empresa_id'];
$caminho_arquivo = null;

// Upload do arquivo (se houver)
if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
    $pasta_destino = 'uploads/';
    if (!is_dir($pasta_destino)) {
        mkdir($pasta_destino, 0755, true);
    }

    $nome_original = basename($_FILES['arquivo']['name']);
    $extensao = pathinfo($nome_original, PATHINFO_EXTENSION);
    $nome_novo = uniqid('evid_', true) . '.' . $extensao;
    $caminho_arquivo = $pasta_destino . $nome_novo;

    if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminho_arquivo)) {
        die("Erro ao fazer upload do arquivo.");
    }
}

// Inserção no banco
$sql = "INSERT INTO respostas_indicadores (indicador_id, resposta, evidencia, criado_por, empresa_id, status)
        VALUES (?, ?, ?, ?, ?, 'preenchido')";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$indicador_id, $resposta, $caminho_arquivo, $usuario_id, $empresa_id]);
    header("Location: indicadores.php?sucesso=1");
    exit();
} catch (PDOException $e) {
    die("Erro ao salvar resposta: " . $e->getMessage());
}
?>
