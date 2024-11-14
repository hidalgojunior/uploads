<?php
require_once 'config/database.php';

header('Content-Type: application/json');

$arquivo_id = filter_input(INPUT_GET, 'arquivo_id', FILTER_VALIDATE_INT);

if (!$arquivo_id) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            id,
            media_estrelas,
            total_likes,
            total_dislikes,
            total_downloads,
            (SELECT COUNT(*) FROM votos WHERE arquivo_id = uploads.id AND tipo_voto = 'estrela') as total_votos
        FROM uploads 
        WHERE id = ?
    ");
    $stmt->execute([$arquivo_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'data' => $resultado
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Arquivo não encontrado']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar estatísticas']);
} 