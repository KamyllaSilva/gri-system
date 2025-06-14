<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['empresa_id'])) {
    echo json_encode(['error' => 'Acesso não autorizado']);
    exit;
}

$empresa_id = $_SESSION['empresa_id'];

try {
    
    $total = $db->query("SELECT COUNT(*) FROM indicadores")->fetchColumn();
    
    $preenchidos = $db->query("SELECT COUNT(DISTINCT ri.indicador_id) 
                             FROM respostas_indicadores ri 
                             WHERE ri.empresa_id = $empresa_id")->fetchColumn();
    
    $pendentes = $total - $preenchidos;


    $query = "SELECT 
                i.id, i.codigo, i.descricao, i.categoria,
                ri.resposta as valor, ri.status
              FROM indicadores i
              LEFT JOIN respostas_indicadores ri ON i.id = ri.indicador_id AND ri.empresa_id = $empresa_id
              ORDER BY i.categoria, i.codigo";
    
    $stmt = $db->query($query);
    $indicadores = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $indicadoresPorCategoria = [];
    foreach ($indicadores as $ind) {
        $categoria = $ind['categoria'] ?? 'Outros';
        if (!isset($indicadoresPorCategoria[$categoria])) {
            $indicadoresPorCategoria[$categoria] = [];
        }
        
        $indicadoresPorCategoria[$categoria][] = [
            'id' => $ind['id'],
            'nome' => $ind['codigo'] . ' - ' . $ind['descricao'],
            'valor' => $ind['valor'],
            'status' => $ind['status'] ?? 'pendente'
        ];
    }

    echo json_encode([
        'total' => $total,
        'preenchidos' => $preenchidos,
        'pendentes' => $pendentes,
        'indicadores' => $indicadoresPorCategoria
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao carregar dados: ' . $e->getMessage()]);
}