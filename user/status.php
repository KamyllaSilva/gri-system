<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$usuario_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, i.codigo, i.descricao, e.caminho_arquivo
    FROM respostas r
    JOIN indicadores i ON r.indicador_id = i.id
    LEFT JOIN evidencias e ON e.resposta_id = r.id
    WHERE r.usuario_id = ?
    ORDER BY r.data_resposta DESC
");
$stmt->execute([$usuario_id]);
$respostas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Status dos Indicadores</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status {
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            padding: 4px 10px;
            border-radius: 999px;
        }
        .Preenchido { background-color: #22c55e; }
        .Pendente { background-color: #facc15; }
        .Revisado { background-color: #3b82f6; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-100 to-white min-h-screen p-6">
    <h1 class="text-4xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500 mb-10">
        Status dos Indicadores
    </h1>

    <div class="grid gap-6 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3">
        <?php foreach ($respostas as $r): ?>
            <div class="glassmorphic p-6 rounded shadow-md flex flex-col justify-between h-full">
                <div class="mb-2">
                    <div class="flex justify-between items-start mb-1">
                        <h2 class="text-lg font-semibold"><?= htmlspecialchars($r['codigo']) ?></h2>
                        <span class="status <?= $r['status'] ?>"><?= $r['status'] ?></span>
                    </div>
                    <p class="text-sm text-muted-foreground"><?= htmlspecialchars($r['descricao']) ?></p>
                </div>

                <p class="text-xs text-muted-foreground mt-2 mb-1">
                    Respondido em: <?= date('d/m/Y H:i', strtotime($r['data_resposta'])) ?>
                </p>

                <?php if ($r['caminho_arquivo']): ?>
                    <a href="<?= $r['caminho_arquivo'] ?>" target="_blank" class="text-sm text-blue-600 hover:underline">
                        Ver Evidência
                    </a>
                <?php else: ?>
                    <p class="text-sm text-gray-400 italic">Sem evidência anexada.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
