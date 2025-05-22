<?php
// Configurações de conexão com o banco
$host = '127.0.0.1';      // ou 'localhost'
$dbname = 'gri_sistema';  // nome do banco que você criou
$user = 'root';           // usuário do MySQL
$pass = '';               // senha (caso tenha colocado uma, insira aqui)

// Conexão usando PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    
    // Configura o modo de erro para exceções (boa prática)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Se der erro, para o sistema e exibe a mensagem
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
