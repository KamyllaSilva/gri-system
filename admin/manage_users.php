<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db.php';

$empresa_id = $_SESSION['empresa_id'];
$msg_success = $_SESSION['msg_success'] ?? '';
$msg_error = $_SESSION['msg_error'] ?? '';
unset($_SESSION['msg_success'], $_SESSION['msg_error']);

// Busca usuários da empresa
try {
    $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE empresa_id = ?");
    $stmt->execute([$empresa_id]);
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Gestão de Usuários</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <style>
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
      th { background-color: #004080; color: white; }
      .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
      .btn-edit { background-color: #3b82f6; color: white; }
      .btn-delete { background-color: #ef4444; color: white; }
      .btn-add { background-color: #22c55e; color: white; margin-bottom: 10px; }
      .msg-success { color: green; }
      .msg-error { color: red; }
      form { margin-top: 20px; }
      label { display: block; margin-top: 10px; }
      input, select { width: 100%; padding: 6px; margin-top: 4px; }
    </style>
</head>
<body>
<h1>Gestão de Usuários - Empresa</h1>

<?php if ($msg_success): ?>
    <p class="msg-success"><?= htmlspecialchars($msg_success) ?></p>
<?php endif; ?>
<?php if ($msg_error): ?>
    <p class="msg-error"><?= htmlspecialchars($msg_error) ?></p>
<?php endif; ?>

<button class="btn btn-add" onclick="document.getElementById('formNovoUsuario').style.display='block'">
    + Novo Usuário
</button>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['tipo']) ?></td>
            <td>
                <a class="btn btn-edit" href="edit_user.php?id=<?= $u['id'] ?>">Editar</a>
                <a class="btn btn-delete" href="delete_user.php?id=<?= $u['id'] ?>" 
                   onclick="return confirm('Confirma a exclusão deste usuário?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Formulário Novo Usuário -->
<div id="formNovoUsuario" style="display:none; margin-top: 20px;">
    <h2>Criar Novo Usuário</h2>
    <form action="create_user.php" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <label>Senha:</label>
        <input type="password" name="senha" required minlength="6">
        
        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="usuario">Usuário Comum</option>
            <option value="admin">Administrador</option>
        </select>
        
        <button type="submit" class="btn btn-add" style="margin-top: 10px;">Salvar</button>
        <button type="button" onclick="document.getElementById('formNovoUsuario').style.display='none'">Cancelar</button>
    </form>
</div>
</body>
</html>
