<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$empresa_id = $_SESSION['empresa_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_users.php");
    exit();
}

$msg_error = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_error']);

try {
    // Busca usuário e valida empresa
    $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = ? AND empresa_id = ?");
    $stmt->execute([$id, $empresa_id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        $_SESSION['msg_error'] = "Usuário não encontrado ou sem permissão.";
        header("Location: manage_users.php");
        exit();
    }

} catch (PDOException $e) {
    die("Erro ao buscar usuário: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
        form { max-width: 400px; margin: 40px auto; }
        label { display: block; margin-top: 12px; }
        input, select { width: 100%; padding: 8px; margin-top: 4px; }
        .btn { margin-top: 20px; padding: 8px 15px; background-color: #3b82f6; border: none; color: white; cursor: pointer; border-radius: 5px; }
        .msg-error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Editar Usuário</h1>

    <?php if ($msg_error): ?>
        <p class="msg-error"><?= htmlspecialchars($msg_error) ?></p>
    <?php endif; ?>

    <form action="update_user.php" method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>Usuário Comum</option>
            <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>

        <label>Nova Senha (deixe em branco para não alterar):</label>
        <input type="password" name="senha" minlength="6">

        <button type="submit" class="btn">Salvar Alterações</button>
    </form>
</body>
</html>
