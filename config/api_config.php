<?php
// Configurações da API do Instagram
define('INSTAGRAM_ACCESS_TOKEN', 'seu_token_aqui');

// Configuração da API do OpenAI (ChatGPT)
define('OPENAI_API_KEY', 'token-da-api-do-openai-aqui');

// Configurações de Debug (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para log de erros
function logError($message, $context = []) {
    $logFile = __DIR__ . '/../logs/error.log';
    $logDir = dirname($logFile);
    
    // Criar diretório de logs se não existir
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    $logMessage = date('Y-m-d H:i:s') . " - " . $message . " - " . json_encode($context) . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

// Verificar se as constantes necessárias estão definidas
if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY)) {
    logError('OpenAI API Key não configurada');
}

if (!defined('INSTAGRAM_ACCESS_TOKEN') || empty(INSTAGRAM_ACCESS_TOKEN)) {
    logError('Instagram Access Token não configurado');
}
?> 