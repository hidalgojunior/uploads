<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Uploads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        /* Estilos para footer fixo */
        html {
            position: relative;
            min-height: 100%;
            height: 100%;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        main {
            flex: 1 0 auto;
        }

        footer {
            margin-top: auto;
            width: 100%;
            background-color: #343a40;
            color: white;
        }

        /* Seus outros estilos */
        .header-custom {
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Estilos adicionais para garantir espaçamento correto */
        .content-wrapper {
            flex: 1 0 auto;
            width: 100%;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header-custom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo-text">
                        <i class="bi bi-cloud-upload"></i> Sistema de Uploads
                    </div>
                </div>
                <div class="col-md-6">
                    <nav class="nav justify-content-end">
                        <a class="nav-link text-white" href="index.php">
                            <i class="bi bi-house"></i> Início
                        </a>
                        <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-envelope"></i> Contato
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    
    <div class="content-wrapper">
        <main>
</body>
</html> 