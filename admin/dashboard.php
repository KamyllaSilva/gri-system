<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script> <!-- ícones -->
    <style>
        .card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }
        .stat-title {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
        }
        .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-100 to-white min-h-screen p-8">
    <h1 class="text-4xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500 mb-12">Painel de Controle</h1>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-10">
        <div class="card border-l-4 border-green-400">
            <div class="flex items-center justify-between">
                <span class="stat-title">Indicadores Preenchidos</span>
                <i data-lucide="check-square" class="text-green-500" width="24" height="24"></i>
            </div>
            <div class="stat-value text-green-500">75 / 120</div>
        </div>
        <div class="card border-l-4 border-blue-400">
            <div class="flex items-center justify-between">
                <span class="stat-title">Progresso Geral</span>
                <i data-lucide="bar-chart-2" class="text-blue-500" width="24" height="24"></i>
            </div>
            <div class="stat-value text-blue-500">62.5%</div>
        </div>
        <div class="card border-l-4 border-red-400">
            <div class="flex items-center justify-between">
                <span class="stat-title">Pendências Urgentes</span>
                <i data-lucide="alert-triangle" class="text-red-500" width="24" height="24"></i>
            </div>
            <div class="stat-value text-red-500">3</div>
        </div>
        <div class="card border-l-4 border-purple-400">
            <div class="flex items-center justify-between">
                <span class="stat-title">Usuários Ativos</span>
                <i data-lucide="users" class="text-purple-500" width="24" height="24"></i>
            </div>
            <div class="stat-value text-purple-500">12</div>
        </div>
    </div>

    <div class="glassmorphic p-6 max-w-4xl mx-auto text-center text-sm text-muted-foreground rounded">
        Este painel é uma visão geral do sistema. Acompanhe o progresso dos indicadores GRI, gerencie usuários e evidências.
        <div class="mt-6">
            <a href="manage_users.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition">Gerenciar Usuários</a>
            <a href="../user/indicators.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded ml-4 transition">Ver Indicadores</a>
            <a href="../logout.php" class="inline-block bg-gray-300 hover:bg-gray-400 text-black px-6 py-2 rounded ml-4 transition">Sair</a>
        </div>
    </div>

    <script>
        lucide.createIcons(); // inicializa ícones
    </script>
</body>
</html>
