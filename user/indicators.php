<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'usuario') {
    header("Location: ../index.php");
    exit();
}

$page_title = "Indicadores GRI - Preenchimento";
require_once '../includes/header.php';

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$indicadores = $pdo->query("SELECT * FROM indicadores ORDER BY codigo ASC")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM respostas WHERE usuario_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$respostas_usuario = [];
while ($row = $stmt->fetch()) {
    $respostas_usuario[$row['indicador_id']] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['respostas'] as $indicador_id => $resposta) {
        $resposta = trim($resposta);
        $evidencia_nome = null;

        if (isset($_FILES['evidencias']['name'][$indicador_id]) && $_FILES['evidencias']['error'][$indicador_id] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['evidencias']['tmp_name'][$indicador_id];
            $originalName = basename($_FILES['evidencias']['name'][$indicador_id]);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $novoNome = 'evidencia_' . $_SESSION['user_id'] . '_' . $indicador_id . '_' . time() . '.' . $ext;
                $destino = $uploadDir . $novoNome;
                if (move_uploaded_file($tmpName, $destino)) {
                    $evidencia_nome = $novoNome;
                }
            }
        }

        $check = $pdo->prepare("SELECT id, evidencia FROM respostas WHERE usuario_id = ? AND indicador_id = ?");
        $check->execute([$_SESSION['user_id'], $indicador_id]);

        if ($check->rowCount() > 0) {
            $row = $check->fetch();
            if ($evidencia_nome) {
                $update = $pdo->prepare("UPDATE respostas SET resposta = ?, status = 'preenchido', evidencia = ? WHERE id = ?");
                $update->execute([$resposta, $evidencia_nome, $row['id']]);
            } else {
                $update = $pdo->prepare("UPDATE respostas SET resposta = ?, status = 'preenchido' WHERE id = ?");
                $update->execute([$resposta, $row['id']]);
            }
        } else {
            $insert = $pdo->prepare("INSERT INTO respostas (usuario_id, indicador_id, resposta, status, evidencia) VALUES (?, ?, ?, 'preenchido', ?)");
            $insert->execute([$_SESSION['user_id'], $indicador_id, $resposta, $evidencia_nome]);
        }
    }
    header("Location: indicators.php?salvo=1");
    exit();
}

$totalIndicadores = count($indicadores);
$preenchidos = 0;
foreach ($respostas_usuario as $resposta) {
    if ($resposta['status'] === 'preenchido') {
        $preenchidos++;
    }
}
$percentual = $totalIndicadores > 0 ? round(($preenchidos / $totalIndicadores) * 100) : 0;

if (isset($_GET['salvo'])): ?>
    <div class="alert alert-success" role="alert">
        Respostas e evidências salvas com sucesso!
    </div>
<?php endif; ?>

<section class="progress-container" aria-label="Progresso do preenchimento dos indicadores">
    <div class="progress-bar" style="width: <?= $percentual ?>%;"><?= $percentual ?>%</div>
</section>

<form method="POST" enctype="multipart/form-data" aria-describedby="form-instrucoes">
    <p id="form-instrucoes" style="margin-bottom:30px; color:#555;">
        Preencha os campos abaixo com suas respostas e envie evidências (PDF, JPG, PNG).
    </p>

    <?php foreach ($indicadores as $ind): 
        $resp = $respostas_usuario[$ind['id']] ?? null;
        $statusClass = ($resp && $resp['status'] === 'preenchido') ? 'preenchido' : 'pendente';
    ?>
        <article class="indicator-card" aria-live="polite">
            <header class="indicator-header">
                <span class="indicator-code"><?= htmlspecialchars($ind['codigo']) ?></span>
                <span class="status <?= $statusClass ?>">
                    <?= $statusClass === 'preenchido' ? '✅ Preenchido' : '❌ Pendente' ?>
                </span>
            </header>
            <p class="indicator-description"><?= htmlspecialchars($ind['descricao']) ?></p>

            <textarea name="respostas[<?= $ind['id'] ?>]" placeholder="Sua resposta..."><?= htmlspecialchars($resp['resposta'] ?? '') ?></textarea>

            <div class="file-input-container">
                <label for="evidencia-<?= $ind['id'] ?>">Evidência (PDF, JPG, PNG):</label>
                <input 
                    type="file" 
                    name="evidencias[<?= $ind['id'] ?>]" 
                    id="evidencia-<?= $ind['id'] ?>"
                    accept=".pdf,.jpg,.jpeg,.png"
                    aria-describedby="evidencia-desc-<?= $ind['id'] ?>"
                >
            </div>

            <?php if (!empty($resp['evidencia']) && file_exists($uploadDir . $resp['evidencia'])): ?>
                <p style="margin-top:10px;">
                    <a class="evidence-link" href="../uploads/<?= htmlspecialchars($resp['evidencia']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Ver evidência enviada para <?= htmlspecialchars($ind['codigo']) ?>">
                        📎 Ver evidência atual
                    </a>
                </p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>

    <button type="submit" aria-label="Salvar respostas e evidências">Salvar Respostas e Evidências</button>
</form>

<?php require_once '../includes/footer.php'; ?>
