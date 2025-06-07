<?php
require_once __DIR__ . '/../models/indicador.php';

class IndicadorController {
    private $model;

    public function __construct($pdo) {
        $this->model = new Indicador($pdo);
    }

    public function listar() {
        $indicadores = $this->model->listarTodos();
        echo json_encode($indicadores);
    }

    public function buscar($id) {
        $indicador = $this->model->buscarPorId($id);
        if ($indicador) {
            echo json_encode($indicador);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Indicador não encontrado']);
        }
    }

    public function criar($data) {
        if (!isset($data['codigo'], $data['descricao'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios faltando']);
            return;
        }

        $codigo = trim($data['codigo']);
        $descricao = trim($data['descricao']);
        $resposta = isset($data['resposta']) ? trim($data['resposta']) : null;
        $evidencias = isset($data['evidencias']) ? trim($data['evidencias']) : null;

        $sucesso = $this->model->criar($codigo, $descricao, $resposta, $evidencias);
        if ($sucesso) {
            http_response_code(201);
            echo json_encode(['message' => 'Indicador criado com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar indicador']);
        }
    }

    public function atualizar($id, $data) {
        $indicadorExistente = $this->model->buscarPorId($id);
        if (!$indicadorExistente) {
            http_response_code(404);
            echo json_encode(['error' => 'Indicador não encontrado']);
            return;
        }

        $codigo = isset($data['codigo']) ? trim($data['codigo']) : $indicadorExistente['codigo'];
        $descricao = isset($data['descricao']) ? trim($data['descricao']) : $indicadorExistente['descricao'];
        $resposta = isset($data['resposta']) ? trim($data['resposta']) : $indicadorExistente['resposta'];
        $evidencias = isset($data['evidencias']) ? trim($data['evidencias']) : $indicadorExistente['evidencias'];

        $sucesso = $this->model->atualizar($id, $codigo, $descricao, $resposta, $evidencias);
        if ($sucesso) {
            echo json_encode(['message' => 'Indicador atualizado com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar indicador']);
        }
    }

    public function excluir($id) {
        $indicadorExistente = $this->model->buscarPorId($id);
        if (!$indicadorExistente) {
            http_response_code(404);
            echo json_encode(['error' => 'Indicador não encontrado']);
            return;
        }

        $sucesso = $this->model->excluir($id);
        if ($sucesso) {
            echo json_encode(['message' => 'Indicador excluído com sucesso']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir indicador']);
        }
    }
}
