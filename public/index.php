<?php
// Opcional: carregar sessão aqui
session_start();

// Lista branca das páginas permitidas
$allowedPages = ['login', 'dashboard', 'indicadores', 'editar_indicador', 'usuarios'];
$page = $_GET['page'] ?? 'login';

if (!in_array($page, $allowedPages)) {
    echo "Página não encontrada.";
    exit;
}

// Inclui conexão só quando precisar
if (in_array($page, ['dashboard', 'indicadores', 'editar_indicador', 'usuarios'])) {
    require_once __DIR__ . '/../backend/config/database.php';
}

switch ($page) {
    case 'login':
        include __DIR__ . '/../frontend/pages/login.php'; // prefira .php para páginas com lógica
        break;
    case 'dashboard':
        include __DIR__ . '/../frontend/pages/dashboard.php';
        break;
    case 'indicadores':
        include __DIR__ . '/../frontend/pages/indicadores.php';
        break;
    case 'editar_indicador':
        include __DIR__ . '/../frontend/pages/editar_indicador.php';
        break;
    case 'usuarios':
        include __DIR__ . '/../frontend/pages/usuarios.php';
        break;
}
