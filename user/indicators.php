<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

// Exemplo de dados (no real, você puxará do banco)
$indicadores = [
    ['id' => 'GRI 102-1', 'nome' => 'Nome da organização', 'categoria' => 'Geral', 'status' => 'Completo'],
    ['id' => 'GRI 201-1', 'nome' => 'Valor econômico direto gerado e distribuído', 'categoria' => 'Econômico', 'status' => 'Pendente'],
    ['id' => 'GRI 302-1', 'nome' => 'Consumo de energia', 'categoria' => 'Ambiental', 'status' => 'Em Progresso'],
    ['id' => 'GRI 401-1', 'nome' => 'Rotatividade de funcionários', 'categoria' => 'Social', 'status' => 'Não Iniciado'],
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Indicadores GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status {
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            padding: 2px 8px;
            border-radius: 999px;
        }
        .status.Completo {
            background-color: #22c55e;
        }
        .status.Pendente {
            background-color: #facc15;
        }
.status.Em\ Progresso {
    background-color: #3b82f6;
}
.status.Não\ Iniciado { 
    background-color: #9ca3af;
}
    </style>
</head>
<body class="bg-gradient-to-br from-purple-100 to-white min-h-screen p-6">

    <h1 class="text-4xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500 mb-10">Indicadores GRI</h1>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php foreach ($indicadores as $item): ?>
            <div class="glassmorphic p-6 rounded shadow-lg flex flex-col justify-between h-full">
                <div class="mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="text-lg font-semibold"><?php echo $item['id']; ?></h2>
                        <span class="status <?php echo $item['status']; ?>"><?php echo $item['status']; ?></span>
                    </div>
                    <p class="text-sm text-muted-foreground"><?php echo $item['nome']; ?></p>
                </div>
                <p class="text-xs text-muted-foreground mb-4">Categoria: <strong><?php echo $item['categoria']; ?></strong></p>
                <a href="preencher.php?id=<?php echo $item['id']; ?>" class="bg-purple-600 text-white text-sm text-center py-2 px-4 rounded hover:bg-purple-700 transition">
                    Preencher / Ver Detalhes
                </a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
