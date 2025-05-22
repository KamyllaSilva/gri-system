<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$page_title = "Cadastrar / Editar Indicador";
include '../includes/header.php';

$id = $_GET['id'] ?? null;
$nome = $descricao = $meta = "";
$errors = [];

// Se edição, carrega dados
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM indicadores WHERE id = ?");
    $stmt->execute([$id]);
    $indicador = $stmt->fetch();

    if (!$indicador) {
        echo "<p style='color:red;'>Indicador não encontrado.</p>";
        include '../includes/footer.php';
        exit();
    }

    $nome = $indicador['nome'];
    $descricao = $indicador['descricao'];
    $meta = $indicador['meta'];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $meta = trim($_POST['meta']);

    if (empty($nome)) $errors[] = "O nome do indicador é obrigatório.";
    if (empty($descricao)) $errors[] = "A descrição é obrigatória.";
    if (empty($meta)) $errors[] = "A meta é obrigatória.";

    if (empty($errors)) {
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE indicadores SET nome = ?, descricao = ?, meta = ? WHERE id = ?");
                $stmt->execute([$nome, $descricao, $meta, $id]);
                echo "<p style='color:green;'>Indicador atualizado com sucesso.</p>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO indicadores (nome, descricao, meta) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $descricao, $meta]);
                echo "<p style='color:green;'>Indicador cadastrado com sucesso.</p>";
                // limpa campos
                $nome = $descricao = $meta = "";
            }
        } catch (PDOException $e) {
            $errors[] = "Erro no banco de dados: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<section>
    <h2><?php echo $id ? "Editar Indicador" : "Cadastrar Novo Indicador"; ?></h2>

    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="nome">Nome do Indicador:</label><br>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required><br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($descricao); ?></textarea><br><br>

        <label for="meta">Meta:</label><br>
        <input type="text" id="meta" name="meta" value="<?php echo htmlspecialchars($meta); ?>" required><br><br>

        <button type="submit"><?php echo $id ? "Atualizar" : "Cadastrar"; ?></button>
        <a href="manage_indicators.php" style="margin-left: 10px;">Voltar</a>
    </form>
</section>

<?php include '../includes/footer.php'; ?>
