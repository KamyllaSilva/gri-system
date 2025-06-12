<?php
session_start();
require_once 'includes/db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$empresa_logada_id = $_SESSION['empresa_id']; // empresa da sessão
$erro = null;
$sucesso = null;

// --- CADASTRO DE EMPRESA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar_empresa') {
    $nome_empresa = trim($_POST['nome_empresa'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');

    if (!$nome_empresa || !$cnpj) {
        $erro = "Preencha todos os campos da empresa.";
    } else {
        // Verifica se empresa já existe pelo CNPJ
        $check = $pdo->prepare("SELECT COUNT(*) FROM empresas WHERE cnpj = ?");
        $check->execute([$cnpj]);
        if ($check->fetchColumn() > 0) {
            $erro = "Essa empresa já está cadastrada.";
        } else {
            $insert = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
            $insert->execute([$nome_empresa, $cnpj]);
            $sucesso = "Empresa cadastrada com sucesso.";
        }
    }
}

// --- CADASTRO DE USUÁRIO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $empresa_id = (int) ($_POST['empresa_id'] ?? 0);

    if (!$nome || !$email || !$senha || !$tipo || !$empresa_id) {
        $erro = "Preencha todos os campos do formulário.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } else if (!in_array($tipo, ['admin', 'usuario'])) {
        $erro = "Tipo de usuário inválido.";
    } else {
        // Verifica se email já existe na empresa selecionada
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND empresa_id = ?");
        $check->execute([$email, $empresa_id]);
        if ($check->fetchColumn() > 0) {
            $erro = "Já existe um usuário com esse e-mail nessa empresa.";
        } else {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$nome, $email, $hashSenha, $tipo, $empresa_id]);
            $sucesso = "Usuário cadastrado com sucesso.";
        }
    }
}

// EXCLUIR USUÁRIO via ?excluir=id
if (isset($_GET['excluir'])) {
    $excluir_id = (int) $_GET['excluir'];

    // Só excluir se o usuário for da mesma empresa da sessão
    $stmt = $pdo->prepare("SELECT empresa_id FROM usuarios WHERE id = ?");
    $stmt->execute([$excluir_id]);
    $userEmpresa = $stmt->fetchColumn();

    if ($userEmpresa == $empresa_logada_id && $excluir_id != $usuario_id) {
        $del = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $del->execute([$excluir_id]);
        $sucesso = "Usuário excluído com sucesso.";
    } else {
        $erro = "Não foi possível excluir esse usuário.";
    }
}

// Buscar empresas para o select do usuário
$stmtEmpresas = $pdo->query("SELECT id, nome FROM empresas ORDER BY nome");
$empresas = $stmtEmpresas->fetchAll();

// Buscar usuários da empresa logada para listar na tabela
$stmtUsuarios = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE empresa_id = ?");
$stmtUsuarios->execute([$empresa_logada_id]);
$usuarios = $stmtUsuarios->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Gerenciar Usuários e Empresas - Sistema GRI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        /* Seu CSS já enviado, mantenha igual */
        /* ... mantenha todo o seu CSS aqui ... */
        :root {
          --primary: #1e3a8a;
          --primary-light: #2563eb;
          --background: #f9fbff;
          --foreground: #1a1a1a;
          --muted: #6c757d;
          --card: #ffffff;
          --shadow: 0 4px 20px rgba(30, 64, 175, 0.1);
          --radius: 12px;
          --transition: all 0.3s ease;
        }

        /* (TODO: copie seu CSS do exemplo para cá) */
    </style>
</head>
<body>

<header>
    <h1>Sistema GRI</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="usuarios.php" class="active">Usuários</a>
        <a href="logout.php" class="button">Sair</a>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">Gerenciar Usuários e Empresas</h1>
    <p class="welcome">Olá, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?>! Veja os usuários da sua empresa.</p>

    <?php if ($erro): ?>
        <div class="msg error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="msg success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <!-- Tabela de usuários -->
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($usuarios): ?>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['tipo']) ?></td>
                    <td class="actions">
                        <?php if ($u['id'] != $usuario_id): ?>
                            <a href="?excluir=<?= $u['id'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars($u['nome']) ?>?')">Excluir</a>
                        <?php else: ?>
                            <em>Você</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center; padding: 15px;">Nenhum usuário cadastrado nesta empresa.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Formulário para cadastrar empresa -->
    <form method="post" action="">
        <h2>Cadastrar Nova Empresa</h2>
        <input type="hidden" name="acao" value="cadastrar_empresa" />

        <label for="nome_empresa">Nome da Empresa</label>
        <input type="text" id="nome_empresa" name="nome_empresa" required />

        <label for="cnpj">CNPJ</label>
        <input type="text" id="cnpj" name="cnpj" required />

        <button type="submit">Cadastrar Empresa</button>
    </form>

    <hr style="margin: 40px 0;">

    <!-- Formulário para cadastrar usuário -->
    <form method="post" action="">
        <h2>Adicionar Novo Usuário</h2>
        <input type="hidden" name="acao" value="adicionar" />

        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required />

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required />

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required />

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="" disabled selected>Selecione o tipo</option>
            <option value="admin">Administrador</option>
            <option value="usuario">Usuário</option>
        </select>

        <label for="empresa_id">Empresa</label>
        <select id="empresa_id" name="empresa_id" required>
            <option value="" disabled selected>Selecione a empresa</option>
            <?php foreach ($empresas as $empresa): ?>
                <option value="<?= htmlspecialchars($empresa['id']) ?>"><?= htmlspecialchars($empresa['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Cadastrar Usuário</button>
    </form>

</div>

</body>
</html>
