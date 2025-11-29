@props([
    'id' => 'modalSimple',
    'size' => 'lg',
    'bodyId' => 'modalBody',
    'title' => '',
    'icon' => 'fa-info-circle',
    'footer' => true
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light p-3">
                @if($title || $icon)
                    <h5 class="modal-title d-flex align-items-center" id="{{ $id }}Label">
                        @if($icon)
                            <i class="fas {{ $icon }} me-2"></i>
                        @endif
                        {{ $title }}
                    </h5>
                @endif
                <button type="button" 
                        class="btn-close" 
                        data-bs-dismiss="modal" 
                        aria-label="Fechar">
                </button>
            </div>
            <div class="modal-body p-4" id="{{ $bodyId }}">
                {{ $slot }}
            </div>
            @if($footer)
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Fechar
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
