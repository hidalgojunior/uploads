<?php
require_once 'config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Buscar informações do arquivo
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
    $stmt->execute([$id]);
    $arquivo = $stmt->fetch();

    if ($arquivo) {
        // Excluir arquivo físico
        if (file_exists($arquivo['caminho'])) {
            unlink($arquivo['caminho']);
        }

        // Excluir registro do banco
        $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
        $stmt->execute([$id]);
    }
}

header('Location: index.php');
?> 