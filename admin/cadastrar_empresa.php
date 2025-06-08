<?php
session_start();
require_once 'includes/admin_only.php';
require_once '../includes/db.php'; // ajuste o caminho conforme seu projeto


// 1. Verificar se usuário está logado e é admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login.php"); // ou página que preferir
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $empresa_nome = trim($_POST['empresa_nome']);
    $empresa_cnpj = trim($_POST['empresa_cnpj']);
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        // 2. Verificar se empresa existe pelo nome ou CNPJ
        $stmt = $pdo->prepare("SELECT id FROM empresas WHERE nome = ? OR cnpj = ?");
        $stmt->execute([$empresa_nome, $empresa_cnpj]);
        $empresa = $stmt->fetch();

        if ($empresa) {
            $empresa_id = $empresa['id'];
        } else {
            // 3. Cadastrar nova empresa
            $stmt = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
            $stmt->execute([$empresa_nome, $empresa_cnpj]);
            $empresa_id = $pdo->lastInsertId();
        }

        // 4. Cadastrar usuário vinculado à empresa
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, 'usuario', ?)");
        $stmt->execute([$nome, $email, $senha, $empresa_id]);

        $mensagem = "Usuário cadastrado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro de Usuário (Admin)</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body class="bg-gradient-to-br from-purple-100 to-white min-h-screen p-6 flex items-center justify-center">
    <div class="glassmorphic w-full max-w-xl p-8 rounded shadow-xl">
        <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Cadastrar Novo Usuário</h2>

        <?php if ($mensagem): ?>
            <p class="text-center text-sm mb-4 <?= strpos($mensagem, 'Erro') === 0 ? 'text-red-600' : 'text-green-600' ?>">
                <?= htmlspecialchars($mensagem) ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="space-y-6" autocomplete="off">
            <div>
                <label class="block text-sm font-medium" for="empresa_nome">Nome da Empresa:</label>
                <input type="text" id="empresa_nome" name="empresa_nome" required class="w-full px-4 py-2 border rounded" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="empresa_cnpj">CNPJ da Empresa:</label>
                <input type="text" id="empresa_cnpj" name="empresa_cnpj" required class="w-full px-4 py-2 border rounded" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="nome">Nome do Usuário:</label>
                <input type="text" id="nome" name="nome" required class="w-full px-4 py-2 border rounded" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="email">Email:</label>
                <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required class="w-full px-4 py-2 border rounded" />
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition">
                Cadastrar Usuário
            </button>
        </form>
    </div>
</body>
</html>
