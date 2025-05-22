<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: user/indicators.php");
    exit();
}
$erro = isset($_GET['erro']) ? "Email ou senha inválidos." : "";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-purple-100 to-white">
    <div class="glassmorphic max-w-md w-full p-8 shadow-xl">
        <h2 class="text-3xl font-bold text-center text-purple-700 mb-6">Login - Sistema GRI</h2>

        <?php if ($erro): ?>
            <p class="text-red-500 text-sm text-center mb-4"><?php echo $erro; ?></p>
        <?php endif; ?>

        <form action="includes/auth.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" name="senha" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition duration-200">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>
