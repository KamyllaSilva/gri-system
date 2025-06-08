<?php
session_start();
require_once 'includes/db.php';

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$empresa_id = $_SESSION['empresa_id'];
$erro = null;
$sucesso = null;

// EXCLUIR USUÁRIO via ?excluir=id
if (isset($_GET['excluir'])) {
    $excluir_id = (int) $_GET['excluir'];

    // Só excluir se o usuário for da mesma empresa
    $stmt = $pdo->prepare("SELECT empresa_id FROM usuarios WHERE id = ?");
    $stmt->execute([$excluir_id]);
    $userEmpresa = $stmt->fetchColumn();

    if ($userEmpresa == $empresa_id && $excluir_id != $_SESSION['usuario_id']) {
        $del = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $del->execute([$excluir_id]);
        $sucesso = "Usuário excluído com sucesso.";
    } else {
        $erro = "Não foi possível excluir esse usuário.";
    }
}

// ADICIONAR NOVO USUÁRIO via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';

    if (!$nome || !$email || !$senha || !$tipo) {
        $erro = "Preencha todos os campos do formulário.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } else if (!in_array($tipo, ['admin', 'user'])) {
        $erro = "Tipo de usuário inválido.";
    } else {
        // Verifica se email já existe na empresa
        $check = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND empresa_id = ?");
        $check->execute([$email, $empresa_id]);
        if ($check->fetchColumn() > 0) {
            $erro = "Já existe um usuário com esse e-mail na sua empresa.";
        } else {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$nome, $email, $hashSenha, $tipo, $empresa_id]);
            $sucesso = "Usuário cadastrado com sucesso.";
        }
    }
}

// Buscar usuários da mesma empresa
$stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE empresa_id = ?");
$stmt->execute([$empresa_id]);
$usuarios = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <h1>Gerenciar Usuários</h1>
    <p class="welcome">Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! Veja os usuários da sua empresa.</p>

    <?php if ($erro): ?>
        <div class="msg error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <div class="msg success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

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
                        <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
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

    <form method="post" action="">
        <h2>Adicionar Novo Usuário</h2>
        <input type="hidden" name="acao" value="adicionar">

        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <label for="tipo">Tipo</label>
        <select id="tipo" name="tipo" required>
            <option value="" disabled selected>Selecione o tipo</option>
            <option value="admin">Administrador</option>
            <option value="user">Usuário</option>
        </select>

        <button type="submit">Cadastrar Usuário</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
