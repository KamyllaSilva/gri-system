<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$indicador_id = $_GET['id'] ?? '';
$mensagem = "";

// Lógica de envio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';

    $resposta = $_POST['resposta'];
    $usuario_id = $_SESSION['user_id'];
    $empresa_id = $_SESSION['empresa_id'];

    // Salvar resposta
    $stmt = $pdo->prepare("INSERT INTO respostas (usuario_id, indicador_id, empresa_id, resposta, status) VALUES (?, ?, ?, ?, 'preenchido')");
    $stmt->execute([$usuario_id, $indicador_id, $empresa_id, $resposta]);

    $resposta_id = $pdo->lastInsertId();

    // Upload do arquivo
    if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
        $pasta = "../uploads/";
        $nome_arquivo = basename($_FILES["evidencia"]["name"]);
        $caminho = $pasta . time() . "-" . $nome_arquivo;
        move_uploaded_file($_FILES["evidencia"]["tmp_name"], $caminho);

        $tipo = mime_content_type($caminho);

        $stmt2 = $pdo->prepare("INSERT INTO evidencias (resposta_id, caminho_arquivo, tipo_arquivo) VALUES (?, ?, ?)");
        $stmt2->execute([$resposta_id, $caminho, $tipo]);
    }

    $mensagem = "Indicador preenchido e evidência enviada!";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Preencher Indicador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gradient-to-br from-purple-100 to-white min-h-screen p-6 flex items-center justify-center">
    <div class="glassmorphic w-full max-w-2xl p-8 rounded shadow-xl">
        <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Preencher Indicador</h2>
        <p class="text-center mb-2 text-sm text-muted-foreground">Indicador: <strong><?= htmlspecialchars($indicador_id) ?></strong></p>

        <?php if ($mensagem): ?>
            <p class="text-green-600 text-sm text-center mb-4"><?= $mensagem ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Resposta:</label>
                <textarea name="resposta" rows="5" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Anexar Evidência (PDF, Imagem...):</label>
                <input type="file" name="evidencia" accept=".pdf,.jpg,.jpeg,.png" class="w-full border rounded p-2">
            </div>

            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">
                Enviar
            </button>
        </form>
    </div>
</body>
</html>
