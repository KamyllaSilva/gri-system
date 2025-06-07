<?php
header('Content-Type: application/json');
require_once 'config.php';
require_once 'controllers/IndicadorController.php';

$controller = new IndicadorController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// Extrai caminho para rota simples (ex: /api/indicadores ou /api/indicadores/3)
$uri = parse_url($request, PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

if ($segments[0] === 'api' && $segments[1] === 'indicadores') {
    $id = $segments[2] ?? null;

    switch ($method) {
        case 'GET':
            if ($id) {
                $controller->buscar($id);
            } else {
                $controller->listar();
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->criar($data);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID do indicador é obrigatório para atualizar']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->atualizar($id, $data);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID do indicador é obrigatório para excluir']);
                break;
            }
            $controller->excluir($id);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método HTTP não permitido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}
