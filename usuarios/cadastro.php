<?php
session_start();
require_once '../includes/db.php';

$erro = '';
$sucesso = '';

// Verifica se está logado e se é admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Busca empresas já cadastradas
$empresas = $pdo->query("SELECT id, nome FROM empresas")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipo'];
    $empresa_id = $_POST['empresa_id'];
    $nova_empresa = trim($_POST['nova_empresa']);

    // Verifica se vai cadastrar nova empresa
    if (!empty($nova_empresa)) {
        $stmt = $pdo->prepare("INSERT INTO empresas (nome) VALUES (?)");
        if ($stmt->execute([$nova_empresa])) {
            $empresa_id = $pdo->lastInsertId();
        } else {
            $erro = "Erro ao cadastrar nova empresa.";
        }
    }

    // Verifica se temos empresa selecionada ou criada
    if (empty($empresa_id)) {
        $erro = "Você precisa selecionar ou cadastrar uma empresa.";
    }

    // Verifica se o email já existe
    $verifica = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $verifica->execute([$email]);
    if ($verifica->rowCount() > 0) {
        $erro = "Este e-mail já está cadastrado.";
    }

    // Cadastra o usuário
    if (empty($erro)) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, empresa_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nome, $email, $senha, $tipo, $empresa_id])) {
            $sucesso = "Usuário cadastrado com sucesso.";
        } else {
            $erro = "Erro ao cadastrar usuário.";
        }
    }
}
?>
