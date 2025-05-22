<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$page_title = "Gerenciar Indicadores";
include '../includes/header.php';

try {
    $stmt = $pdo->query("SELECT id, nome, descricao, meta FROM indicadores ORDER BY nome ASC");
    $indicadores = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erro ao buscar indicadores: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<section>
    <h2>Lista de Indicadores</h2>
    <p><a href="edit_indicator.php">Cadastrar Novo Indicador</a></p>

    <?php
    if (!empty($_SESSION['msg_success'])) {
        echo '<p style="color:green;">' . htmlspecialchars($_SESSION['msg_success']) . '</p>';
        unset($_SESSION['msg_success']);
    }
    if (!empty($_SESSION['msg_error'])) {
        echo '<p style="color:red;">' . htmlspecialchars($_SESSION['msg_error']) . '</p>';
        unset($_SESSION['msg_error']);
    }
    ?>

    <?php if (!empty($indicadores)): ?>
    <table border="1" cellpadding="8" style="width:100%; border-collapse: collapse;">
        <thead style="background-color:#004080; color:#fff;">
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Meta</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indicadores as $ind): ?>
            <tr>
                <td><?php echo htmlspecialchars($ind['nome']); ?></td>
                <td><?php echo htmlspecialchars($ind['descricao']); ?></td>
                <td><?php echo htmlspecialchars($ind['meta']); ?></td>
                <td>
                    <a href="edit_indicator.php?id=<?php echo $ind['id']; ?>">Editar</a> | 
                    <a href="delete_indicator.php?id=<?php echo $ind['id']; ?>" onclick="return confirm('Confirma exclusão deste indicador?');">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Nenhum indicador encontrado.</p>
    <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
