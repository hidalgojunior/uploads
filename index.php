<?php 
require_once 'config/database.php';
require_once 'components/header.php';

function getBreadcrumbs($pdo, $pasta_id = null) {
    $caminho = [['id' => null, 'nome' => 'Raiz']];
    if ($pasta_id) {
        $stmt = $pdo->prepare("SELECT id, nome FROM pastas WHERE id = ?");
        $stmt->execute([$pasta_id]);
        $pasta = $stmt->fetch();
        if ($pasta) {
            $caminho[] = $pasta;
        }
    }
    return $caminho;
}

$visualizacao = $_GET['view'] ?? 'galeria';
$pasta_atual = $_GET['pasta'] ?? null;
$breadcrumbs = getBreadcrumbs($pdo, $pasta_atual);
?>

<!-- Todo o conteúdo fica dentro do main que foi aberto no header -->
<div class="container py-4">
    <!-- Área de Upload -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cloud-upload"></i> Upload de Arquivos</h5>
                </div>
                <div class="card-body">
                    <form action="upload.php" method="POST" enctype="multipart/form-data" class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="arquivo" class="form-label">Selecione o arquivo</label>
                                <input type="file" class="form-control" id="arquivo" name="arquivo" 
                                       accept="image/*,video/*,application/pdf" required 
                                       onchange="previewFile()">
                            </div>
                            <div id="preview" class="mb-3 text-center">
                                <!-- Preview será mostrado aqui -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pasta" class="form-label">Pasta</label>
                                <select class="form-select" id="pasta" name="pasta_id">
                                    <option value="">Raiz</option>
                                    <?php
                                    $pastas = $pdo->query("SELECT id, nome FROM pastas ORDER BY nome")->fetchAll();
                                    foreach($pastas as $pasta):
                                    ?>
                                    <option value="<?= $pasta['id'] ?>"><?= htmlspecialchars($pasta['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Enviar Arquivo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Visualização -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group">
                <a href="?view=galeria" class="btn btn-outline-primary <?= $visualizacao == 'galeria' ? 'active' : '' ?>">
                    <i class="bi bi-grid-3x3-gap"></i> Galeria
                </a>
                <a href="?view=lista" class="btn btn-outline-primary <?= $visualizacao == 'lista' ? 'active' : '' ?>">
                    <i class="bi bi-list"></i> Lista
                </a>
            </div>
        </div>
    </div>

    <!-- Galeria de Imagens -->
    <?php if($visualizacao == 'galeria'): ?>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            // Agora vamos buscar todos os tipos de arquivo, não só imagens
            $query = "SELECT * FROM uploads ORDER BY data_upload DESC LIMIT 12";
            $arquivos = $pdo->query($query)->fetchAll();
            foreach($arquivos as $arquivo):
                // Determina o tipo de preview baseado no tipo do arquivo
                $isPdf = $arquivo['tipo'] === 'application/pdf';
                $isImage = strpos($arquivo['tipo'], 'image/') === 0;
            ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-preview-container" 
                         onclick="openFileModal('uploads/<?= $arquivo['nome_arquivo'] ?>', 
                                             '<?= htmlspecialchars($arquivo['nome_arquivo']) ?>', 
                                             '<?= $arquivo['tipo'] ?>')">
                        <?php if($isImage): ?>
                            <img src="uploads/<?= $arquivo['nome_arquivo'] ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($arquivo['nome_arquivo']) ?>">
                        <?php elseif($isPdf): ?>
                            <div class="pdf-preview">
                                <i class="bi bi-file-pdf"></i>
                                <span class="pdf-label">PDF</span>
                            </div>
                        <?php else: ?>
                            <div class="file-preview">
                                <i class="bi bi-file-earmark"></i>
                                <span class="file-label"><?= strtoupper(pathinfo($arquivo['nome_arquivo'], PATHINFO_EXTENSION)) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title text-truncate" title="<?= htmlspecialchars($arquivo['nome_arquivo']) ?>">
                            <?= htmlspecialchars($arquivo['nome_arquivo']) ?>
                        </h6>
                        <p class="card-text">
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($arquivo['data_upload'])) ?><br>
                                <?= number_format($arquivo['tamanho'] / 1048576, 2) ?> MB
                            </small>
                        </p>
                        <div class="rating-section mt-2">
                            <div class="stars mb-2" data-arquivo-id="<?= $arquivo['id'] ?>">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= ($arquivo['media_estrelas'] >= $i) ? '-fill' : '' ?> star-rating"
                                       data-value="<?= $i ?>"
                                       role="button"></i>
                                <?php endfor; ?>
                                <small class="text-muted ms-2 rating-value">(<?= number_format($arquivo['media_estrelas'], 1) ?>)</small>
                            </div>
                            
                            <div class="likes-container">
                                <button class="btn btn-sm btn-outline-success like-btn" 
                                        data-arquivo-id="<?= $arquivo['id'] ?>" 
                                        data-valor="1">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                    <span class="like-count"><?= $arquivo['total_likes'] ?? 0 ?></span>
                                </button>
                                <button class="btn btn-sm btn-outline-danger dislike-btn" 
                                        data-arquivo-id="<?= $arquivo['id'] ?>" 
                                        data-valor="-1">
                                    <i class="bi bi-hand-thumbs-down"></i>
                                    <span class="dislike-count"><?= $arquivo['total_dislikes'] ?? 0 ?></span>
                                </button>
                            </div>

                            <!-- Downloads -->
                            <div class="downloads mt-2">
                                <i class="bi bi-download"></i>
                                <span class="download-count"><?= $arquivo['total_downloads'] ?></span> downloads
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <a href="javascript:void(0)" 
                               onclick="openFileModal('uploads/<?= $arquivo['nome_arquivo'] ?>', 
                                                   '<?= htmlspecialchars($arquivo['nome_arquivo']) ?>', 
                                                   '<?= $arquivo['tipo'] ?>')" 
                               class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="excluir.php?id=<?= $arquivo['id'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Tem certeza que deseja excluir?')">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Lista tradicional -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Tamanho</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM uploads ORDER BY data_upload DESC";
                    $arquivos = $pdo->query($query)->fetchAll();
                    foreach($arquivos as $arquivo):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($arquivo['nome_arquivo']) ?></td>
                        <td><?= $arquivo['tipo'] ?></td>
                        <td><?= number_format($arquivo['tamanho'] / 1048576, 2) ?> MB</td>
                        <td><?= date('d/m/Y H:i', strtotime($arquivo['data_upload'])) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="javascript:void(0)" 
                                   onclick="openFileModal('uploads/<?= $arquivo['nome_arquivo'] ?>', 
                                                         '<?= htmlspecialchars($arquivo['nome_arquivo']) ?>', 
                                                         '<?= $arquivo['tipo'] ?>')" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="excluir.php?id=<?= $arquivo['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function previewFile() {
    const preview = document.getElementById('preview');
    const file = document.querySelector('input[type=file]').files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
        preview.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = reader.result;
            img.className = 'img-fluid';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        } else if (file.type === 'application/pdf') {
            preview.innerHTML = '<i class="bi bi-file-pdf fs-1 text-danger"></i>';
        } else if (file.type.startsWith('video/')) {
            preview.innerHTML = '<i class="bi bi-file-play fs-1 text-primary"></i>';
        }
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}
</script>

