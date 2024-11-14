<?php
require_once 'components/header.php';
require_once 'config/api_config.php';

function generateCreativeCaption($image_description) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);

    $prompt = "Crie uma legenda criativa para uma foto com as seguintes características: " . $image_description;
    
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 150
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['choices'][0]['message']['content'] ?? '';
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Integração com Instagram</h4>
        </div>
        <div class="card-body">
            <form action="post_to_instagram.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="image" class="form-label">Selecione a Imagem</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Descreva a imagem</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-info" onclick="generateCaption()">
                        Gerar Legenda com IA
                    </button>
                </div>
                <div class="mb-3">
                    <label for="caption" class="form-label">Legenda</label>
                    <textarea class="form-control" id="caption" name="caption" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Postar no Instagram</button>
            </form>
        </div>
    </div>
</div>

<script>
async function generateCaption() {
    const description = document.getElementById('description').value;
    if (!description) {
        alert('Por favor, descreva a imagem primeiro.');
        return;
    }

    try {
        const response = await fetch('generate_caption.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ description })
        });
        
        const data = await response.json();
        document.getElementById('caption').value = data.caption;
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao gerar legenda');
    }
}
</script>

<?php require_once 'components/footer.php'; ?> 