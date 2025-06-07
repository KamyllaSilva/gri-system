<?php
require_once __DIR__ . '/../config/database.php';

$empresaId = $_SESSION['empresa_id'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE empresa_id = ?");
$stmt->execute([$empresaId]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Nome</th><th>Email</th><th>Tipo</th><th>Ações</th></tr>";
foreach ($usuarios as $u) {
    echo "<tr>";
    echo "<td>{$u['nome']}</td>";
    echo "<td>{$u['email']}</td>";
    echo "<td>{$u['tipo']}</td>";
    echo "<td>
        <a href='../../backend/routes/trocar_tipo.php?id={$u['id']}'>Alterar tipo</a> |
        <a href='../../backend/routes/excluir_usuario.php?id={$u['id']}' onclick=\"return confirm('Excluir este usuário?')\">Excluir</a>
    </td>";
    echo "</tr>";
}
echo "</table>";
