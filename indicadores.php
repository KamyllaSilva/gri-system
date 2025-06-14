<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}


$host = getenv("DB_HOST") ?: 'mysql.railway.internal';
$dbname = getenv("DB_NAME") ?: 'railway';
$user = getenv("DB_USER") ?: 'root';
$pass = getenv("DB_PASS") ?: 'uiieAgKnVVmRzCiByaTGwZZPuPurwuQX';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
 
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
   <link rel="stylesheet" href="assets/css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --danger-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
    
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .categoria-section {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .categoria-title {
            font-size: 1.5rem;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .indicadores-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .indicador-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-color);
            display: flex;
            flex-direction: column;
        }
        
        .indicador-card:hover {
            transform: translateY(-5px);
        }
        
        .indicador-card.respondido {
            border-left-color: var(--secondary-color);
            background-color: var(--light-gray);
        }
        
        .indicador-card.obrigatorio {
            border-left-color: var(--danger-color);
        }
        
        .indicador-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .indicador-codigo {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .indicador-status {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 1rem;
            background: var(--danger-color);
            color: white;
            font-weight: 600;
        }
        
        .indicador-status.respondido {
            background: var(--secondary-color);
        }
        
        .indicador-obrigatorio {
            color: var(--danger-color);
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .indicador-descricao {
            color: #555;
            margin: 0.5rem 0;
            flex-grow: 1;
        }
        
        .resposta-container {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed #ddd;
        }
        
        .resposta-texto {
            font-style: italic;
            margin-bottom: 0.5rem;
        }
        
        .evidencia-link a {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .evidencia-link a:hover {
            text-decoration: underline;
        }
        
        .evidencia-link i {
            margin-right: 0.3rem;
        }
        
        .form-resposta {
            margin-top: 1rem;
            display: none;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            font-family: inherit;
        }
        
        input[type="file"] {
            margin-bottom: 1rem;
            width: 100%;
        }
        
        .btn {
            padding: 0.6rem 1rem;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            font-family: inherit;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-success {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .btn-block {
            display: block;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .data-resposta {
            font-size: 0.8rem;
            color: #777;
        }
    </style>
</head>
<body>
<header>
    <div style="display: flex; align-items: center;">
        <img src="assets/css/img/logo.png" alt="Logo" class="logo-small" />
        <h1 style="margin-left: 1rem;">Sistema GRI</h1>
    </div>
    <nav>
        <a href="dashboard.php">Painel</a>
        <a href="indicadores.php" class="active">Indicadores</a>
        <a href="usuarios.php">Usuários</a>
        <a href="empresas.php">Empresas</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<main class="container">
    <h2 style="color: var(--dark-gray); margin-bottom: 0.5rem;">Indicadores GRI</h2>
    <p style="color: #666; margin-bottom: 2rem;">Gerencie os indicadores de sustentabilidade da sua empresa</p>
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success">
            <?= $_GET['sucesso'] == 1 ? 'Resposta salva com sucesso!' : 'Resposta atualizada com sucesso!' ?>
        </div>
    <?php endif; ?>
    
    <?php foreach ($indicadoresPorCategoria as $categoria => $indicadores): ?>
    <section class="categoria-section">
        <h3 class="categoria-title"><?= htmlspecialchars($categoria) ?></h3>
        <div class="indicadores-grid">
            <?php foreach ($indicadores as $ind): ?>
            <div class="indicador-card <?= $ind['resposta_id'] ? 'respondido' : '' ?> <?= $ind['obrigatorio'] ? 'obrigatorio' : '' ?>">
                <div class="indicador-header">
                    <span class="indicador-codigo"><?= htmlspecialchars($ind['codigo']) ?></span>
                    <span class="indicador-status <?= $ind['resposta_id'] ? 'respondido' : '' ?>">
                        <?= $ind['resposta_id'] ? 'Respondido' : ($ind['obrigatorio'] ? 'Obrigatório' : 'Opcional') ?>
                    </span>
                </div>
                
                <?php if ($ind['obrigatorio']): ?>
                    <span class="indicador-obrigatorio">Indicador Obrigatório</span>
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
                    <span class="data-resposta">Respondido em: <?= date('d/m/Y H:i', strtotime($ind['criado_em'])) ?></span>
                </div>
                <?php endif; ?>
                
                <button class="btn btn-primary btn-block" 
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
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Salvar Resposta
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
        
        
        if (form.style.display === 'block') {
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
</script>
</body>
</html>