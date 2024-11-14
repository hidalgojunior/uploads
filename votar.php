<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit;
}

$arquivo_id = filter_input(INPUT_POST, 'arquivo_id', FILTER_VALIDATE_INT);
$tipo_voto = filter_input(INPUT_POST, 'tipo_voto', FILTER_SANITIZE_STRING);
$valor = filter_input(INPUT_POST, 'valor', FILTER_VALIDATE_INT);
$ip = $_SERVER['REMOTE_ADDR'];

if (!$arquivo_id || !$tipo_voto || !isset($valor)) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Verifica se já votou
    $stmt = $pdo->prepare("SELECT id, valor FROM votos WHERE arquivo_id = ? AND usuario_ip = ? AND tipo_voto = ?");
    $stmt->execute([$arquivo_id, $ip, $tipo_voto]);
    $voto_existente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($voto_existente) {
        // Atualiza voto existente
        $stmt = $pdo->prepare("UPDATE votos SET valor = ? WHERE id = ?");
        $stmt->execute([$valor, $voto_existente['id']]);
    } else {
        // Insere novo voto
        $stmt = $pdo->prepare("INSERT INTO votos (arquivo_id, usuario_ip, tipo_voto, valor) VALUES (?, ?, ?, ?)");
        $stmt->execute([$arquivo_id, $ip, $tipo_voto, $valor]);
    }

    // Atualiza estatísticas no arquivo
    if ($tipo_voto === 'estrela') {
        $stmt = $pdo->prepare("
            UPDATE uploads SET 
            media_estrelas = (SELECT AVG(valor) FROM votos WHERE arquivo_id = ? AND tipo_voto = 'estrela')
            WHERE id = ?
        ");
        $stmt->execute([$arquivo_id, $arquivo_id]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE uploads SET 
            total_likes = (SELECT COUNT(*) FROM votos WHERE arquivo_id = ? AND tipo_voto = 'like' AND valor = 1),
            total_dislikes = (SELECT COUNT(*) FROM votos WHERE arquivo_id = ? AND tipo_voto = 'like' AND valor = -1)
            WHERE id = ?
        ");
        $stmt->execute([$arquivo_id, $arquivo_id, $arquivo_id]);
    }

    // Busca valores atualizados
    $stmt = $pdo->prepare("SELECT media_estrelas, total_likes, total_dislikes FROM uploads WHERE id = ?");
    $stmt->execute([$arquivo_id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $resultado
    ]);

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 