<!-- Adicione este modal no final do arquivo, antes do footer -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visualizar Arquivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="fileViewer" class="text-center">
                    <!-- Conteúdo será carregado aqui -->
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="downloadBtn" download>
                    <i class="bi bi-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Atualize o JavaScript -->
<script>
// Função para preview de upload
function previewFile() {
    const preview = document.getElementById('preview');
    const file = document.querySelector('input[type=file]').files[0];
    const reader = new FileReader();

    reader.onloadend = function() {
        preview.innerHTML = '';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = reader.result;
            img.className = 'img-fluid';
            img.style.maxHeight = '200px';
            preview.appendChild(img);
        } else if (file.type === 'application/pdf') {
            preview.innerHTML = '<i class="bi bi-file-pdf fs-1 text-danger"></i>';
        } else if (file.type.startsWith('video/')) {
            preview.innerHTML = '<i class="bi bi-file-play fs-1 text-primary"></i>';
        }
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}

// Função para abrir arquivo no modal
function openFileModal(fileUrl, fileName, fileType) {
    const modal = new bootstrap.Modal(document.getElementById('fileModal'));
    const viewer = document.getElementById('fileViewer');
    const downloadBtn = document.getElementById('downloadBtn');
    
    // Atualizar título do modal
    document.querySelector('#fileModal .modal-title').textContent = fileName;
    
    // Configurar botão de download
    downloadBtn.href = fileUrl;
    downloadBtn.download = fileName;
    
    // Limpar viewer
    viewer.innerHTML = '';
    
    // Verificar tipo de arquivo e criar visualização apropriada
    if (fileType.startsWith('image/')) {
        // Para imagens
        const img = document.createElement('img');
        img.src = fileUrl;
        img.className = 'img-fluid';
        img.style.maxHeight = '80vh';
        viewer.appendChild(img);
    } else if (fileType === 'application/pdf') {
        // Para PDFs
        const embed = document.createElement('embed');
        embed.src = fileUrl;
        embed.type = 'application/pdf';
        embed.style.width = '100%';
        embed.style.height = '80vh';
        viewer.appendChild(embed);
    } else {
        // Para outros tipos de arquivo
        viewer.innerHTML = `
            <div class="p-5">
                <i class="bi bi-file-earmark fs-1"></i>
                <p>Este tipo de arquivo não pode ser visualizado diretamente.</p>
                <p>Por favor, use o botão de download.</p>
            </div>
        `;
    }
    
    modal.show();
}

