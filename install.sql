-- Criar banco de dados se não existir
CREATE DATABASE IF NOT EXISTS sistema_uploads;
USE sistema_uploads;

-- Tabela de pastas
CREATE TABLE IF NOT EXISTS pastas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de uploads
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_arquivo VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    tamanho INT NOT NULL,
    pasta_id INT DEFAULT NULL,
    visualizacao ENUM('card', 'lista') DEFAULT 'lista',
    FOREIGN KEY (pasta_id) REFERENCES pastas(id) ON DELETE SET NULL
);

-- Adicione estas tabelas ao seu banco de dados
CREATE TABLE votos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arquivo_id INT NOT NULL,
    usuario_ip VARCHAR(45),
    tipo_voto ENUM('estrela', 'like') NOT NULL,
    valor INT NOT NULL, -- 1-5 para estrelas, 1 ou -1 para like/dislike
    data_voto DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_voto (arquivo_id, usuario_ip, tipo_voto)
);

CREATE TABLE downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arquivo_id INT NOT NULL,
    usuario_ip VARCHAR(45),
    data_download DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Adicione estes campos à tabela uploads
ALTER TABLE uploads 
ADD COLUMN total_downloads INT DEFAULT 0,
ADD COLUMN media_estrelas DECIMAL(2,1) DEFAULT 0,
ADD COLUMN total_likes INT DEFAULT 0,
ADD COLUMN total_dislikes INT DEFAULT 0;