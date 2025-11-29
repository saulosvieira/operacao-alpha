<!-- Modal de confirmação de exclusão simplificado -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este registro? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <form id="deleteConfirmationForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Verifica se já existe um manipulador de eventos para o modal de exclusão
if (!window.deleteModalInitialized) {
    window.deleteModalInitialized = true;
    
    document.addEventListener('click', function(e) {
        // Verifica se o clique foi em um botão de exclusão
        const deleteButton = e.target.closest('.delete-btn');
        if (!deleteButton) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const itemId = deleteButton.getAttribute('data-id');
        const entity = deleteButton.closest('[data-entity]')?.getAttribute('data-entity');
        
        if (!entity) {
            console.error('Elemento com data-entity não encontrado!');
            return;
        }
        
        // Encontra o formulário de exclusão específico
        const specificForm = document.getElementById(`delete-form-${entity}-${itemId}`);
        const deleteModal = document.getElementById('deleteConfirmationModal');
        const deleteForm = document.getElementById('deleteConfirmationForm');
        
        if (!specificForm || !deleteModal || !deleteForm) {
            console.error('Elementos necessários não encontrados!');
            return;
        }
        
        // Atualiza a ação do formulário
        deleteForm.action = specificForm.action;
        
        // Garante que o token CSRF esteja presente
        const csrfToken = specificForm.querySelector('input[name="_token"]')?.value;
        if (csrfToken) {
            let csrfInput = deleteForm.querySelector('input[name="_token"]');
            if (!csrfInput) {
                csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                deleteForm.appendChild(csrfInput);
            }
            csrfInput.value = csrfToken;
        }
        
        // Cria uma nova instância do modal
        const modal = new bootstrap.Modal(deleteModal);
        
        // Função para limpar o modal quando fechado
        function onModalHidden() {
            // Remove o backdrop manualmente se ainda existir
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                backdrop.remove();
            });
            
            // Remove a classe de modal aberto do body
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
            
            // Remove o estilo inline de padding-right que o Bootstrap adiciona
            document.body.removeAttribute('style');
            
            // Remove o event listener para evitar vazamento de memória
            deleteModal.removeEventListener('hidden.bs.modal', onModalHidden);
        }
        
        // Remove qualquer listener anterior e adiciona o novo
        deleteModal.removeEventListener('hidden.bs.modal', onModalHidden);
        deleteModal.addEventListener('hidden.bs.modal', onModalHidden);
        
        // Mostra o modal
        modal.show();
    });
}
</script>
@endpush
