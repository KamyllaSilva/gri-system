<?php
require_once 'includes/db.php';
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa_nome = $_POST['empresa_nome'];
    $empresa_cnpj = $_POST['empresa_cnpj'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $stmt1 = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
        $stmt1->execute([$empresa_nome, $empresa_cnpj]);
        $empresa_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, 'admin', ?)");
        $stmt2->execute([$nome, $email, $senha, $empresa_id]);

        $mensagem = "Cadastro realizado com sucesso!";
    } catch (PDOException $e) {
        $mensagem = "Erro: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Inicial - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-purple-100 to-white">
    <div class="glassmorphic max-w-2xl w-full p-8 shadow-xl">
        <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Cadastro Inicial</h2>

        <?php if ($mensagem): ?>
            <p class="text-center mb-4 text-sm font-medium text-green-600"><?= $mensagem ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <fieldset class="space-y-4">
                <legend class="text-xl font-semibold text-gray-700">Dados da Empresa</legend>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome da Empresa:</label>
                    <input type="text" name="empresa_nome" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">CNPJ:</label>
                    <input type="text" name="empresa_cnpj" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="text-xl font-semibold text-gray-700 mt-4">Administrador</legend>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome:</label>
                    <input type="text" name="nome" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email:</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha:</label>
                    <input type="password" name="senha" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </fieldset>

            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition duration-200">
                Cadastrar
            </button>
        </form>
    </div>
</body>
</html>
