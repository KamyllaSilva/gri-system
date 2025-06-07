<?php
session_start();

// Conexão com o banco
require_once __DIR__ . '/../backend/config/database.php';

// Define a página solicitada
$page = $_GET['page'] ?? 'login';

// Roteamento simples
switch ($page) {
    case 'login':
        include __DIR__ . '/../frontend/pages/login.php';
        break;

    case 'dashboard':
        // Protege rota
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ?page=login");
            exit;
        }
        include __DIR__ . '/../frontend/pages/dashboard.php';
        break;

    case 'indicadores':
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ?page=login");
            exit;
        }
        include __DIR__ . '/../frontend/pages/indicadores.php';
        break;

    case 'editar_indicador':
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ?page=login");
            exit;
        }
        include __DIR__ . '/../frontend/pages/editar_indicador.php';
        break;

    case 'usuarios':
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ?page=login");
            exit;
        }
        include __DIR__ . '/../frontend/pages/usuarios.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: ?page=login");
        exit;

    default:
        echo "Página não encontrada.";
        break;
}
