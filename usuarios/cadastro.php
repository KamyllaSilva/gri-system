<?php
require_once '../includes/db.php';

// Busca empresas já cadastradas
$empresas = $pdo->query("SELECT id, nome FROM empresas")->fetchAll();

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];
    $empresa_id = $_POST['empresa_id'];
    $nova_empresa = trim($_POST['nova_empresa']);

    // Verifica se vai cadastrar nova empresa
    if ($nova_empresa !== '') {
        $stmt = $pdo->prepare("INSERT INTO empresas (nome) VALUES (?)");
        $stmt->execute([$nova_empresa]);
        $empresa_id = $pdo->lastInsertId();
    }

    // Cadastra o usuário
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nome, $email, $senha, $tipo, $empresa_id])) {
        header("Location: login.php");
        exit;
    } else {
        $erro = "Erro ao cadastrar usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        input, select { width: 100%; padding: 8px; margin-top: 10px; }
        button { margin-top: 15px; background: #007BFF; color: #fff; border: none; padding: 10px; cursor: pointer; width: 100%; }
        button:hover { background: #0056b3; }
        .erro { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Cadastro de Usuário</h2>

        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Senha:</label>
        <input type="password" name="senha" required>

        <label>Tipo de usuário:</label>
        <select name="tipo" required>
            <option value="admin">Administrador</option>
            <option value="lider">Líder</option>
            <option value="colaborador">Colaborador</option>
        </select>

        <label>Selecionar empresa existente:</label>
        <select name="empresa_id">
            <option value="">-- Selecione --</option>
            <?php foreach ($empresas as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Ou cadastrar nova empresa:</label>
        <input type="text" name="nova_empresa" placeholder="Nome da nova empresa">

        <button type="submit">Cadastrar</button>

        <?php if ($erro): ?>
            <p class="erro"><?= $erro ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
