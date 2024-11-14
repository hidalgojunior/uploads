<?php
define('BASE_PATH', __DIR__);

echo "Verificando estrutura de arquivos...<br>";

$required_files = [
    '/config/database.php',
    '/config/api_config.php',
    '/components/header.php',
    '/components/footer.php'
];

$all_ok = true;

foreach ($required_files as $file) {
    $full_path = BASE_PATH . $file;
    if (file_exists($full_path)) {
        echo "✅ Arquivo encontrado: {$file}<br>";
    } else {
        echo "❌ Arquivo não encontrado: {$file}<br>";
        $all_ok = false;
    }
}

if ($all_ok) {
    echo "<br>✨ Todos os arquivos necessários estão presentes!";
} else {
    echo "<br>⚠️ Alguns arquivos estão faltando. Por favor, crie os arquivos necessários.";
}

// Verificar configurações
echo "<br><br>Verificando configurações...<br>";

require_once BASE_PATH . '/config/api_config.php';

if (defined('OPENAI_API_KEY') && !empty(OPENAI_API_KEY)) {
    echo "✅ OpenAI API Key configurada<br>";
} else {
    echo "❌ OpenAI API Key não configurada<br>";
}

if (defined('INSTAGRAM_ACCESS_TOKEN') && !empty(INSTAGRAM_ACCESS_TOKEN)) {
    echo "✅ Instagram Access Token configurado<br>";
} else {
    echo "❌ Instagram Access Token não configurado<br>";
}
?> 