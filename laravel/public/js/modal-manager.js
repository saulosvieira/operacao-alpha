/**
 * Gerenciador centralizado de modais
 * Garante o funcionamento consistente de todos os modais da aplicação
 */

document.addEventListener('DOMContentLoaded', function() {
    // Função global para fechar modais de forma consistente
    window.closeCustomModal = function(button) {
        try {
            const modal = button.closest('.modal');
            if (!modal) return;

            // Tenta obter a instância do modal do Bootstrap 5
            const bsModal = bootstrap.Modal.getInstance(modal);
            
            if (bsModal) {
                bsModal.hide();
            } else {
                // Fallback: Cria uma nova instância e fecha
                const newModal = new bootstrap.Modal(modal);
                newModal.hide();
            }
        } catch (error) {
            console.error('Erro ao fechar modal:', error);
            // Fallback final: Esconde o modal diretamente
            if (modal) {
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
    };

    // Configura o fechamento de modais via data-attributes
    document.addEventListener('click', function(e) {
        // Botão de fechar com data-bs-dismiss
        if (e.target.matches('[data-bs-dismiss="modal"]')) {
            e.preventDefault();
            const modal = e.target.closest('.modal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                } else {
                    const newModal = new bootstrap.Modal(modal);
                    newModal.hide();
                }
            }
        }
    });
});
