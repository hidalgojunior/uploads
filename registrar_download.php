<?php
require_once 'config/database.php';

$arquivo_id = $_POST['arquivo_id'] ?? 0;
$ip = $_SERVER['REMOTE_ADDR'];

try {
    // Registra o download
    $stmt = $pdo->prepare("INSERT INTO downloads (arquivo_id, usuario_ip) VALUES (?, ?)");
    $stmt->execute([$arquivo_id, $ip]);

    // Atualiza contador no arquivo
    $stmt = $pdo->prepare("UPDATE uploads SET total_downloads = total_downloads + 1 WHERE id = ?");
    $stmt->execute([$arquivo_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 