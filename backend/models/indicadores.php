<?php
class Indicador {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarTodos() {
        $stmt = $this->pdo->query("SELECT * FROM indicadores ORDER BY data_registro DESC");
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM indicadores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function criar($codigo, $descricao, $resposta, $evidencias) {
        $stmt = $this->pdo->prepare("INSERT INTO indicadores (codigo, descricao, resposta, evidencias) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$codigo, $descricao, $resposta, $evidencias]);
    }

    public function atualizar($id, $codigo, $descricao, $resposta, $evidencias) {
        $stmt = $this->pdo->prepare("UPDATE indicadores SET codigo = ?, descricao = ?, resposta = ?, evidencias = ? WHERE id = ?");
        return $stmt->execute([$codigo, $descricao, $resposta, $evidencias, $id]);
    }

    public function excluir($id) {
        $stmt = $this->pdo->prepare("DELETE FROM indicadores WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
