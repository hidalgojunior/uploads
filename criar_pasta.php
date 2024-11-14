<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nome_pasta'])) {
    $nome_pasta = trim($_POST['nome_pasta']);
    
    $stmt = $pdo->prepare("INSERT INTO pastas (nome) VALUES (?)");
    $stmt->execute([$nome_pasta]);
}

header('Location: index.php'); 