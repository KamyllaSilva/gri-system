<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco
$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Buscar todos os indicadores com informações de resposta
    $query = "SELECT 
                i.id, 
                i.codigo, 
                i.descricao, 
                i.categoria,
                i.obrigatorio,
                r.id AS resposta_id,
                r.resposta,
                r.criado_em,
                e.caminho_arquivo AS evidencia
              FROM indicadores i
              LEFT JOIN respostas_indicadores r ON i.id = r.indicador_id 
                AND r.empresa_id = :empresa_id
              LEFT JOIN evidencias e ON e.resposta_id = r.id
              ORDER BY i.categoria, i.codigo";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['empresa_id' => $_SESSION['empresa_id']]);
    $indicadores = $stmt->fetchAll();
    
    // Agrupar por categoria
    $indicadoresPorCategoria = [];
    foreach ($indicadores as $ind) {
        $categoria = $ind['categoria'] ?? 'Outros';
        $indicadoresPorCategoria[$categoria][] = $ind;
    }
    
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Indicadores - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* (Mantido o mesmo estilo CSS do anterior) */
        .indicador-card.obrigatorio {
            border-left-color: #e74c3c;
        }
        .indicador-obrigatorio {
            color: #e74c3c;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
<!-- (Mantido o mesmo header do anterior) -->

<main class="container">
    <h2>Indicadores GRI</h2>
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?= $_GET['sucesso'] == 1 ? 'Resposta salva com sucesso!' : 'Resposta atualizada com sucesso!' ?>
        </div>
    <?php endif; ?>
    
    <?php foreach ($indicadoresPorCategoria as $categoria => $indicadores): ?>
    <section class="categoria-section">
        <h3 class="categoria-title"><?= htmlspecialchars($categoria) ?></h3>
        <div class="indicadores-container">
            <?php foreach ($indicadores as $ind): ?>
            <div class="indicador-card <?= $ind['resposta_id'] ? 'respondido' : '' ?> <?= $ind['obrigatorio'] ? 'obrigatorio' : '' ?>">
                <div class="indicador-header">
                    <span class="indicador-codigo"><?= htmlspecialchars($ind['codigo']) ?></span>
                    <span class="indicador-status <?= $ind['resposta_id'] ? 'respondido' : '' ?>">
                        <?= $ind['resposta_id'] ? 'Respondido' : ($ind['obrigatorio'] ? 'Obrigatório' : 'Opcional') ?>
                    </span>
                </div>
                
                <?php if ($ind['obrigatorio']): ?>
                    <span class="indicador-obrigatorio">(Indicador Obrigatório)</span>
                <?php endif; ?>
                
                <div class="indicador-descricao">
                    <?= htmlspecialchars($ind['descricao']) ?>
                </div>
                
                <?php if ($ind['resposta_id']): ?>
                <div class="resposta-container">
                    <div class="resposta-texto">
                        <strong>Resposta:</strong><br>
                        <?= nl2br(htmlspecialchars($ind['resposta'])) ?>
                    </div>
                    <?php if ($ind['evidencia']): ?>
                        <div class="evidencia-link">
                            <a href="<?= $ind['evidencia'] ?>" target="_blank">
                                <i class="fas fa-paperclip"></i> Ver evidência
                            </a>
                        </div>
                    <?php endif; ?>
                    <small>Respondido em: <?= date('d/m/Y H:i', strtotime($ind['criado_em'])) ?></small>
                </div>
                <?php endif; ?>
                
                <button class="btn btn-primary btn-responder" 
                        onclick="toggleForm('form-<?= $ind['id'] ?>')">
                    <?= $ind['resposta_id'] ? 'Editar Resposta' : 'Responder' ?>
                </button>
                
                <div id="form-<?= $ind['id'] ?>" class="form-resposta">
                    <form method="POST" action="salvar_indicador.php" enctype="multipart/form-data">
                        <input type="hidden" name="indicador_id" value="<?= $ind['id'] ?>">
                        <?php if ($ind['resposta_id']): ?>
                        <input type="hidden" name="resposta_id" value="<?= $ind['resposta_id'] ?>">
                        <?php endif; ?>
                        
                        <label for="resposta-<?= $ind['id'] ?>">Resposta</label>
                        <textarea name="resposta" id="resposta-<?= $ind['id'] ?>" rows="4" required><?= 
                            $ind['resposta_id'] ? htmlspecialchars($ind['resposta']) : '' 
                        ?></textarea>
                        
                        <label for="arquivo-<?= $ind['id'] ?>">Anexar Evidência</label>
                        <input type="file" name="arquivo" id="arquivo-<?= $ind['id'] ?>">
                        
                        <button type="submit" class="btn btn-success" style="margin-top: 10px;">
                            Salvar Resposta
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>
</main>

<script>
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        form.style.display = form.style.display === 'block' ? 'none' : 'block';
    }
</script>
</body>
</html>