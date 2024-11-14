<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Conectar ao MySQL sem selecionar banco
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ler o arquivo SQL
    $sql = file_get_contents('install.sql');

    // Executar os comandos SQL
    $pdo->exec($sql);

    echo "Banco de dados e tabelas criados com sucesso!";
    
    // Redirecionar após 3 segundos
    header("refresh:3;url=index.php");

} catch(PDOException $e) {
    echo "Erro na instalação: " . $e->getMessage();
}
?> 