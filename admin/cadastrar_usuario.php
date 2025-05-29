<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Buscar empresas para associar
$empresas = $pdo->query("SELECT id, nome FROM empresas")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];
    $empresa_id = $_POST['empresa_id'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha, $tipo, $empresa_id]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Cadastrar Novo Usuário</h2>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>Tipo de Usuário:</label><br>
        <select name="tipo" required>
            <option value="usuario">Usuário</option>
            <option value="admin">Administrador</option>
        </select><br><br>

        <label>Empresa:</label><br>
        <select name="empresa_id" required>
            <?php foreach ($empresas as $empresa): ?>
                <option value="<?= $empresa['id'] ?>"><?= $empresa['nome'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Cadastrar</button>
    </form>
    <p><a href="dashboard.php">← Voltar</a></p>
</body>
</html>
