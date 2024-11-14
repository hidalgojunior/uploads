<?php
// Verificar se as configurações básicas estão presentes
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
?>

<footer class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Sobre o Sistema</h5>
                <p>Sistema de gerenciamento de arquivos e uploads com organização em pastas.</p>
            </div>
            <div class="col-md-4">
                <h5>Links Rápidos</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light">Política de Privacidade</a></li>
                    <li><a href="#" class="text-light">Termos de Uso</a></li>
                    <li><a href="#" class="text-light">FAQ</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contato</h5>
                <ul class="list-unstyled">
                    <li>
                        <i class="bi bi-envelope"></i> 
                        <a href="mailto:arnaldo.hidalgo@etec.sp.gov.br" class="text-light text-decoration-none">
                            arnaldo.hidalgo@etec.sp.gov.br
                        </a>
                    </li>
                    <li>
                        <i class="bi bi-phone"></i> 
                        <a href="tel:+5514998168273" class="text-light text-decoration-none">
                            (14) 99816-8273
                        </a>
                    </li>
                    <li><i class="bi bi-geo-alt"></i><a href="https://devisate.cps.sp.gov.br" target="_blank" class="text-light text-decoration-none"> ETEC Antonio Devisate - Marília, SP</a></li>
                </ul>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p class="mb-0">
                &copy; <?= date('Y') ?> Sistema de Uploads. 
                Todos os direitos reservados à Coordenação do Curso MTec PI Desenvolvimento de Sistemas
            </p>
        </div>
    </div>
</footer>

<!-- Modal de Contato -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Entre em Contato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem</label>
                        <textarea class="form-control" id="message" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="submitContact()">Enviar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function submitContact() {
    // Implementar envio do formulário de contato
    alert('Mensagem enviada com sucesso!');
    $('#contactModal').modal('hide');
}
</script>
</body>
</html> 