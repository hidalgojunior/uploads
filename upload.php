<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arquivo = $_FILES['arquivo'];
    $pasta_id = $_POST['pasta_id'] ?: null;
    $visualizacao = $_POST['visualizacao'] ?? 'lista';
    $diretorio = 'uploads/';

    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Validar tipo de arquivo
    $tiposPermitidos = [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'video/mp4', 
        'video/mpeg',
        'application/pdf'
    ];
    
    if (!in_array($arquivo['type'], $tiposPermitidos)) {
        die('Tipo de arquivo não permitido');
    }

    $nomeArquivo = uniqid() . '_' . $arquivo['name'];
    $caminhoCompleto = $diretorio . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO uploads (nome_arquivo, tipo, caminho, tamanho, pasta_id, visualizacao) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nomeArquivo,
                $arquivo['type'],
                $caminhoCompleto,
                $arquivo['size'],
                $pasta_id,
                $visualizacao
            ]);

            header('Location: index.php' . ($pasta_id ? '?pasta='.$pasta_id : ''));
        } catch (PDOException $e) {
            echo "Erro ao salvar no banco: " . $e->getMessage();
            // Se der erro, remove o arquivo que foi enviado
            if (file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }
        }
    } else {
        echo "Erro no upload do arquivo.";
    }
}
?> 