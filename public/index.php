<?php
require_once __DIR__ . '/../backend/config/database.php';

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        include __DIR__ . '/../frontend/pages/login.html';
        break;
    case 'dashboard':
        include __DIR__ . '/../frontend/pages/dashboard.html';
        break;
    case 'indicadores':
    include __DIR__ . '/../frontend/pages/indicadores.html';
    break;
    case 'editar_indicador':
    include __DIR__ . '/../frontend/pages/editar_indicador.php';
    break;
    case 'usuarios':
    include __DIR__ . '/../frontend/pages/usuarios.html';
    break;


    default:
        echo "Página não encontrada.";
}
