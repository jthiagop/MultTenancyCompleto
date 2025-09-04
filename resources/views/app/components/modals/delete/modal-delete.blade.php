<div class="modal fade" id="kt_modal_delete_card" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <!-- Cabeçalho -->
          <div class="modal-header">
              <h5 class="modal-title text-danger fw-bold">Confirmar Exclusão</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"
                  aria-label="Close"></button>
          </div>

          <!-- Corpo -->
          <div class="modal-body text-center">
              <i class="bi bi-exclamation-circle-fill text-danger fs-2 mb-4"></i>
              <p class="mb-0 fs-5 fw-semibold text-center">
                  Tem certeza que deseja excluir o registro <strong>#{{ $lp->description }}</strong>?
              </p>
              <small class="text-muted d-block mt-3">
                  Esta ação não pode ser desfeita.
              </small>
          </div>

          <!-- Rodapé -->
          <div class="modal-footer justify-content-center">
              <form id="delete-form" method="POST"
                  action="{{ route('lancamentoPadrao.destroy', $lp->id) }}">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn btn-secondary px-4"
                      data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-danger px-4">
                      <i class="fas fa-trash-alt me-2"></i> Confirmar Exclusão
                  </button>
              </form>
          </div>
      </div>
  </div>