// Adicionar estilos CSS dinamicamente
const style = document.createElement('style');
style.textContent = `
    #fileViewer {
        background-color: #f8f9fa;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #fileViewer img {
        max-width: 100%;
        height: auto;
        max-height: 80vh;
    }
    .modal-xl {
        max-width: 90vw;
    }
    .card {
        transition: box-shadow 0.3s;
    }
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .card-preview-container {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .card-preview-container:hover {
        background-color: #e9ecef;
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .pdf-preview, .file-preview {
        text-align: center;
        padding: 20px;
    }

    .pdf-preview i, .file-preview i {
        font-size: 4rem;
    }

    .pdf-preview i {
        color: #dc3545;
    }

    .pdf-preview .pdf-label, .file-preview .file-label {
        display: block;
        margin-top: 10px;
        font-size: 1rem;
        font-weight: bold;
        color: #6c757d;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }
`;
document.head.appendChild(style);
</script>

<style>
.rating-section {
    border-top: 1px solid #dee2e6;
    padding-top: 10px;
}

.stars {
    color: #ffc107;
    cursor: pointer;
}

.star-rating {
    transition: transform 0.2s;
    cursor: pointer;
}

.star-rating:hover {
    transform: scale(1.2);
}

.likes-container {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.like-btn, .dislike-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.like-count, .dislike-count {
    min-width: 20px;
    display: inline-block;
    text-align: center;
}

.rating-value {
    font-size: 0.9rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de estrelas
    document.querySelectorAll('.stars').forEach(starsContainer => {
        const arquivo_id = starsContainer.dataset.arquivoId;
        
        starsContainer.querySelectorAll('.star-rating').forEach(star => {
            star.addEventListener('click', async function() {
                const valor = this.dataset.value;
                await enviarVoto(arquivo_id, 'estrela', valor, starsContainer);
            });

            // Efeito hover
            star.addEventListener('mouseenter', function() {
                const valor = this.dataset.value;
                highlightStars(starsContainer, valor);
            });
        });

        // Restaurar estado original ao sair da área de estrelas
        starsContainer.addEventListener('mouseleave', function() {
            const rating = starsContainer.querySelector('.rating-value');
            const currentRating = parseFloat(rating.textContent.replace(/[()]/g, ''));
            highlightStars(starsContainer, currentRating);
        });
    });

    // Sistema de like/dislike
    document.querySelectorAll('.like-btn, .dislike-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const arquivo_id = this.dataset.arquivoId;
            const valor = this.dataset.valor;
            await enviarVoto(arquivo_id, 'like', valor);
        });
    });
});

async function enviarVoto(arquivo_id, tipo_voto, valor, starsContainer = null) {
    try {
        const response = await fetch('votar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `arquivo_id=${arquivo_id}&tipo_voto=${tipo_voto}&valor=${valor}`
        });

        const data = await response.json();
        
        if (data.success) {
            if (tipo_voto === 'estrela' && starsContainer) {
                // Atualiza estrelas
                const ratingValue = starsContainer.querySelector('.rating-value');
                ratingValue.textContent = `(${data.data.media_estrelas})`;
                highlightStars(starsContainer, data.data.media_estrelas);
            } else {
                // Atualiza likes/dislikes
                const container = document.querySelector(`[data-arquivo-id="${arquivo_id}"]`).closest('.likes-container');
                container.querySelector('.like-count').textContent = data.data.total_likes;
                container.querySelector('.dislike-count').textContent = data.data.total_dislikes;
            }
        } else {
            console.error('Erro ao votar:', data.error);
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
    }
}

function highlightStars(container, rating) {
    container.querySelectorAll('.star-rating').forEach((star, index) => {
        star.classList.remove('bi-star-fill', 'bi-star');
        star.classList.add(index < rating ? 'bi-star-fill' : 'bi-star');
    });
}
</script>

<?php require_once 'components/footer.php'; ?> 