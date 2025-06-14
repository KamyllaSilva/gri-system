<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    die(json_encode(["error" => "Usuário ou empresa não autenticado."]));
}

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
$resposta_id = $_POST['resposta_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];
$empresa_id = $_SESSION['empresa_id'];

if (empty($indicador_id) || empty($resposta)) {
    die(json_encode(["error" => "Indicador e resposta são obrigatórios."]));
}

// Processar upload do arquivo
$caminho_arquivo = null;
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

// Iniciar transação para garantir consistência
$pdo->beginTransaction();

try {
    if (!empty($resposta_id)) {
        // ATUALIZAR resposta existente
        $sql = "UPDATE respostas_indicadores 
                SET resposta = ?, status = 'preenchido'
                WHERE id = ? AND empresa_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$resposta, $resposta_id, $empresa_id]);
        
        // Se há arquivo, inserir na tabela de evidências
        if ($caminho_arquivo) {
            $sqlEvidencia = "INSERT INTO evidencias 
                            (resposta_id, caminho_arquivo, tipo_arquivo)
                            VALUES (?, ?, ?)";
            $stmtEvidencia = $pdo->prepare($sqlEvidencia);
            $stmtEvidencia->execute([
                $resposta_id,
                $caminho_arquivo,
                $_FILES['arquivo']['type']
            ]);
        }
        
        $sucesso = 2; // Código para atualização
    } else {
        // NOVA resposta
        $sql = "INSERT INTO respostas_indicadores 
                (indicador_id, resposta, criado_por, empresa_id, status)
                VALUES (?, ?, ?, ?, 'preenchido')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$indicador_id, $resposta, $usuario_id, $empresa_id]);
        $resposta_id = $pdo->lastInsertId();
        
        // Se há arquivo, inserir na tabela de evidências
        if ($caminho_arquivo) {
            $sqlEvidencia = "INSERT INTO evidencias 
                            (resposta_id, caminho_arquivo, tipo_arquivo)
                            VALUES (?, ?, ?)";
            $stmtEvidencia = $pdo->prepare($sqlEvidencia);
            $stmtEvidencia->execute([
                $resposta_id,
                $caminho_arquivo,
                $_FILES['arquivo']['type']
            ]);
        }
        
        $sucesso = 1; // Código para criação
    }
    
    // Atualizar status do indicador na tabela indicadores
    $sqlUpdateIndicador = "UPDATE indicadores SET preenchido = 1 WHERE id = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdateIndicador);
    $stmtUpdate->execute([$indicador_id]);
    
    $pdo->commit();
    header("Location: indicadores.php?sucesso=$sucesso");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    die("Erro ao processar resposta: " . $e->getMessage());
}