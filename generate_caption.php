<?php
require_once 'config/api_config.php';

header('Content-Type: application/json');

// Função para gerar legenda usando ChatGPT
function generateCreativeCaption($description) {
    try {
        $ch = curl_init();
        
        // Configuração do cURL para a API do OpenAI
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false, // Em produção, mantenha como true
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . OPENAI_API_KEY
            ]
        ]);

        // Preparar o prompt para o ChatGPT
        $prompt = "Crie uma legenda criativa e envolvente para o Instagram para uma foto com as seguintes características: " . 
                  $description . 
                  ". A legenda deve ser atraente, incluir emojis relevantes e algumas hashtags populares.";

        // Dados para a API
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Você é um especialista em marketing digital e criação de conteúdo para Instagram.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 150,
            'temperature' => 0.7
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        // Executar a requisição
        $response = curl_exec($ch);
        
        // Verificar erros do cURL
        if (curl_errno($ch)) {
            throw new Exception('Erro cURL: ' . curl_error($ch));
        }
        
        curl_close($ch);

        // Decodificar resposta
        $result = json_decode($response, true);
        
        // Verificar se há erro na resposta
        if (isset($result['error'])) {
            throw new Exception('Erro API OpenAI: ' . $result['error']['message']);
        }

        // Retornar a legenda gerada
        return $result['choices'][0]['message']['content'] ?? '';

    } catch (Exception $e) {
        // Log do erro (em produção, use um sistema proper de logging)
        error_log('Erro ao gerar legenda: ' . $e->getMessage());
        return false;
    }
}

// Receber dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$description = $data['description'] ?? '';

// Validar entrada
if (empty($description)) {
    echo json_encode([
        'success' => false,
        'error' => 'Descrição não fornecida'
    ]);
    exit;
}

// Verificar se a chave API está configurada
if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
    echo json_encode([
        'success' => false,
        'error' => 'Chave API OpenAI não configurada'
    ]);
    exit;
}

// Gerar a legenda
$caption = generateCreativeCaption($description);

if ($caption === false) {
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao gerar legenda'
    ]);
    exit;
}

// Retornar sucesso
echo json_encode([
    'success' => true,
    'caption' => $caption
]); 